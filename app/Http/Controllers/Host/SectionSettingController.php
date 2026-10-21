<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/** Aggiorna un solo interruttore senza modificare assegnazioni o contenuti della sezione. */
class SectionSettingController extends Controller
{
    public function update(Request $request, Property $property, int $section): RedirectResponse
    {
        Gate::authorize('update', $property);
        $section = $property->sections()->findOrFail($section);
        $data = $request->validate(['setting' => ['required', Rule::in(['is_active', 'is_shared'])], 'value' => ['required', 'boolean']]);
        $section->settings()->updateOrCreate([], [$data['setting'] => $request->boolean('value')]);
        $message = $data['setting'] === 'is_active'
            ? ($request->boolean('value') ? 'Sezione attivata: visibile nelle stanze abilitate, oppure in tutte se condivisa.' : 'Sezione disattivata: nascosta da tutte le guide. I contenuti sono conservati.')
            : ($request->boolean('value') ? 'Sezione condivisa con tutte le stanze, comprese quelle nuove. Deve essere attiva per apparire nelle guide.' : 'Condivisione rimossa: restano valide le assegnazioni alle singole stanze.');

        return back()->with('status', $message);
    }
}
