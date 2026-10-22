<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Http\Requests\Host\PropertyRequest;
use App\Models\Property;
use App\Models\Room;
use App\Models\Section;
use App\Services\PropertyProfileStorage;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/** Gestisce le strutture dell'host senza esporre quelle di altri utenti. */
class PropertyController extends Controller
{
    public function index(Request $request): View
    {
        return view('pages.host.properties.index', [
            'properties' => $request->user()->properties()->with('profile')->withCount(['rooms', 'sections'])->orderByDesc('id')->paginate(12),
            'activeProperties' => $request->user()->properties()->whereNull('archived_at')->count(),
            'roomCount' => Room::query()->whereHas('property', fn (Builder $query) => $query->whereBelongsTo($request->user()))->count(),
            'sectionCount' => Section::query()->whereHas('property', fn (Builder $query) => $query->whereBelongsTo($request->user()))->count(),
        ]);
    }

    public function create(): View
    {
        return view('pages.host.properties.form', ['property' => new Property]);
    }

    public function store(PropertyRequest $request, PropertyProfileStorage $profiles): RedirectResponse
    {
        $property = $request->user()->properties()->make($request->safe()->only(['name', 'city', 'address', 'welcome_message']));
        $profiles->save($property, $request->validated('description'), $request->file('image'), false, $request->validated('translations', []));

        return redirect()->route('host.properties.index')->with('status', 'Struttura creata. Ora puoi gestire stanze e sezioni dai pulsanti della struttura.');
    }

    public function show(Property $property): RedirectResponse
    {
        Gate::authorize('view', $property);

        return redirect()->route('host.rooms.index', $property);
    }

    public function edit(Property $property): View
    {
        Gate::authorize('update', $property);
        $property->load('translations');

        return view('pages.host.properties.form', compact('property'));
    }

    public function update(PropertyRequest $request, Property $property, PropertyProfileStorage $profiles): RedirectResponse
    {
        $property->fill($request->safe()->only(['name', 'city', 'address', 'welcome_message']));
        $profiles->save($property, $request->validated('description'), $request->file('image'), $request->boolean('remove_image'), $request->validated('translations', []));

        return redirect()->route('host.properties.index')->with('status', 'Bozza della struttura salvata. Rivedi e pubblica la guida per aggiornare gli ospiti.');
    }
}
