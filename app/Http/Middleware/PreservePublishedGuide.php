<?php

namespace App\Http\Middleware;

use App\Models\Property;
use App\Services\GuidePublication;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

/** Salva la versione iniziale prima delle modifiche e serializza le revisioni della bozza. */
class PreservePublishedGuide
{
    public function __construct(private GuidePublication $publication) {}

    public function handle(Request $request, Closure $next): Response
    {
        $property = $request->route('property');
        if ($request->isMethodSafe() || ! $property instanceof Property || ! $request->routeIs('host.properties.update', 'host.rooms.store', 'host.rooms.update', 'host.rooms.destroy', 'host.sections.*', 'host.blocks.*')) {
            return $next($request);
        }
        Gate::authorize('update', $property);

        return DB::transaction(function () use ($property, $request, $next): Response {
            $workspace = $this->publication->lock($property);
            $workspace->increment('revision');

            $response = $next($request);
            // La pipeline Laravel può trasformare un'eccezione in risposta prima del ritorno al middleware.
            $validationRedirect = $response->isRedirection() && $request->hasSession() && in_array('errors', $request->session()->get('_flash.new', []), true);
            if ($response->getStatusCode() >= 400 || $validationRedirect) {
                throw new HttpResponseException($response);
            }

            return $response;
        });
    }
}
