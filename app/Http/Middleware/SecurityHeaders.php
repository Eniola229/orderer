<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline' cdn.jsdelivr.net cdnjs.cloudflare.com js.pusher.com cdn.tiny.cloud https://maps.googleapis.com https://maps.gstatic.com https://static.ads-twitter.com; " .
            "style-src 'self' 'unsafe-inline' cdn.jsdelivr.net cdnjs.cloudflare.com fonts.googleapis.com cdn.tiny.cloud; " .
            "font-src 'self' fonts.gstatic.com cdnjs.cloudflare.com cdn.tiny.cloud; " .
            "img-src 'self' data: blob: https://res.cloudinary.com https://*.cloudinary.com cdn.tiny.cloud sp.tinymce.com https://maps.googleapis.com https://maps.gstatic.com https://*.ggpht.com https://t.co https://ads-twitter.com https://*.ads-twitter.com https://analytics.twitter.com https://ads-api.twitter.com; " .
            "connect-src 'self' wss://*.pusher.com https://*.pusher.com https://cdn.tiny.cloud sp.tinymce.com https://maps.googleapis.com https://restcountries.com https://static.ads-twitter.com https://analytics.twitter.com https://ads-api.twitter.com; " .
            "worker-src 'self' blob:;"
        );
        return $response;
    }
}