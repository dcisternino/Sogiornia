<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Http\Requests\Host\BookingRequest;
use App\Models\Room;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/** Permette di creare un soggiorno dalla dashboard scegliendo struttura e stanza. */
class BookingController extends Controller
{
    public function create(Request $request): View
    {
        return view('pages.host.stays.booking', [
            'properties' => $request->user()->properties()->whereNull('archived_at')->whereHas('rooms', fn (Builder $query) => $query->whereNull('archived_at'))
                ->with(['rooms' => fn (HasMany $query) => $query->whereNull('archived_at')->orderBy('name')])->orderBy('name')->get(),
        ]);
    }

    public function store(BookingRequest $request): RedirectResponse
    {
        $room = Room::query()->whereNull('archived_at')->whereHas('property', fn (Builder $query) => $query->whereBelongsTo($request->user())->whereNull('archived_at'))
            ->findOrFail($request->validated('room_id'));
        $room->stays()->create($request->stayData());

        return redirect()->route('host.stays.index', [$room->property_id, $room])->with('status', 'Soggiorno creato: il link è disponibile nella scheda della stanza.');
    }
}
