<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;

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
        // ✅ Force HTTPS only — do NOT override the root URL
        if (app()->environment('production')) {
            URL::forceScheme('https'); // Ensures signed URLs are validated as HTTPS
            // URL::forceRootUrl(config('app.url')); ❌ Do NOT use this — it may break signed route validation
        }

        // ✅ Optional: Prevent charset issues with older MySQL
        Schema::defaultStringLength(191);
    }
}
