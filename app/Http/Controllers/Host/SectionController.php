<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Http\Requests\Host\SectionRequest;
use App\Models\Property;
use App\Models\Section;
use App\Services\SectionImageStorage;
use App\Services\SectionTemplates;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

/** Gestisce contenuti testuali riutilizzabili nelle guide della struttura. */
class SectionController extends Controller
{
    public function create(Request $request, Property $property, SectionTemplates $templates): View
    {
        Gate::authorize('update', $property);

        $templateKey = $request->query('template');
        $template = is_string($templateKey) ? ($templates->all()[$templateKey] ?? []) : [];

        return view('pages.host.sections.form', ['property' => $property, 'section' => new Section($template), 'templates' => $templates->all()]);
    }

    public function store(SectionRequest $request, Property $property, SectionImageStorage $images): RedirectResponse
    {
        $section = $property->sections()->make($request->safe()->except(['image', 'remove_image']));
        $images->save($section, $request->file('image'), false);

        return redirect()->route('host.properties.show', $property)->with('status', 'Sezione creata. Abilitala nelle stanze che devono mostrarla.');
    }

    public function edit(Property $property, int $section): View
    {
        Gate::authorize('update', $property);

        return view('pages.host.sections.form', [
            'property' => $property,
            'section' => $property->sections()->findOrFail($section),
            'templates' => [],
        ]);
    }

    public function update(SectionRequest $request, Property $property, int $section, SectionImageStorage $images): RedirectResponse
    {
        $section = $property->sections()->findOrFail($section);
        $section->fill($request->safe()->except(['image', 'remove_image']));
        $images->save($section, $request->file('image'), $request->boolean('remove_image'));

        return redirect()->route('host.properties.show', $property)->with('status', 'Sezione aggiornata in tutte le stanze che la utilizzano.');
    }

    public function destroy(Property $property, int $section): RedirectResponse
    {
        Gate::authorize('update', $property);
        $section = $property->sections()->findOrFail($section);
        $path = $section->image_path;
        $section->delete();
        if ($path) {
            Storage::disk('local')->delete($path);
        }

        return redirect()->route('host.properties.show', $property)->with('status', 'Sezione eliminata e rimossa dalle guide.');
    }
}
