<?php

namespace Database\Factories;

use App\Models\TranslationUsage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<TranslationUsage> */
class TranslationUsageFactory extends Factory
{
    public function definition(): array
    {
        return ['user_id' => User::factory(), 'period' => now('Europe/Rome')->startOfMonth()->format('Y-m-d'), 'resource_type' => 'block', 'locale' => 'en', 'characters' => 1000, 'budget_characters' => 1000, 'status' => 'success', 'provider_status' => 200, 'duration_ms' => 100];
    }
}
