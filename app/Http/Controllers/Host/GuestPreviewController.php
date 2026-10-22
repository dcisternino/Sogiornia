<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Services\GuestGuideData;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;

/** Mostra all'host proprietario un'anteprima privata senza creare soggiorni o link pubblici. */
class GuestPreviewController extends Controller
{
    public function index(Property $property, int $room, GuestGuideData $guides): View
    {
        Gate::authorize('view', $property);

        return view('pages.guest.home', $guides->forRoom($property->rooms()->findOrFail($room), null));
    }

    public function show(Property $property, int $room, string $section, GuestGuideData $guides): View
    {
        Gate::authorize('view', $property);
        $guide = $guides->forRoom($property->rooms()->findOrFail($room), null);
        abort_unless(isset($guide['sections'][$section]), 404);

        return view('pages.guest.section', [...$guide, 'section' => $guide['sections'][$section]]);
    }
}
