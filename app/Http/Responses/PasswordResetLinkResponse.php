<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\FailedPasswordResetLinkRequestResponse;
use Laravel\Fortify\Contracts\SuccessfulPasswordResetLinkRequestResponse;
use Symfony\Component\HttpFoundation\Response;

/** Rende indistinguibili indirizzi registrati, sconosciuti e richieste appena ripetute. */
class PasswordResetLinkResponse implements FailedPasswordResetLinkRequestResponse, SuccessfulPasswordResetLinkRequestResponse
{
    /** @param Request $request */
    public function toResponse($request): Response
    {
        $message = 'Se l’indirizzo è registrato, riceverai un link per scegliere la password. Controlla anche la posta indesiderata.';

        return $request->wantsJson() ? response()->json(['message' => $message]) : back()->with('status', $message);
    }
}
