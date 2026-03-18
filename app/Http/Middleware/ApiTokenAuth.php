<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['message' => __('Unauthorized. Missing bearer token.')], 401);
        }

        $hashed = hash('sha256', $token);

        $user = User::query()->where('api_token', $hashed)->first();

        if (! $user) {
            return response()->json(['message' => __('Unauthorized. Invalid token.')], 401);
        }

        Auth::setUser($user);
        $request->setUserResolver(fn () => $user);

        $supported = config('app.supported_locales', ['en', 'sw']);
        if ($user->locale && in_array($user->locale, $supported, true)) {
            App::setLocale($user->locale);
        }

        return $next($request);
    }
}
