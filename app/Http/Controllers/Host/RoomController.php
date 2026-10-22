<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Http\Requests\Host\RoomRequest;
use App\Models\Property;
use App\Models\Room;
use App\Services\OnboardingNavigation;
use App\Services\RoomImageStorage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

/** Gestisce le stanze e salva in modo atomico le sezioni abilitate. */
class RoomController extends Controller
{
    public function __construct(private OnboardingNavigation $navigation) {}

    /** Elenca esclusivamente le stanze della struttura selezionata. */
    public function index(Property $property): View
    {
        Gate::authorize('view', $property);

        return view('pages.host.rooms.index', [
            'property' => $property,
            'rooms' => $property->rooms()->with('photo')->orderBy('name')->orderBy('id')->paginate(12)->withQueryString(),
        ]);
    }

    public function create(Property $property): View
    {
        Gate::authorize('update', $property);

        return view('pages.host.rooms.form', [
            'property' => $property,
            'room' => new Room,
            'sections' => $property->sections()->with('settings')->orderBy('title')->orderBy('id')->get(),
            'selectedSections' => [],
        ]);
    }

    public function store(RoomRequest $request, Property $property, RoomImageStorage $images): RedirectResponse
    {
        $room = $property->rooms()->make($request->safe()->only(['name', 'description']));
        $images->save($room, $request->file('image'), false, (array) $request->validated('sections', []));

        return ($this->navigation->active() ? redirect($this->navigation->setupUrl($property, $room)) : redirect()->route('host.rooms.index', $property))->with('status', 'Stanza creata. Crea un soggiorno per generare il link ospite.');
    }

    public function edit(Property $property, int $room): View
    {
        Gate::authorize('update', $property);
        $room = $property->rooms()->with(['sections', 'photo'])->findOrFail($room);

        return view('pages.host.rooms.form', [
            'property' => $property,
            'room' => $room,
            'sections' => $property->sections()->with('settings')->orderBy('title')->orderBy('id')->get(),
            'selectedSections' => $room->sections->modelKeys(),
        ]);
    }

    public function update(RoomRequest $request, Property $property, int $room, RoomImageStorage $images): RedirectResponse
    {
        $room = $property->rooms()->findOrFail($room);

        $room->fill($request->safe()->only(['name', 'description']));
        $images->save($room, $request->file('image'), $request->boolean('remove_image'), (array) $request->validated('sections', []));

        return ($this->navigation->active() ? redirect($this->navigation->setupUrl($property, $room)) : redirect()->route('host.rooms.index', $property))->with('status', 'Bozza della stanza salvata. Pubblica la guida per aggiornare foto e sezioni visibili.');
    }
}
