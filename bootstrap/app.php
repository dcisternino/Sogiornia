<?php

use App\Http\Middleware\EnsureHostCanWrite;
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\ProtectGuestGuide;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Registra i controlli sui ruoli e la riservatezza delle guide ospite.
        $middleware->alias([
            'role' => EnsureUserHasRole::class,
            'host.write' => EnsureHostCanWrite::class,
            'guide.privacy' => ProtectGuestGuide::class,
        ]);
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(fn () => route('dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
