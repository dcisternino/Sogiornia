<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/** Sceglie e ricorda la lingua della sola guida, senza cambiare quella delle aree riservate. */
class SetGuestLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = array_keys(config('guest.locales'));
        $selected = $request->query('lang');
        $remembered = $request->cookie('guest_locale');
        $locale = is_string($selected) && in_array($selected, $supported, true)
            ? $selected
            : (is_string($remembered) && in_array($remembered, $supported, true)
                ? $remembered
                : ($request->getPreferredLanguage($supported) ?? 'it'));
        $previous = App::currentLocale();
        App::setLocale($locale);

        try {
            $response = $next($request);
            $response->headers->set('Content-Language', $locale);
            $response->setVary(['Accept-Language', 'Cookie'], false);
            $response->headers->set('Cache-Control', 'private, no-store');
            if ($remembered !== $locale) {
                $response->headers->setCookie(cookie('guest_locale', $locale, 60 * 24 * 365, '/', null, $request->isSecure(), true, false, 'lax'));
            }

            return $response;
        } finally {
            App::setLocale($previous);
        }
    }
}
