<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Http\Requests\Host\PropertyRequest;
use App\Models\Property;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/** Gestisce le strutture dell'host senza esporre quelle di altri utenti. */
class PropertyController extends Controller
{
    public function index(Request $request): View
    {
        return view('pages.host.properties.index', [
            'properties' => $request->user()->properties()->withCount(['rooms', 'sections'])->orderByDesc('id')->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('pages.host.properties.form', ['property' => new Property]);
    }

    public function store(PropertyRequest $request): RedirectResponse
    {
        $property = $request->user()->properties()->create($request->validated());

        return redirect()->route('host.properties.show', $property)->with('status', 'Struttura creata. Aggiungi le sezioni e poi le stanze.');
    }

    public function show(Property $property): View
    {
        Gate::authorize('view', $property);

        return view('pages.host.properties.show', [
            'property' => $property,
            'rooms' => $property->rooms()->withCount('sections')->orderBy('name')->orderBy('id')->paginate(12, ['*'], 'rooms_page'),
            'sections' => $property->sections()->orderBy('title')->orderBy('id')->paginate(15, ['*'], 'sections_page'),
        ]);
    }

    public function edit(Property $property): View
    {
        Gate::authorize('update', $property);

        return view('pages.host.properties.form', compact('property'));
    }

    public function update(PropertyRequest $request, Property $property): RedirectResponse
    {
        $property->update($request->validated());

        return redirect()->route('host.properties.show', $property)->with('status', 'Struttura aggiornata.');
    }
}
