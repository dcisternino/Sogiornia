<?php

namespace App\Services;

/** Offre punti di partenza modificabili senza pubblicare automaticamente dati sensibili. */
class SectionTemplates
{
    /** @return array<string, array{title: string, content: string}> */
    public function all(): array
    {
        return [
            'wifi' => ['title' => 'Wi-Fi', 'content' => "Nome rete: [inserisci il nome]\nPassword: [inserisci la password]"],
            'check-in' => ['title' => 'Check-in e check-out', 'content' => "Check-in dalle: [orario]\nCheck-out entro: [orario]\nRitiro e riconsegna chiavi: [istruzioni]"],
            'regole' => ['title' => 'Regole della casa', 'content' => "Orari di silenzio: [orari]\nFumo e animali: [regole]\nRaccolta differenziata: [istruzioni]"],
            'contatti' => ['title' => 'Contatti utili', 'content' => "Come contattare l’host: [istruzioni]\nIn caso di emergenza chiama il 112."],
            'territorio' => ['title' => 'Da scoprire', 'content' => "Il nostro consiglio: [luogo o esperienza]\nCome arrivare: [indicazioni]\nOrari e prenotazioni: [informazioni]"],
        ];
    }
}
