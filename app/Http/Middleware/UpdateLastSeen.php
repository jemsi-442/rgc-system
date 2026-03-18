<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastSeen
{
    /**
     * Handle an incoming request.
     * Updates the user's last_seen_at timestamp on each request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            try {
                Auth::user()->update(['last_seen_at' => now()]);
            } catch (\Exception $e) {
                // Column might not exist yet, ignore
            }
        }

        return $next($request);
    }
}
