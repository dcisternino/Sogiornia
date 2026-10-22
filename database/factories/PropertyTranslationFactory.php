<?php

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Genera benvenuti tradotti senza modificare il testo italiano. */
class PropertyTranslationFactory extends Factory
{
    public function definition(): array
    {
        return ['property_id' => Property::factory(), 'locale' => 'en', 'welcome_message' => 'Welcome to our property.'];
    }
}
