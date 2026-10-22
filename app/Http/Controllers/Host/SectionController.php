<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Http\Requests\Host\SectionRequest;
use App\Models\Property;
use App\Models\Section;
use App\Services\GuideMedia;
use App\Services\SectionImageStorage;
use App\Services\SectionTemplates;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/** Gestisce contenuti testuali riutilizzabili nelle guide della struttura. */
class SectionController extends Controller
{
    /** Mostra contenuti e interruttori della sola struttura posseduta dall'host. */
    public function index(Property $property): View
    {
        Gate::authorize('view', $property);

        return view('pages.host.sections.index', [
            'property' => $property,
            'sections' => $property->sections()->with('settings')->withCount('blocks')->orderBy('title')->orderBy('id')->paginate(20),
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
        $section->content = $section->content ?? '';
        $images->save($section, $request->file('image'), false, $request->validated('translations', []));

        return redirect()->route('host.blocks.index', [$property, $section])->with('status', 'Sezione creata. Aggiungi i contenuti e abilita la sezione nelle stanze che devono mostrarla.');
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
        $section->content = $section->content ?? '';
        $images->save($section, $request->file('image'), $request->boolean('remove_image'), $request->validated('translations', []));

        return redirect()->route('host.sections.index', $property)->with('status', 'Bozza della sezione salvata. Rivedi e pubblica la guida per aggiornare gli ospiti.');
    }

    public function destroy(Property $property, int $section): RedirectResponse
    {
        Gate::authorize('update', $property);
        $section = $property->sections()->findOrFail($section);
        $paths = $section->blocks()->whereNotNull('image_path')->pluck('image_path')->all();
        if ($section->image_path) {
            $paths[] = $section->image_path;
        }
        $section->delete();
        app(GuideMedia::class)->deleteUnused($property, $paths);

        return redirect()->route('host.sections.index', $property)->with('status', 'Sezione rimossa dalla bozza. La versione pubblicata rimane disponibile fino alla prossima pubblicazione.');
    }
}
