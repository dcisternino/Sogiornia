<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Config\app;

class PageController extends Controller
{
    public function welcome()
    {
        $appName = config('app.name');
        return view('welcome', ['title' => "$appName | Welcome Page"]);
    }
    public function home_admin()
    {
        $appName = config('app.name');
        return view('pages.admin.home', ['title' => "$appName | Admin Homepage"]);
    }
    public function host_admin()
    {
        $appName = config('app.name');
        return view('pages.host.home', ['title' => "$appName | Host Homepage"]);
    }

    public function guest_admin()
    {
        return view('pages.guest.home', [
            'sections' => $this->guestSections(),
        ]);
    }

    public function guestSection(string $section)
    {
        $sections = $this->guestSections();

        abort_unless(isset($sections[$section]), 404);

        return view('pages.guest.section', [
            'section' => $sections[$section],
        ]);
    }



    private function guestSections(): array
    {
        return [
            'wifi' => [
                'title' => 'Wi-Fi',
                'icon' => '📶',
                'content' => [
                    'Nome rete' => 'CasaMarina_Guest',
                    'Password' => 'CasaMarina2026',
                ],
            ],
            'check-in' => [
                'title' => 'Check-in',
                'icon' => '🔑',
                'content' => [
                    'Orario check-in' => 'Dalle 15:00',
                    'Orario check-out' => 'Entro le 10:00',
                ],
            ],
            'regole' => [
                'title' => 'Regole della casa',
                'icon' => '📖',
                'content' => [
                    'Fumo' => 'Vietato fumare',
                    'Animali' => 'Non ammessi',
                ],
            ],
            'numeri-utili' => [
                'title' => 'Numeri utili',
                'icon' => '📞',
                'content' => [
                    'Emergenze' => '112',
                    'Host (numero dimostrativo)' => '333 000 0000',
                ],
            ],
            'ristoranti' => [
                'title' => 'Ristoranti',
                'icon' => '🍝',
                'content' => [
                    'Suggerimento 1' => 'Ristorante tipico',
                    'Suggerimento 2' => 'Pizzeria locale',
                ],
            ],
        ];
    }
}
