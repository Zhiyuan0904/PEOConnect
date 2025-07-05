<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
    public function boot()
    {
        // Force HTTPS if in production
        if (app()->environment('production')) {
            URL::forceScheme('https');
            URL::forceRootUrl(config('app.url'));
        }

        // Optional: if you use older MySQL
        Schema::defaultStringLength(191);
    }
}
