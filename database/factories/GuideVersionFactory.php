<?php

namespace Database\Factories;

use App\Models\GuideVersion;
use App\Models\Property;
use App\Services\GuideSnapshot;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<GuideVersion> */
class GuideVersionFactory extends Factory
{
    public function definition(): array
    {
        $payload = ['schema' => 1, 'property' => ['name' => fake()->company(), 'city' => fake()->city(), 'address' => null, 'welcome_message' => null, 'description' => null, 'image_path' => null, 'translations' => []], 'rooms' => [], 'sections' => []];

        return ['property_id' => Property::factory(), 'number' => 1, 'payload' => $payload, 'checksum' => app(GuideSnapshot::class)->checksum($payload), 'published_at' => now(), 'published_by' => null];
    }
}
