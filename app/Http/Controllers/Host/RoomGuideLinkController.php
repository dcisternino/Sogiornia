<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

/** Revoca il link ospite precedente generando un nuovo token per la stanza. */
class RoomGuideLinkController extends Controller
{
    public function update(Property $property, int $room): RedirectResponse
    {
        Gate::authorize('update', $property);
        $room = $property->rooms()->findOrFail($room);
        $room->guide_token = Str::random(64);
        $room->save();

        return back()->with('status', 'Nuovo link generato. Il precedente non è più valido.');
    }
}
