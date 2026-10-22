<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Http\Requests\Host\BookingRequest;
use App\Models\Property;
use App\Models\Room;
use App\Services\StayScheduler;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Permette di creare un soggiorno dalla dashboard scegliendo struttura e stanza. */
class BookingController extends Controller
{
    public function create(Request $request): View
    {
        $data = $request->validate([
            'property_id' => ['nullable', 'integer', Rule::exists(Property::class, 'id')->where('user_id', $request->user()->id)->whereNull('archived_at')],
            'room_id' => ['nullable', 'integer', Rule::exists(Room::class, 'id')->where(function (\Illuminate\Database\Query\Builder $query) use ($request): void {
                $query->whereIn('property_id', $request->user()->properties()->whereNull('archived_at')->select('id'))->whereNull('archived_at');
                if ($request->filled('property_id')) {
                    $query->where('property_id', $request->integer('property_id'));
                }
            })],
            'arrival_date' => ['nullable', 'date_format:Y-m-d'],
            'departure_date' => ['nullable', 'date_format:Y-m-d', Rule::when($request->filled('arrival_date'), ['after:arrival_date'])],
        ]);
        $arrival = isset($data['arrival_date']) ? CarbonImmutable::createFromFormat('!Y-m-d', $data['arrival_date'], 'Europe/Rome') : CarbonImmutable::now('Europe/Rome');
        $properties = $request->user()->properties()->whereNull('archived_at')->whereHas('rooms', fn (Builder $query) => $query->whereNull('archived_at'));
        if (! empty($data['property_id'])) {
            $properties->whereKey($data['property_id']);
        }

        return view('pages.host.stays.booking', [
            'properties' => $properties->with(['rooms' => fn (HasMany $query) => $query->whereNull('archived_at')->orderBy('name')])->orderBy('name')->get(),
            'defaults' => ['room_id' => $data['room_id'] ?? null, 'arrival_date' => $arrival->format('Y-m-d'), 'departure_date' => $data['departure_date'] ?? $arrival->addDay()->format('Y-m-d')],
        ]);
    }

    public function store(BookingRequest $request, StayScheduler $scheduler): RedirectResponse
    {
        $room = Room::query()->whereNull('archived_at')->whereHas('property', fn (Builder $query) => $query->whereBelongsTo($request->user())->whereNull('archived_at'))
            ->findOrFail($request->validated('room_id'));
        $scheduler->save($room, $request->stayData(), $request->boolean('overlap_confirmed'));

        return redirect()->route('host.stays.index', [$room->property_id, $room])->with('status', 'Soggiorno creato: il link è disponibile nella scheda della stanza.');
    }
}
