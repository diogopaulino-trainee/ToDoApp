<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Inertia\Inertia;

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
        if (App::environment('testing')) {
            \Inertia\Inertia::setRootView('testing');
        } else {
            Vite::prefetch(concurrency: 3);
        }

        Vite::prefetch(concurrency: 3);
        View::share('laravelVersion', app()->version());
        View::share('phpVersion', phpversion());
        Inertia::share('csrf_token', csrf_token());
    }
}
