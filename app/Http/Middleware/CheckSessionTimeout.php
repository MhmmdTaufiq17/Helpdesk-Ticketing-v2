<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSessionTimeout
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $lastActivity = session('last_activity');
            $timeout = 298; // 5 minutes

            if ($lastActivity && (time() - $lastActivity > $timeout)) {
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();

                // Set flag untuk SweetAlert
                session()->flash('session_timeout', true);

                return redirect()->route('admin.login');
            }

            session(['last_activity' => time()]);
        }

        return $next($request);
    }
}
