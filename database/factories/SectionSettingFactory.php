<?php

namespace Database\Factories;

use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Genera impostazioni compatibili con le sezioni esistenti. */
class SectionSettingFactory extends Factory
{
    public function definition(): array
    {
        return ['section_id' => Section::factory(), 'is_active' => true, 'is_shared' => false];
    }
}
