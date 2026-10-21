<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Http\Requests\Host\SectionRequest;
use App\Models\Property;
use App\Models\Section;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

/** Gestisce contenuti testuali riutilizzabili nelle guide della struttura. */
class SectionController extends Controller
{
    public function create(Property $property): View
    {
        Gate::authorize('update', $property);

        return view('pages.host.sections.form', ['property' => $property, 'section' => new Section]);
    }

    public function store(SectionRequest $request, Property $property): RedirectResponse
    {
        $property->sections()->create($request->validated());

        return redirect()->route('host.properties.show', $property)->with('status', 'Sezione creata. Abilitala nelle stanze che devono mostrarla.');
    }

    public function edit(Property $property, int $section): View
    {
        Gate::authorize('update', $property);

        return view('pages.host.sections.form', [
            'property' => $property,
            'section' => $property->sections()->findOrFail($section),
        ]);
    }

    public function update(SectionRequest $request, Property $property, int $section): RedirectResponse
    {
        $property->sections()->findOrFail($section)->update($request->validated());

        return redirect()->route('host.properties.show', $property)->with('status', 'Sezione aggiornata in tutte le stanze che la utilizzano.');
    }

    public function destroy(Property $property, int $section): RedirectResponse
    {
        Gate::authorize('update', $property);
        $property->sections()->findOrFail($section)->delete();

        return redirect()->route('host.properties.show', $property)->with('status', 'Sezione eliminata e rimossa dalle guide.');
    }
}
