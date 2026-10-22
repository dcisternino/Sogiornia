<?php

namespace Database\Factories;

use App\Models\SectionBlock;
use App\Models\SectionBlockTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SectionBlockTranslation> */
class SectionBlockTranslationFactory extends Factory
{
    public function definition(): array
    {
        return ['section_block_id' => SectionBlock::factory(), 'locale' => 'en', 'title' => fake()->sentence(3), 'content' => fake()->paragraph()];
    }
}
