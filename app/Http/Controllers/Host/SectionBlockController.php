<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Http\Requests\Host\SectionBlockRequest;
use App\Models\Property;
use App\Models\SectionBlock;
use App\Services\GuideMedia;
use App\Services\SectionBlockStorage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/** Gestisce i contenuti di una sezione senza consentire accesso ad altre strutture. */
class SectionBlockController extends Controller
{
    public function index(Property $property, int $section): View
    {
        Gate::authorize('view', $property);
        $section = $property->sections()->findOrFail($section);

        return view('pages.host.blocks.index', ['property' => $property, 'section' => $section, 'blocks' => $section->blocks()->paginate(30)->withQueryString()]);
    }

    public function create(Request $request, Property $property, int $section): View
    {
        Gate::authorize('update', $property);
        $section = $property->sections()->findOrFail($section);
        $type = $request->query('type', 'text');
        abort_unless(is_string($type) && array_key_exists($type, SectionBlock::TYPES), 404);

        return view('pages.host.blocks.form', ['property' => $property, 'section' => $section, 'block' => new SectionBlock(['type' => $type, 'is_active' => true])]);
    }

    public function store(SectionBlockRequest $request, Property $property, int $section, SectionBlockStorage $storage): RedirectResponse
    {
        $section = $property->sections()->findOrFail($section);
        $storage->save($section, new SectionBlock, $request->safe()->except(['image', 'remove_image', 'translations']), $request->file('image'), false, $request->validated('translations', []));

        return redirect()->route('host.blocks.index', [$property, $section])->with('status', 'Contenuto aggiunto alla bozza. Controlla la visibilità e pubblica la guida.');
    }

    public function edit(Property $property, int $section, int $block): View
    {
        Gate::authorize('update', $property);
        $section = $property->sections()->findOrFail($section);

        return view('pages.host.blocks.form', ['property' => $property, 'section' => $section, 'block' => $section->blocks()->with('translations')->findOrFail($block)]);
    }

    public function update(SectionBlockRequest $request, Property $property, int $section, int $block, SectionBlockStorage $storage): RedirectResponse
    {
        $section = $property->sections()->findOrFail($section);
        $block = $section->blocks()->findOrFail($block);
        $storage->save($section, $block, $request->safe()->except(['image', 'remove_image', 'translations']), $request->file('image'), $request->boolean('remove_image'), $request->validated('translations', []));

        return redirect()->route('host.blocks.index', [$property, $section])->with('status', 'Bozza del contenuto salvata. Pubblica la guida per renderla visibile agli ospiti.');
    }

    public function destroy(Property $property, int $section, int $block): RedirectResponse
    {
        Gate::authorize('update', $property);
        $section = $property->sections()->findOrFail($section);
        $block = $section->blocks()->findOrFail($block);
        $path = $block->image_path;
        $block->delete();
        if ($path) {
            app(GuideMedia::class)->deleteUnused($property, [$path]);
        }

        return redirect()->route('host.blocks.index', [$property, $section])->with('status', 'Contenuto eliminato dalla bozza. Pubblica la guida per aggiornare gli ospiti.');
    }
}
