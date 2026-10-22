<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Room;
use App\Services\HostOnboarding;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Accompagna l'host dalla prima struttura alla condivisione della guida, senza creare dati fittizi. */
class OnboardingController extends Controller
{
    public function index(Request $request, HostOnboarding $onboarding): View
    {
        $data = $request->validate([
            'property_id' => ['nullable', 'required_with:room_id', 'integer', Rule::exists(Property::class, 'id')->where('user_id', $request->user()->id)->whereNull('archived_at')],
            'room_id' => ['nullable', 'integer', Rule::exists(Room::class, 'id')->where(fn (Builder $query) => $query->where('property_id', $request->integer('property_id'))->whereNull('archived_at'))],
        ]);

        return view('pages.host.setup', $onboarding->forHost($request->user(), isset($data['property_id']) ? (int) $data['property_id'] : null, isset($data['room_id']) ? (int) $data['room_id'] : null, $request->session()->get('onboarding.word_templates', [])));
    }
}
