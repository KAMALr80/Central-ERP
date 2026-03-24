<?php
// app/Http/Middleware/RoleMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = Auth::user()->role;

        // If no roles specified, just check if user is authenticated
        if (empty($roles)) {
            return $next($request);
        }

        // Check if user's role matches any of the allowed roles
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // Redirect based on role
        if ($userRole === 'admin') {
            return redirect()->route('admin.dashboard')->with('error', 'Unauthorized access.');
        } elseif ($userRole === 'delivery_agent') {
            return redirect()->route('agent.dashboard')->with('error', 'Unauthorized access.');
        } else {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }
    }
}
