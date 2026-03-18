<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = config('app.supported_locales', ['en', 'sw']);
        $locale = $request->hasSession() ? $request->session()->get('locale') : null;

        if (! $locale && $request->query('lang') && in_array($request->query('lang'), $supported, true)) {
            $locale = $request->query('lang');
        }

        if (! $locale && $request->user()?->locale && in_array($request->user()->locale, $supported, true)) {
            $locale = $request->user()->locale;
        }

        if (! $locale && $request->hasHeader('Accept-Language')) {
            $preferred = substr((string) $request->header('Accept-Language'), 0, 2);

            if (in_array($preferred, $supported, true)) {
                $locale = $preferred;
            }
        }

        App::setLocale(in_array($locale, $supported, true) ? $locale : config('app.locale'));

        return $next($request);
    }
}
