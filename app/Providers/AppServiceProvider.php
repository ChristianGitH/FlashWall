<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Force main domaine for all generated URLs
        if (config('app.env') !== 'local') {
            URL::forceRootUrl('https://flashwall.app');
        }

        // Force HTTPS for all URLs
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }

        view()->composer('partials.language-switcher', function ($view) {
            $view->with('current_locale', app()->getLocale());
            $view->with('available_locales', config('app.available_locales'));
        });
    }
}
