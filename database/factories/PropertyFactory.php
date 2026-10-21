<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Genera strutture dimostrative collegate a un host. */
class PropertyFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => 'Casa '.fake()->lastName(),
            'city' => fake()->city(),
            'address' => fake()->streetAddress(),
            'welcome_message' => 'Benvenuto! Qui trovi le informazioni utili per il tuo soggiorno.',
        ];
    }
}
