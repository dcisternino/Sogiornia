<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Genera i metadati della copertina; il file viene gestito separatamente. */
class RoomPhotoFactory extends Factory
{
    public function definition(): array
    {
        return ['room_id' => Room::factory(), 'path' => 'room-images/'.fake()->uuid().'.jpg'];
    }
}
