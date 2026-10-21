<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Genera scadenze facoltative per gli account host. */
class HostAccessFactory extends Factory
{
    public function definition(): array
    {
        return ['user_id' => User::factory()->state(['role' => User::ROLE_HOST]), 'expires_at' => null];
    }
}
