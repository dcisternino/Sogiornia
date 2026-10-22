<?php

namespace Database\Factories;

use App\Models\GuideWorkspace;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<GuideWorkspace> */
class GuideWorkspaceFactory extends Factory
{
    public function definition(): array
    {
        return ['property_id' => Property::factory(), 'revision' => 0, 'lock_sequence' => 0, 'needs_initial_version' => false];
    }
}
