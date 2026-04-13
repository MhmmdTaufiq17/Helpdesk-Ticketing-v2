<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            // Jika akses ke halaman admin, redirect ke admin.login
            if ($request->is('admin*') || $request->routeIs('admin.*')) {
                return route('admin.login');
            }

            // Selain itu redirect ke halaman home user
            return route('home');
        }

        return null;
    }
}
