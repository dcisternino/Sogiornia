<?php

namespace App\Http\Controllers;

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
                'propertyName' => 'Casa Marina · Demo',
                'propertyCity' => 'Bari',
                'welcomeMessage' => 'Un assaggio della guida che puoi creare per i tuoi ospiti.',
                'roomName' => null,
                'roomImageUrl' => null,
                'token' => null,
                'sections' => $this->guestSections(),
            ];
        }

        $stay = Stay::query()->where('guide_token', $token)
            ->with(['room.property', 'room.photo', 'room.sections' => fn ($query) => $query->orderBy('title')->orderBy('id')])
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
            'sections' => $room->sections->where('property_id', $room->property_id)->mapWithKeys(fn ($section) => [
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
        return [
            'wifi' => ['title' => 'Wi-Fi', 'content' => 'Qui il tuo ospite troverà rete e password configurate da te. Questa è una guida dimostrativa.'],
            'check-in' => ['title' => 'Check-in', 'content' => 'Arrivo dalle 15:00. Check-out entro le 10:00.'],
            'regole' => ['title' => 'Regole della casa', 'content' => 'Rispetta il riposo degli altri ospiti e prenditi cura degli spazi.'],
            'numeri-utili' => ['title' => 'Numeri utili', 'content' => 'Emergenze: 112. Aggiungi qui i contatti utili della tua struttura.'],
            'ristoranti' => ['title' => 'Dove mangiare', 'content' => 'Condividi i tuoi posti del cuore e i consigli di chi vive il territorio.'],
            'luoghi-da-visitare' => ['title' => 'Luoghi da visitare', 'content' => "Una passeggiata tra i vicoli di Bari Vecchia, il lungomare e una visita alla Basilica di San Nicola: qualche idea per scoprire la città con calma.\n\nQuesti sono consigli dimostrativi. Nella tua guida potrai inserire i tuoi luoghi preferiti, indicazioni e link alle mappe."],
        ];
    }
}
