<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;

/** Impedisce a un host di leggere o modificare strutture altrui. */
class PropertyPolicy
{
    public function view(User $user, Property $property): bool
    {
        return $user->isHost() && $property->user_id === $user->id;
    }

    public function update(User $user, Property $property): bool
    {
        return $user->canWrite() && $this->view($user, $property);
    }
}
