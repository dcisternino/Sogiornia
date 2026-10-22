<?php

namespace App\Http\Middleware;

use App\Services\OnboardingNavigation;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Mantiene provenienza e stanza selezionata durante il percorso guidato e gli errori di validazione. */
class ShareOnboardingNavigation
{
    public function __construct(private OnboardingNavigation $navigation) {}

    public function handle(Request $request, Closure $next): Response
    {
        $navigation = $this->navigation;
        view()->share('setupNavigation', $navigation);
        $response = $next($request);
        if ($response instanceof RedirectResponse) {
            $response->setTargetUrl($navigation->carry($response->getTargetUrl()));
        }

        return $response;
    }
}
