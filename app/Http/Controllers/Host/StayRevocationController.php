<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

/** Revoca in modo definitivo il link di un singolo soggiorno. */
class StayRevocationController extends Controller
{
    public function update(Property $property, int $room, int $stay): RedirectResponse
    {
        Gate::authorize('update', $property);
        $stay = $property->rooms()->findOrFail($room)->stays()->findOrFail($stay);
        $stay->revoked_at = $stay->revoked_at ?? now();
        $stay->save();

        return back()->with('status', 'Accesso al soggiorno revocato.');
    }
}
