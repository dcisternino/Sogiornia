<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Valida i tipi prima della normalizzazione e della verifica del token eseguite da Fortify. */
class ValidatePasswordReset
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->validate([
            'token' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        return $next($request);
    }
}
