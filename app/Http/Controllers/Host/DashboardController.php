<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Stay;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Riepiloga i soggiorni esclusivamente nelle strutture dell'host autenticato. */
class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $data = $request->validate(['period' => ['nullable', Rule::in(['active', 'scheduled', 'past'])]]);
        $period = $data['period'] ?? 'active';
        $stays = Stay::query()->whereHas('room.property', fn (Builder $query) => $query->whereBelongsTo($request->user()));

        return view('pages.host.home', [
            'period' => $period,
            'counts' => collect(['active', 'scheduled', 'past'])->mapWithKeys(fn (string $period) => [$period => (clone $stays)->inPeriod($period)->count()]),
            'stays' => $stays->inPeriod($period)->with('room.property')->orderBy('starts_at', $period === 'past' ? 'desc' : 'asc')->orderBy('id')->paginate(15)->withQueryString(),
        ]);
    }
}
