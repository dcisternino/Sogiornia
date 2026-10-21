<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Genera soggiorni dimostrativi senza dati personali degli ospiti. */
class StayFactory extends Factory
{
    public function definition(): array
    {
        return [
            'room_id' => Room::factory(),
            'reference' => 'Soggiorno '.fake()->unique()->numerify('#####'),
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addDay(),
        ];
    }
}
