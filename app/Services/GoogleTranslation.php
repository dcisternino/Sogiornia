<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

/** Chiede a Google una bozza in testo semplice: nessun contenuto viene pubblicato o salvato. */
class GoogleTranslation
{
    public function __construct(private TranslationBudget $budget) {}

    /** @param array<string, string> $fields
     * @return array<string, string>
     */
    public function suggest(User $host, array $fields, string $locale, string $type): array
    {
        $key = config('services.google_translation.key');
        if (blank($key)) {
            throw new HttpException(503, 'Traduzione automatica non configurata. Chiedi all’amministratore di impostare la chiave Google Cloud. Puoi continuare a tradurre manualmente.');
        }
        $characters = array_sum(array_map(fn (string $text): int => mb_strlen($text, 'UTF-8'), $fields));
        $usage = $this->budget->reserve($host, $characters, $locale, $type);
        $started = hrtime(true);
        $providerStatus = null;
        try {
            $result = $this->translate($fields, $locale, $key, $providerStatus);
        } catch (Throwable $exception) {
            $uncertain = $providerStatus === null || $providerStatus >= 500;
            $this->budget->finish($usage, $uncertain ? 'uncertain' : 'failed', (int) ((hrtime(true) - $started) / 1000000), $providerStatus, $providerStatus === null ? 'connection_or_interruption' : ($providerStatus === 200 ? 'invalid_result' : 'provider_rejected'));
            throw $exception;
        }
        $this->budget->finish($usage, 'success', (int) ((hrtime(true) - $started) / 1000000), $providerStatus, null);

        return $result;
    }

    /** @param array<string, string> $fields
     * @return array<string, string>
     */
    private function translate(array $fields, string $locale, string $key, ?int &$providerStatus): array
    {
        try {
            $response = Http::acceptJson()->asJson()->withHeaders(['X-Goog-Api-Key' => $key])
                ->withoutRedirecting()->connectTimeout(5)->timeout(25)
                ->post('https://translation.googleapis.com/language/translate/v2', [
                    'q' => array_values($fields), 'source' => 'it', 'target' => $locale, 'format' => 'text',
                ]);
        } catch (ConnectionException) {
            throw new HttpException(503, 'Google non risponde. I testi nel modulo sono rimasti invariati: riprova tra poco.');
        }
        $providerStatus = $response->status();
        if (! $response->successful()) {
            $message = match ($response->status()) {
                400, 401, 403 => 'Google non ha autorizzato la traduzione. Chiedi all’amministratore di verificare chiave, API, fatturazione e quote.',
                429 => 'Limite di traduzione Google raggiunto. Riprova più tardi oppure inserisci la traduzione manualmente.',
                default => 'Il servizio di traduzione non è disponibile. I testi nel modulo sono rimasti invariati.',
            };
            throw new HttpException(503, $message);
        }
        $translations = $response->json('data.translations');
        if (! is_array($translations) || count($translations) !== count($fields)) {
            throw new HttpException(502, 'Google ha restituito una risposta incompleta. Nessuna traduzione è stata applicata.');
        }
        $result = [];
        foreach (array_keys($fields) as $index => $field) {
            $text = $translations[$index]['translatedText'] ?? null;
            if (! is_string($text) || blank($text)) {
                throw new HttpException(502, 'Google ha restituito una traduzione incompleta. Riprova.');
            }
            $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $limit = match ($field) {
                'content' => 20000, 'welcome_message' => 3000, default => 255
            };
            if (mb_strlen($text) > $limit) {
                throw new HttpException(422, 'Una traduzione supera la lunghezza consentita. Accorcia il testo italiano o traduci il campo manualmente.');
            }
            $result[$field] = $text;
        }

        return $result;
    }
}
