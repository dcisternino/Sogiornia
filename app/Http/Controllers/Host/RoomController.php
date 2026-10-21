<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Http\Requests\Host\RoomRequest;
use App\Models\Property;
use App\Models\Room;
use App\Services\RoomImageStorage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

/** Gestisce le stanze e salva in modo atomico le sezioni abilitate. */
class RoomController extends Controller
{
    public function create(Property $property): View
    {
        Gate::authorize('update', $property);

        return view('pages.host.rooms.form', [
            'property' => $property,
            'room' => new Room,
            'sections' => $property->sections()->orderBy('title')->orderBy('id')->get(),
            'selectedSections' => [],
        ]);
    }

    public function store(RoomRequest $request, Property $property, RoomImageStorage $images): RedirectResponse
    {
        $room = $property->rooms()->make($request->safe()->only(['name', 'description']));
        $images->save($room, $request->file('image'), false, (array) $request->validated('sections', []));

        return redirect()->route('host.properties.show', $property)->with('status', 'Stanza creata. Crea un soggiorno per generare il link ospite.');
    }

    public function edit(Property $property, int $room): View
    {
        Gate::authorize('update', $property);
        $room = $property->rooms()->with(['sections', 'photo'])->findOrFail($room);

        return view('pages.host.rooms.form', [
            'property' => $property,
            'room' => $room,
            'sections' => $property->sections()->orderBy('title')->orderBy('id')->get(),
            'selectedSections' => $room->sections->modelKeys(),
        ]);
    }

    public function update(RoomRequest $request, Property $property, int $room, RoomImageStorage $images): RedirectResponse
    {
        $room = $property->rooms()->findOrFail($room);

        $room->fill($request->safe()->only(['name', 'description']));
        $images->save($room, $request->file('image'), $request->boolean('remove_image'), (array) $request->validated('sections', []));

        return redirect()->route('host.properties.show', $property)->with('status', 'Stanza e sezioni visibili aggiornate.');
    }
}
