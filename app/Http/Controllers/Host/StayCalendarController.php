<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Http\Requests\Host\StayFilterRequest;
use App\Models\Room;
use App\Models\Stay;
use App\Services\StayCalendar;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

/** Riunisce calendario mensile, indicatori e tabella filtrabile dei soggiorni dell'host. */
class StayCalendarController extends Controller
{
    public function index(StayFilterRequest $request, StayCalendar $calendar): View
    {
        $data = $request->validated();
        $period = $data['period'] ?? 'active';
        $month = $data['month'] ?? now('Europe/Rome')->format('Y-m');
        $filters = array_diff_key($data, ['period' => true, 'month' => true]);
        $stays = Stay::query()->whereHas('room.property', fn (Builder $query) => $query->whereBelongsTo($request->user()));
        if (! empty($filters['property_id'])) {
            $stays->whereHas('room', fn (Builder $query) => $query->where('property_id', $filters['property_id']));
        }
        if (! empty($filters['room_id'])) {
            $stays->where('room_id', $filters['room_id']);
        }
        $calendarQuery = clone $stays;
        $bookableRooms = Room::query()->whereNull('archived_at')->whereHas('property', fn (Builder $query) => $query->whereBelongsTo($request->user())->whereNull('archived_at'));
        if (! empty($filters['property_id'])) {
            $bookableRooms->where('property_id', $filters['property_id']);
        }
        if (! empty($filters['room_id'])) {
            $bookableRooms->whereKey($filters['room_id']);
        }
        if (! empty($filters['from_date'])) {
            $stays->where('ends_at', '>', CarbonImmutable::createFromFormat('!Y-m-d', $filters['from_date'], 'Europe/Rome')->utc());
        }
        if (! empty($filters['to_date'])) {
            $stays->where('starts_at', '<', CarbonImmutable::createFromFormat('!Y-m-d', $filters['to_date'], 'Europe/Rome')->addDay()->utc());
        }
        $counts = collect(['active', 'scheduled', 'past'])->mapWithKeys(fn (string $period): array => [$period => (clone $stays)->inPeriod($period)->count()]);
        if ($period !== 'all') {
            $stays->inPeriod($period);
        }

        return view('pages.host.stays.calendar', [
            'period' => $period, 'month' => $month, 'filters' => $filters, 'counts' => $counts,
            'canCreate' => $request->user()->canWrite() && $bookableRooms->exists(),
            'calendar' => $calendar->forMonth($calendarQuery, $month),
            'properties' => $request->user()->properties()->with('rooms')->orderBy('name')->orderBy('id')->get(),
            'stays' => $stays->with('room.property')->orderBy('starts_at', $period === 'past' ? 'desc' : 'asc')->orderBy('id')->paginate(15)->withQueryString(),
        ]);
    }
}
