<?php

namespace App\Http\Controllers;

use App\Models\Room;
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
                'propertyName' => 'Casa Marina · Demo',
                'welcomeMessage' => 'Un assaggio della guida che puoi creare per i tuoi ospiti.',
                'roomName' => null,
                'token' => null,
                'sections' => $this->guestSections(),
            ];
        }

        $room = Room::query()->where('guide_token', $token)
            ->with(['property', 'sections' => fn ($query) => $query->orderBy('title')->orderBy('id')])
            ->firstOrFail();

        return [
            'propertyName' => $room->property->name,
            'welcomeMessage' => $room->property->welcome_message,
            'roomName' => $room->name,
            'token' => $token,
            'sections' => $room->sections->where('property_id', $room->property_id)->mapWithKeys(fn ($section) => [
                $section->slug => ['title' => $section->title, 'content' => $section->content],
            ])->all(),
        ];
    }

    /** @return array<string, array{title: string, content: string}> */
    private function guestSections(): array
    {
        return [
            'wifi' => ['title' => 'Wi-Fi', 'content' => 'Qui il tuo ospite troverà rete e password configurate da te. Questa è una guida dimostrativa.'],
            'check-in' => ['title' => 'Check-in', 'content' => 'Arrivo dalle 15:00. Check-out entro le 10:00.'],
            'regole' => ['title' => 'Regole della casa', 'content' => 'Rispetta il riposo degli altri ospiti e prenditi cura degli spazi.'],
            'numeri-utili' => ['title' => 'Numeri utili', 'content' => 'Emergenze: 112. Aggiungi qui i contatti utili della tua struttura.'],
            'ristoranti' => ['title' => 'Dove mangiare', 'content' => 'Condividi i tuoi posti del cuore e i consigli di chi vive il territorio.'],
        ];
    }
}
