<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Room;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Elenca le stanze di tutte le strutture possedute, con filtro per struttura. */
class RoomDirectoryController extends Controller
{
    public function index(Request $request): View
    {
        $data = $request->validate(['property_id' => ['nullable', 'integer', Rule::exists(Property::class, 'id')->where('user_id', $request->user()->id)]]);
        $rooms = Room::query()->whereHas('property', fn (Builder $query) => $query->whereBelongsTo($request->user()));
        if (! empty($data['property_id'])) {
            $rooms->where('property_id', $data['property_id']);
        }

        return view('pages.host.rooms.directory', [
            'properties' => $request->user()->properties()->orderBy('name')->orderBy('id')->get(),
            'selectedProperty' => $data['property_id'] ?? null,
            'rooms' => $rooms->with(['property', 'photo'])->orderBy('property_id')->orderBy('name')->orderBy('id')->paginate(12)->withQueryString(),
        ]);
    }
}
