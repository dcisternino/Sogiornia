<?php

namespace Database\Factories;

use App\Models\TranslationSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<TranslationSetting> */
class TranslationSettingFactory extends Factory
{
    public function definition(): array
    {
        return ['is_enabled' => true, 'monthly_character_limit' => 450000, 'default_host_character_limit' => 30000];
    }
}
