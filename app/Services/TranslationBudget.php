<?php

namespace App\Services;

use App\Models\HostTranslationLimit;
use App\Models\TranslationSetting;
use App\Models\TranslationUsage;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

/** Riserva il budget prima della chiamata esterna e serializza controlli e modifiche amministrative. */
class TranslationBudget
{
    public function currentPeriod(): string
    {
        return CarbonImmutable::now(config('translation.timezone'))->startOfMonth()->format('Y-m-d');
    }

    /** La prima operazione acquisisce una scrittura anche su SQLite, prima di leggere i consumi. */
    public function lockSettings(): TranslationSetting
    {
        TranslationSetting::query()->whereKey(1)->increment('reservation_sequence');

        return TranslationSetting::query()->lockForUpdate()->findOrFail(1);
    }

    public function reserve(User $host, int $characters, string $locale, string $type): TranslationUsage
    {
        $usage = DB::transaction(function () use ($host, $characters, $locale, $type): TranslationUsage {
            $settings = $this->lockSettings();
            $host = $host->fresh(['hostAccess']);
            abort_unless($host?->isHost() && $host->canWrite(), 403, 'Account in sola lettura: le traduzioni automatiche non sono disponibili.');
            $period = $this->currentPeriod();
            $hostLimit = HostTranslationLimit::query()->whereBelongsTo($host)->first();
            $limit = $hostLimit && ! $hostLimit->uses_default_limit ? $hostLimit->monthly_character_limit : $settings->default_host_character_limit;
            $globalUsed = (int) TranslationUsage::query()->where('period', $period)->sum('budget_characters');
            $hostUsed = (int) $host->translationUsages()->where('period', $period)->sum('budget_characters');
            $reason = match (true) {
                ! $settings->is_enabled => 'global_disabled',
                $hostLimit && ! $hostLimit->is_enabled => 'host_disabled',
                $settings->monthly_character_limit !== null && $globalUsed + $characters > $settings->monthly_character_limit => 'global_limit',
                $limit !== null && $hostUsed + $characters > $limit => 'host_limit',
                default => null,
            };

            return $host->translationUsages()->create([
                'period' => $period, 'locale' => $locale, 'resource_type' => $type,
                'characters' => $characters, 'budget_characters' => $reason ? 0 : $characters,
                'status' => $reason ? 'blocked' : 'reserved', 'error_code' => $reason,
            ]);
        }, 3);
        if ($usage->status === 'blocked') {
            $message = match ($usage->error_code) {
                'global_disabled' => 'Le traduzioni automatiche sono temporaneamente sospese dall’amministratore. Puoi inserire le traduzioni manualmente.',
                'host_disabled' => 'Le traduzioni automatiche del tuo account sono sospese. Contatta l’amministratore; le guide restano disponibili.',
                'global_limit' => 'Il budget mensile di traduzione di Sogiornia non è sufficiente per questi testi. Contatta l’amministratore oppure attendi il prossimo mese.',
                default => 'Il budget mensile di traduzione del tuo account non è sufficiente per questi testi. Riduci il testo, contatta l’amministratore oppure attendi il prossimo mese.',
            };
            throw new HttpException(422, $message);
        }

        return $usage;
    }

    public function finish(TranslationUsage $usage, string $status, int $duration, ?int $providerStatus, ?string $error): void
    {
        TranslationUsage::query()->whereKey($usage->id)->where('status', 'reserved')->update([
            'status' => $status, 'duration_ms' => $duration, 'provider_status' => $providerStatus, 'error_code' => $error,
        ]);
    }
}
