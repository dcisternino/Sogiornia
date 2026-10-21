<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Mantiene gli host disattivati in sola lettura, senza interrompere le guide esistenti. */
class EnsureHostCanWrite
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->isHost() && ! $request->user()->canWrite()) {
            abort_if(! $request->isMethodSafe() || $request->routeIs('host.*.create', 'host.*.edit'), 403, 'Account in sola lettura: contatta l’amministratore.');
        }

        return $next($request);
    }
}
