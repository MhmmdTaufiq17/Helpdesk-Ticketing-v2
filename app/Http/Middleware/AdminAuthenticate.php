<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticate
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Jika tidak login
        if (!Auth::check()) {
            return redirect()->route('home');
        }

        // Cek role
        $allowedRoles = $roles ?: ['admin', 'super_admin'];
        if (!in_array(Auth::user()->role, $allowedRoles)) {
            return redirect()->route('home')->with('error', 'Access denied');
        }

        return $next($request);
    }
}
