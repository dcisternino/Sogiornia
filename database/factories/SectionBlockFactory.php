<?php

namespace Database\Factories;

use App\Models\Section;
use App\Models\SectionBlock;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SectionBlock> */
class SectionBlockFactory extends Factory
{
    public function definition(): array
    {
        return ['section_id' => Section::factory(), 'type' => 'text', 'title' => fake()->sentence(3), 'content' => fake()->paragraph(), 'is_active' => true, 'position' => 0];
    }
}
