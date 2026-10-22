<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

/** Chiede a Google una bozza in testo semplice: nessun contenuto viene pubblicato o salvato. */
class GoogleTranslation
{
    /** @param array<string, string> $fields
     * @return array<string, string>
     */
    public function suggest(array $fields, string $locale): array
    {
        $key = config('services.google_translation.key');
        if (blank($key)) {
            throw new HttpException(503, 'Traduzione automatica non configurata. Chiedi all’amministratore di impostare la chiave Google Cloud. Puoi continuare a tradurre manualmente.');
        }
        try {
            $response = Http::acceptJson()->asJson()->withHeaders(['X-Goog-Api-Key' => $key])
                ->withoutRedirecting()->connectTimeout(5)->timeout(25)
                ->post('https://translation.googleapis.com/language/translate/v2', [
                    'q' => array_values($fields), 'source' => 'it', 'target' => $locale, 'format' => 'text',
                ]);
        } catch (ConnectionException) {
            throw new HttpException(503, 'Google non risponde. I testi nel modulo sono rimasti invariati: riprova tra poco.');
        }
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
