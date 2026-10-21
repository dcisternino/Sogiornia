<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Http\Requests\Host\StayRequest;
use App\Models\Property;
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

    public function store(StayRequest $request, Property $property, int $room): RedirectResponse
    {
        $room = $property->rooms()->findOrFail($room);
        abort_if($property->archived_at !== null || $room->archived_at !== null, 422, 'Ripristina struttura e stanza prima di creare un soggiorno.');
        $room->stays()->create($request->stayData());

        return redirect()->route('host.stays.index', [$property, $room])->with('status', 'Soggiorno creato. Il link sarà valido dall’arrivo alla partenza alle 10:00.');
    }
}
