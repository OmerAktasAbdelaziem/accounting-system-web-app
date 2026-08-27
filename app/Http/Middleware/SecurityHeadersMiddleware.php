<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SecurityHeadersMiddleware - Adds security headers to all responses
 * 
 * Headers included:
 * - X-Content-Type-Options: Prevent MIME type sniffing
 * - X-Frame-Options: Prevent clickjacking
 * - X-XSS-Protection: Enable XSS protection
 * - Referrer-Policy: Control referrer information
 * - Strict-Transport-Security: Force HTTPS (production only)
 */
class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $headers = $response->headers;

        // Prevent MIME type sniffing
        $headers->set('X-Content-Type-Options', 'nosniff');

        // Prevent clickjacking (frame embedding)
        $headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Enable XSS protection in older browsers
        $headers->set('X-XSS-Protection', '1; mode=block');

        // Control referrer information
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Disable content sniffing
        $headers->set('X-Content-Type-Options', 'nosniff');

        // Only send Strict-Transport-Security in production with HTTPS
        if (config('app.env') === 'production' && request()->secure()) {
            $headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Cache control headers
        $headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $headers->set('Pragma', 'no-cache');
        $headers->set('Expires', '0');

        return $response;
    }
}
