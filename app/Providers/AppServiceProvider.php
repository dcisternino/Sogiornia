<?php

namespace App\Providers;

use App\Services\OnboardingNavigation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(OnboardingNavigation::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Mantiene la paginazione coerente con il frontend Bootstrap.
        Paginator::useBootstrapFive();
    }
}
