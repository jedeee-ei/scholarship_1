<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register the middleware
        $this->app['router']->aliasMiddleware('ensure.application.ownership', \App\Http\Middleware\EnsureStudentOwnsApplication::class);

        // Add scholarship renewal period config
        config(['scholarship.renewal_period' => false]); // Set to true during renewal periods
    }
}

