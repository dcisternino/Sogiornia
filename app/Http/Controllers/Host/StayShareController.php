<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Services\StayShareMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/** Mostra un messaggio modificabile per condividere soltanto i link di soggiorni ancora validi. */
class StayShareController extends Controller
{
    public function show(Request $request, Property $property, int $room, int $stay, StayShareMessage $messages): View
    {
        Gate::authorize('view', $property);
        $room = $property->rooms()->findOrFail($room);
        $stay = $room->stays()->with('room.property')->findOrFail($stay);
        abort_if($stay->revoked_at || $stay->ends_at->isPast() || $property->archived_at || $room->archived_at || ! $property->guide_enabled || ! $room->guide_enabled, 409, 'Questo link è scaduto, revocato o sospeso e non può essere condiviso.');
        $data = $request->validate(['lang' => ['sometimes', 'required', 'string', Rule::in(array_keys(config('guest.locales')))]]);
        $locale = $data['lang'] ?? 'it';

        return view('pages.host.stays.share', ['property' => $property, 'room' => $room, 'stay' => $stay, 'locale' => $locale, 'share' => $messages->forStay($stay, $locale)]);
    }
}
