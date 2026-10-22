<?php

namespace App\Http\Controllers\Auth;

use App\Http\Responses\PasswordResetLinkResponse;
use Illuminate\Contracts\Support\Responsable;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController as FortifyPasswordResetLinkController;
use Laravel\Fortify\Http\Requests\SendPasswordResetLinkRequest;
use Throwable;

/** Usa il recupero Fortify mantenendo una risposta riservata anche in caso di errore email. */
class PasswordResetLinkController extends FortifyPasswordResetLinkController
{
    public function store(SendPasswordResetLinkRequest $request): Responsable
    {
        try {
            return parent::store($request);
        } catch (Throwable $exception) {
            report($exception);

            return new PasswordResetLinkResponse;
        }
    }
}
