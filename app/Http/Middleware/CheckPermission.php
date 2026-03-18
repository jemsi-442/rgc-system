<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Tafadhali ingia kwanza');
        }

        $user = auth()->user();

        if (!$user->hasPermission($permission)) {
            return redirect()->route('dashboard')->with('error', 'Huna ruhusa ya kufanya kitendo hiki');
        }

        return $next($request);
    }
}
