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
    /** Mostra contenuti e interruttori della sola struttura posseduta dall'host. */
    public function index(Property $property): View
    {
        Gate::authorize('view', $property);

        return view('pages.host.sections.index', [
            'property' => $property,
            'sections' => $property->sections()->with('settings')->orderBy('title')->orderBy('id')->paginate(20),
        ]);
    }

    public function create(Request $request, Property $property, SectionTemplates $templates): View
    {
        Gate::authorize('update', $property);

        $templateKey = $request->query('template');
        $template = is_string($templateKey) ? ($templates->all()[$templateKey] ?? []) : [];

        return view('pages.host.sections.form', ['property' => $property, 'section' => new Section($template), 'templates' => $templates->all()]);
    }

    public function store(SectionRequest $request, Property $property, SectionImageStorage $images): RedirectResponse
    {
        $section = $property->sections()->make($request->safe()->except(['image', 'remove_image', 'translations']));
        $images->save($section, $request->file('image'), false, $request->validated('translations', []));

        return redirect()->route('host.sections.index', $property)->with('status', 'Sezione creata. Abilitala nelle stanze che devono mostrarla.');
    }

    public function edit(Property $property, int $section): View
    {
        Gate::authorize('update', $property);

        return view('pages.host.sections.form', [
            'property' => $property,
            'section' => $property->sections()->with('translations')->findOrFail($section),
            'templates' => [],
        ]);
    }

    public function update(SectionRequest $request, Property $property, int $section, SectionImageStorage $images): RedirectResponse
    {
        $section = $property->sections()->findOrFail($section);
        $section->fill($request->safe()->except(['image', 'remove_image', 'translations']));
        $images->save($section, $request->file('image'), $request->boolean('remove_image'), $request->validated('translations', []));

        return redirect()->route('host.sections.index', $property)->with('status', 'Sezione aggiornata in tutte le stanze che la utilizzano.');
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

        return redirect()->route('host.sections.index', $property)->with('status', 'Sezione eliminata e rimossa dalle guide.');
    }
}
