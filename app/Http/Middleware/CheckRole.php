<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Tafadhali ingia kwanza');
        }

        $user = auth()->user();

        if (!$user->role) {
            return redirect()->route('dashboard')->with('error', 'Huna ruhusa ya kufikia ukurasa huu');
        }

        // Check if user has any of the allowed roles
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        return redirect()->route('dashboard')->with('error', 'Huna ruhusa ya kufikia ukurasa huu');
    }
}
