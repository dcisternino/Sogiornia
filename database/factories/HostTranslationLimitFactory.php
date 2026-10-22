<?php

namespace Database\Factories;

use App\Models\HostTranslationLimit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<HostTranslationLimit> */
class HostTranslationLimitFactory extends Factory
{
    public function definition(): array
    {
        return ['user_id' => User::factory(), 'is_enabled' => true, 'uses_default_limit' => true, 'monthly_character_limit' => null];
    }
}
