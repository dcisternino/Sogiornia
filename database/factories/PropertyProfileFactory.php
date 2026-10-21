<?php

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Genera una breve presentazione della struttura senza file fittizi su disco. */
class PropertyProfileFactory extends Factory
{
    public function definition(): array
    {
        return ['property_id' => Property::factory(), 'description' => fake()->sentence(), 'image_path' => null];
    }
}
