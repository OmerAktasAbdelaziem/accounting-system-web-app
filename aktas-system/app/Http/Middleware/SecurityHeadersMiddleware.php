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

        // Prevent MIME type sniffing
        $response->header('X-Content-Type-Options', 'nosniff');

        // Prevent clickjacking (frame embedding)
        $response->header('X-Frame-Options', 'SAMEORIGIN');

        // Enable XSS protection in older browsers
        $response->header('X-XSS-Protection', '1; mode=block');

        // Control referrer information
        $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Disable content sniffing
        $response->header('X-Content-Type-Options', 'nosniff');

        // Only send Strict-Transport-Security in production with HTTPS
        if (config('app.env') === 'production' && request()->secure()) {
            $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Cache control headers
        $response->header('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Expires', '0');

        return $response;
    }
}
