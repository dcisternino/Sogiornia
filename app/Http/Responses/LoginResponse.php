<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

/** Invia sempre alla dashboard che determina la destinazione in base al ruolo autenticato. */
class LoginResponse implements LoginResponseContract
{
    /** @param Request $request */
    public function toResponse($request): Response
    {
        return $request->wantsJson() ? response()->json(['two_factor' => false]) : redirect()->route('dashboard');
    }
}
