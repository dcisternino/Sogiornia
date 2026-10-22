<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Section;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Elenca le sezioni dell'host mantenendo separate le assegnazioni delle singole strutture. */
class SectionDirectoryController extends Controller
{
    public function index(Request $request): View
    {
        $data = $request->validate(['property_id' => ['nullable', 'integer', Rule::exists(Property::class, 'id')->where('user_id', $request->user()->id)]]);
        $sections = Section::query()->whereHas('property', fn (Builder $query) => $query->whereBelongsTo($request->user()));
        if (! empty($data['property_id'])) {
            $sections->where('property_id', $data['property_id']);
        }

        return view('pages.host.sections.directory', [
            'properties' => $request->user()->properties()->orderBy('name')->orderBy('id')->get(),
            'selectedProperty' => $data['property_id'] ?? null,
            'sections' => $sections->with(['property', 'settings'])->withCount('blocks')->orderBy('property_id')->orderBy('title')->orderBy('id')->paginate(20)->withQueryString(),
        ]);
    }
}
