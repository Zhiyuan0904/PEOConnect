<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Log; // ✅ Add this line

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request): ?string
    {
        Log::info('✅ Custom Authenticate middleware used.');

        if ($request->expectsJson()) {
            return null;
        }

        abort(401, 'Unauthenticated');
    }
}
