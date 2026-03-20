<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    /**
     * Ensure generated URLs stay on HTTPS behind Railway or other reverse proxies.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('production')) {
            $appUrl = (string) config('app.url');
            $forwardedProto = strtolower((string) $request->headers->get('x-forwarded-proto', ''));

            if ($request->isSecure() || $forwardedProto === 'https' || str_starts_with($appUrl, 'https://')) {
                URL::forceScheme('https');

                if ($appUrl !== '') {
                    URL::forceRootUrl($appUrl);
                }
            }
        }

        return $next($request);
    }
}
