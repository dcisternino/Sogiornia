<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
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

        $message = match ($data['action']) {
            'archive' => 'Stanza archiviata: le guide non sono accessibili. Puoi ripristinarla in qualsiasi momento.',
            'restore' => 'Stanza ripristinata. Le guide tornano accessibili se abilitate e con soggiorno valido.',
            'suspend' => 'Guida della stanza sospesa. I contenuti sono conservati e puoi riattivarla quando vuoi.',
            'resume' => 'Guida della stanza riattivata. Verifica che anche la struttura sia attiva.',
        };

        return back()->with('status', $message);
    }

    public function destroy(Request $request, Property $property, int $room): RedirectResponse
    {
        Gate::authorize('update', $property);
        $room = $property->rooms()->findOrFail($room);
        $request->validate(['confirmation' => ['required', 'string', Rule::in([$room->name])]]);
        $path = $room->photo?->path;
        $room->delete();
        if ($path) {
            Storage::disk('local')->delete($path);
        }

        return redirect()->route('host.rooms.index', $property)->with('status', 'Stanza eliminata con i relativi soggiorni.');
    }
}
