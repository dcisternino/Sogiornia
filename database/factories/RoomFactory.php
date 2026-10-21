<?php

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Genera stanze dimostrative a cui potranno essere associati più soggiorni. */
class RoomFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'name' => 'Stanza '.fake()->unique()->numerify('####'),
            'description' => fake()->sentence(),
        ];
    }
}
