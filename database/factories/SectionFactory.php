<?php

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Genera contenuti dimostrativi senza credenziali reali. */
class SectionFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'title' => fake()->sentence(3),
            'slug' => fake()->unique()->slug(3),
            'content' => fake()->paragraph(),
        ];
    }
}
