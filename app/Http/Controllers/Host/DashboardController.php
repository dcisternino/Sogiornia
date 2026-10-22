<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Http\Requests\Host\StayFilterRequest;
use App\Models\Stay;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

/** Riepiloga i soggiorni dell'host con filtri combinabili per stanza, struttura e date. */
class DashboardController extends Controller
{
    public function __invoke(StayFilterRequest $request): View
    {
        $data = $request->validated();
        $period = $data['period'] ?? 'active';
        $filters = array_diff_key($data, ['period' => true]);
        $stays = Stay::query()->whereHas('room.property', fn (Builder $query) => $query->whereBelongsTo($request->user()));
        if (! empty($filters['property_id'])) {
            $stays->whereHas('room', fn (Builder $query) => $query->where('property_id', $filters['property_id']));
        }
        if (! empty($filters['room_id'])) {
            $stays->where('room_id', $filters['room_id']);
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

        return view('pages.host.home', [
            'period' => $period, 'filters' => $filters, 'counts' => $counts,
            'properties' => $request->user()->properties()->with('rooms')->orderBy('name')->orderBy('id')->get(),
            'stays' => $stays->with('room.property')->orderBy('starts_at', $period === 'past' ? 'desc' : 'asc')->orderBy('id')->paginate(15)->withQueryString(),
        ]);
    }
}
