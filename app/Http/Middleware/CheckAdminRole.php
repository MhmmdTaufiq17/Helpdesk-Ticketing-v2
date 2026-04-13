<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return redirect()->route('admin.login');
        }

        // Jika tidak ada role yang dispesifikasikan, cek apakah user admin atau super_admin
        if (empty($roles)) {
            if (!auth()->user()->isAnyAdmin()) {
                abort(403, 'Unauthorized access.');
            }
            return $next($request);
        }

        // Cek apakah user memiliki role yang diizinkan
        $hasRole = false;
        foreach ($roles as $role) {
            if ($role === 'super_admin' && auth()->user()->isSuperAdmin()) {
                $hasRole = true;
                break;
            }
            if ($role === 'admin' && auth()->user()->isAdmin()) {
                $hasRole = true;
                break;
            }
        }

        if (!$hasRole) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
