<?php

use Laravel\Fortify\Features;

/** Configura Fortify sulle pagine esistenti; gli host vengono creati soltanto tramite invito admin. */
return [
    'guard' => 'web',
    'passwords' => 'users',
    'username' => 'email',
    'email' => 'email',
    'lowercase_usernames' => true,
    'home' => '/dashboard',
    'views' => true,
    'middleware' => ['web'],
    'limiters' => ['login' => null],
    'redirects' => ['logout' => '/', 'password-reset' => '/login'],
    // Abilita il recupero usato anche dagli inviti, senza registrazione pubblica o nuovi endpoint del profilo.
    'features' => [Features::resetPasswords()],
];
