<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\RoomPhoto;
use App\Models\SectionBlock;
use App\Services\GuideMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/** Archivia, ripristina, sospende o elimina una struttura posseduta dall'host. */
class PropertyLifecycleController extends Controller
{
    public function update(Request $request, Property $property): RedirectResponse
    {
        Gate::authorize('update', $property);
        $data = $request->validate(['action' => ['required', Rule::in(['archive', 'restore', 'suspend', 'resume'])]]);
        match ($data['action']) {
            'archive' => $property->archived_at = now(),
            'restore' => $property->archived_at = null,
            'suspend' => $property->guide_enabled = false,
            'resume' => $property->guide_enabled = true,
        };
        $property->save();

        $message = match ($data['action']) {
            'archive' => 'Struttura archiviata: le sue guide non sono accessibili. Stanze e contenuti restano conservati.',
            'restore' => 'Struttura ripristinata. Le guide tornano accessibili se abilitate e con soggiorno valido.',
            'suspend' => 'Guide della struttura sospese. I contenuti sono conservati e puoi riattivarle quando vuoi.',
            'resume' => 'Guide della struttura riattivate. Restano escluse le stanze archiviate o sospese.',
        };

        return back()->with('status', $message);
    }

    public function destroy(Request $request, Property $property): RedirectResponse
    {
        Gate::authorize('update', $property);
        $request->validate(['confirmation' => ['required', 'string', Rule::in([$property->name])]]);
        $paths = $property->sections()->whereNotNull('image_path')->pluck('image_path')->all();
        $paths = [...$paths, ...SectionBlock::query()->whereHas('section', fn (Builder $query) => $query->whereBelongsTo($property))->whereNotNull('image_path')->pluck('image_path')->all()];
        if ($property->profile?->image_path) {
            $paths[] = $property->profile->image_path;
        }
        $paths = [...$paths, ...RoomPhoto::query()->whereHas('room', fn (Builder $query) => $query->whereBelongsTo($property))->pluck('path')->all()];
        $paths = array_values(array_unique([...$paths, ...app(GuideMedia::class)->historicalPaths($property)]));
        $property->delete();
        Storage::disk('local')->delete($paths);

        return redirect()->route('host.properties.index')->with('status', 'Struttura eliminata con stanze, soggiorni e sezioni.');
    }
}
