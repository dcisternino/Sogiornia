<?php

// Messaggi italiani per le regole usate nei moduli del servizio.
return [
    'required' => 'Il campo :attribute è obbligatorio.',
    'string' => 'Il campo :attribute deve essere un testo.',
    'email' => 'Inserisci un indirizzo email valido.',
    'unique' => 'Il valore di :attribute è già utilizzato.',
    'confirmed' => 'La conferma di :attribute non corrisponde.',
    'array' => 'Il campo :attribute deve contenere una lista.',
    'integer' => 'Il campo :attribute deve essere un numero intero.',
    'distinct' => 'Il campo :attribute contiene un valore duplicato.',
    'exists' => 'Il valore selezionato per :attribute non è consentito.',
    'boolean' => 'Il campo :attribute deve essere vero o falso.',
    'regex' => 'Il campo :attribute può contenere solo lettere minuscole, numeri e trattini tra le parole.',
    'min' => ['string' => 'Il campo :attribute deve contenere almeno :min caratteri.'],
    'max' => [
        'string' => 'Il campo :attribute non può superare :max caratteri.',
        'array' => 'Il campo :attribute non può contenere più di :max elementi.',
    ],
    'password' => [
        'mixed' => 'La password deve contenere almeno una maiuscola e una minuscola.',
        'numbers' => 'La password deve contenere almeno un numero.',
    ],
    'attributes' => [
        'name' => 'nome',
        'email' => 'email',
        'password' => 'password',
        'city' => 'città',
        'address' => 'indirizzo',
        'welcome_message' => 'messaggio di benvenuto',
        'description' => 'descrizione',
        'title' => 'titolo',
        'slug' => 'identificativo',
        'content' => 'contenuto',
        'sections' => 'sezioni',
        'sections.*' => 'sezione',
    ],
];
