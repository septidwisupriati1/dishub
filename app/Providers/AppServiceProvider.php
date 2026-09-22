<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\LaravelVersionHelper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Laravel version helper as singleton
        $this->app->singleton('laravel-version-helper', function () {
            return new LaravelVersionHelper();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Log version info on application boot (only once)
        if (app()->runningInConsole() === false) {
            \Log::info('Application running on Laravel ' . LaravelVersionHelper::getVersion());
        }

        // Register macro for version checking on request
        if (!request()->hasMacro('laravelVersion')) {
            \Illuminate\Http\Request::macro('laravelVersion', function () {
                return LaravelVersionHelper::getVersion();
            });
        }
    }
}