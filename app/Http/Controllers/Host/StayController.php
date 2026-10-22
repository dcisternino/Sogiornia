<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Http\Requests\Host\StayRequest;
use App\Models\Property;
use App\Services\StayScheduler;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

/** Crea link distinti per soggiorno e mostra lo storico della stanza. */
class StayController extends Controller
{
    public function index(Property $property, int $room): View
    {
        Gate::authorize('view', $property);
        $room = $property->rooms()->findOrFail($room);

        return view('pages.host.stays.index', [
            'property' => $property,
            'room' => $room,
            'stays' => $room->stays()->with('room.property')->orderByDesc('starts_at')->orderByDesc('id')->paginate(15),
        ]);
    }

    public function create(Property $property, int $room): View
    {
        Gate::authorize('update', $property);
        $room = $property->rooms()->findOrFail($room);
        abort_if($property->archived_at !== null || $room->archived_at !== null, 422, 'Ripristina struttura e stanza prima di creare un soggiorno.');

        return view('pages.host.stays.create', compact('property', 'room'));
    }

    public function store(StayRequest $request, Property $property, int $room, StayScheduler $scheduler): RedirectResponse
    {
        $room = $property->rooms()->findOrFail($room);
        abort_if($property->archived_at !== null || $room->archived_at !== null, 422, 'Ripristina struttura e stanza prima di creare un soggiorno.');
        $scheduler->save($room, $request->stayData(), $request->boolean('overlap_confirmed'));

        return redirect()->route('host.stays.index', [$property, $room])->with('status', 'Soggiorno creato. Il link sarà valido dall’arrivo alla partenza alle 10:00.');
    }

    /** Modifica le date senza rigenerare il link e senza riattivare un accesso revocato. */
    public function edit(Property $property, int $room, int $stay): View
    {
        Gate::authorize('update', $property);
        $room = $property->rooms()->findOrFail($room);
        abort_if($property->archived_at || $room->archived_at, 422, 'Ripristina struttura e stanza prima di modificare un soggiorno.');
        $stay = $room->stays()->findOrFail($stay);

        return view('pages.host.stays.edit', compact('property', 'room', 'stay'));
    }

    public function update(StayRequest $request, Property $property, int $room, int $stay, StayScheduler $scheduler): RedirectResponse
    {
        $room = $property->rooms()->findOrFail($room);
        abort_if($property->archived_at || $room->archived_at, 422, 'Ripristina struttura e stanza prima di modificare un soggiorno.');
        $stay = $room->stays()->findOrFail($stay);
        $scheduler->save($room, $request->stayData(), $request->boolean('overlap_confirmed'), $stay);

        return redirect()->route('host.stays.index', [$property, $room])->with('status', 'Soggiorno aggiornato. Il link resta lo stesso e segue subito le nuove date; eventuali revoche restano valide.');
    }
}
