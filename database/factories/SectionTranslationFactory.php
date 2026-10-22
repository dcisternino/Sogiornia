<?php

namespace Database\Factories;

use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Genera testi tradotti collegati alla sezione originale. */
class SectionTranslationFactory extends Factory
{
    public function definition(): array
    {
        return ['section_id' => Section::factory(), 'locale' => 'en', 'title' => 'Useful information', 'content' => 'Everything you need for your stay.'];
    }
}
