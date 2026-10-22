<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Stay;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/** Mostra la demo o le sole sezioni abilitate per una stanza. */
class GuestGuideController extends Controller
{
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
                'sections' => $this->guestSections(),
            ];
        }

        $stay = Stay::query()->where('guide_token', $token)
            ->with(['room.property', 'room.photo'])
            ->firstOrFail();
        abort_unless($stay->isAccessible(), 404);
        $room = $stay->room;

        return [
            'propertyName' => $room->property->name,
            'propertyCity' => $room->property->city,
            'welcomeMessage' => $room->property->welcome_message,
            'roomName' => $room->name,
            'roomImageUrl' => $room->photo ? route('guide.room-image', $token) : null,
            'token' => $token,
            'sections' => Section::query()->visibleToRoom($room)->orderBy('title')->orderBy('id')->get()->mapWithKeys(fn ($section) => [
                $section->slug => [
                    'title' => $section->title,
                    'content' => $section->content,
                    'image_url' => $section->image_path ? route('guide.image', ['token' => $token, 'section' => $section->slug]) : null,
                    'image_alt' => $section->image_alt,
                    'link_url' => $section->link_url,
                    'link_label' => $section->link_label,
                    'phone' => $section->phone,
                ],
            ])->all(),
        ];
    }

    /** @return array<string, array{title: string, content: string}> */
    private function guestSections(): array
    {
        $sections = [];
        foreach (['wifi', 'check-in', 'regole', 'numeri-utili', 'ristoranti', 'luoghi-da-visitare'] as $slug) {
            $sections[$slug] = [
                'title' => __('guest.demo.sections.'.$slug.'.title'),
                'content' => __('guest.demo.sections.'.$slug.'.content'),
            ];
        }

        return $sections;
    }
}
