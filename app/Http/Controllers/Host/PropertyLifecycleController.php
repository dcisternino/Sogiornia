<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Property;
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

        return back()->with('status', 'Stato della struttura aggiornato. L’archiviazione o la sospensione interrompono l’accesso alle guide.');
    }

    public function destroy(Request $request, Property $property): RedirectResponse
    {
        Gate::authorize('update', $property);
        $request->validate(['confirmation' => ['required', 'string', Rule::in([$property->name])]]);
        $paths = $property->sections()->whereNotNull('image_path')->pluck('image_path')->all();
        $property->delete();
        Storage::disk('local')->delete($paths);

        return redirect()->route('host.properties.index')->with('status', 'Struttura eliminata con stanze, soggiorni e sezioni.');
    }
}
