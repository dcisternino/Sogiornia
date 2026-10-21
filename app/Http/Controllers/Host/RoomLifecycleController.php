<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/** Gestisce archiviazione reversibile, sospensione guida ed eliminazione della stanza. */
class RoomLifecycleController extends Controller
{
    public function update(Request $request, Property $property, int $room): RedirectResponse
    {
        Gate::authorize('update', $property);
        $room = $property->rooms()->findOrFail($room);
        $data = $request->validate(['action' => ['required', Rule::in(['archive', 'restore', 'suspend', 'resume'])]]);
        match ($data['action']) {
            'archive' => $room->archived_at = now(),
            'restore' => $room->archived_at = null,
            'suspend' => $room->guide_enabled = false,
            'resume' => $room->guide_enabled = true,
        };
        $room->save();

        return back()->with('status', 'Stato della stanza aggiornato.');
    }

    public function destroy(Request $request, Property $property, int $room): RedirectResponse
    {
        Gate::authorize('update', $property);
        $room = $property->rooms()->findOrFail($room);
        $request->validate(['confirmation' => ['required', 'string', Rule::in([$room->name])]]);
        $room->delete();

        return redirect()->route('host.properties.show', $property)->with('status', 'Stanza eliminata con i relativi soggiorni.');
    }
}
