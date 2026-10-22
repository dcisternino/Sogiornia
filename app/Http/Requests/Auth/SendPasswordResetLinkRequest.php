<?php

namespace App\Http\Requests\Auth;

use Laravel\Fortify\Http\Requests\SendPasswordResetLinkRequest as FortifySendPasswordResetLinkRequest;

/** Mantiene il limite dell'indirizzo email nella richiesta di recupero gestita da Fortify. */
class SendPasswordResetLinkRequest extends FortifySendPasswordResetLinkRequest
{
    public function rules(): array
    {
        return ['email' => ['required', 'string', 'email', 'max:255']];
    }
}
