<?php

namespace App\Providers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\SendPasswordResetLinkRequest;
use App\Http\Responses\LoginResponse;
use App\Http\Responses\PasswordResetLinkResponse;
use App\Services\ResetUserPassword;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\FailedPasswordResetLinkRequestResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\SuccessfulPasswordResetLinkRequestResponse;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use Laravel\Fortify\Http\Requests\SendPasswordResetLinkRequest as FortifySendPasswordResetLinkRequest;

/** Integra Fortify con inviti host, validazione e interfaccia Blade di Sogiornia. */
class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Le route restano esplicite in web.php per conservarne URL, commenti e protezioni.
        Fortify::ignoreRoutes();
        $this->app->bind(FortifyLoginRequest::class, LoginRequest::class);
        $this->app->bind(FortifySendPasswordResetLinkRequest::class, SendPasswordResetLinkRequest::class);
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);
        $this->app->singleton(SuccessfulPasswordResetLinkRequestResponse::class, PasswordResetLinkResponse::class);
        $this->app->singleton(FailedPasswordResetLinkRequestResponse::class, PasswordResetLinkResponse::class);
    }

    public function boot(): void
    {
        Fortify::loginView('auth.login');
        Fortify::requestPasswordResetLinkView('auth.forgot-password');
        Fortify::resetPasswordView(fn (Request $request): View => view('auth.reset-password', ['token' => $request->route('token'), 'email' => $request->query('email', '')]));
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
    }
}
