<?php

namespace Database\Seeders;

use App\Models\TranslationSetting;
use Illuminate\Database\Seeder;

/** Ripristina solo la configurazione iniziale mancante, senza generare consumi o sovrascrivere limiti. */
class TranslationMonitoringSeeder extends Seeder
{
    public function run(): void
    {
        if (! TranslationSetting::query()->whereKey(1)->exists()) {
            $settings = new TranslationSetting(['is_enabled' => true, 'monthly_character_limit' => 450000, 'default_host_character_limit' => 30000]);
            $settings->id = 1;
            $settings->save();
        }
    }
}
