<?php

namespace App\Http\Controllers;

use App\Models\Stay;
use App\Services\GuestGuideData;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/** Mostra la demo o le sole sezioni abilitate per una stanza. */
class GuestGuideController extends Controller
{
    public function __construct(private GuestGuideData $guides) {}

    public function index(Request $request): View
    {
        return view('pages.guest.home', $this->guide($request));
    }

    public function show(Request $request): View
    {
        $guide = $this->guide($request);
        $slug = $request->route('section');
        abort_unless(isset($guide['sections'][$slug]), 404);

        return view('pages.guest.section', [...$guide, 'section' => $guide['sections'][$slug]]);
    }

    /** @return array<string, mixed> */
    private function guide(Request $request): array
    {
        $token = $request->route('token');

        if ($token === null) {
            return [
                'propertyName' => __('guest.demo.property'),
                'propertyCity' => 'Bari',
                'welcomeMessage' => __('guest.demo.welcome'),
                'roomName' => null,
                'roomImageUrl' => null,
                'token' => null,
                'isDemo' => true,
                'isPreview' => false,
                'hasFallback' => false,
                'homeUrl' => route('guest-home', ['lang' => app()->getLocale()]),
                'sections' => $this->guestSections(),
            ];
        }

        $stay = Stay::query()->where('guide_token', $token)
            ->with(['room.property', 'room.photo'])
            ->firstOrFail();
        abort_unless($stay->isAccessible(), 404);

        return $this->guides->forRoom($stay->room, $token);
    }

    /** @return array<string, array{title: string, content: string}> */
    private function guestSections(): array
    {
        $sections = [];
        foreach (['wifi', 'check-in', 'regole', 'numeri-utili', 'ristoranti', 'luoghi-da-visitare'] as $slug) {
            $sections[$slug] = [
                'title' => __('guest.demo.sections.'.$slug.'.title'),
                'content' => __('guest.demo.sections.'.$slug.'.content'),
                'url' => route('guest.section', ['section' => $slug, 'lang' => app()->getLocale()]),
            ];
        }

        return $sections;
    }
}
