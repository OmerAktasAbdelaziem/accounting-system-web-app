<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * RateLimitMiddleware - Implements request rate limiting to prevent abuse
 * 
 * Supports multiple rate limit strategies:
 * - Login attempts: 5 per minute per IP
 * - API endpoints: 60 per minute per user
 * - Signup/register: 3 per hour per IP
 */
class RateLimitMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $limit = 'default'): Response
    {
        $key = $this->getThrottleKey($request, $limit);
        $maxAttempts = $this->getMaxAttempts($limit);
        $decaySeconds = $this->getDecaySeconds($limit);

        $current = Cache::get($key, 0);

        if ($current >= $maxAttempts) {
            return response()->json([
                'success' => false,
                'message' => 'Too many requests. Please try again in ' . $decaySeconds . ' seconds.',
            ], 429);
        }

        Cache::put($key, $current + 1, $decaySeconds);

        return $next($request);
    }

    /**
     * Get the throttle key for the request
     */
    protected function getThrottleKey(Request $request, string $limit): string
    {
        $identifier = $this->getIdentifier($request);

        return "rate_limit:{$limit}:{$identifier}";
    }

    /**
     * Get the identifier for rate limiting
     * Uses authenticated user ID or IP address
     */
    protected function getIdentifier(Request $request): string
    {
        if ($request->user()) {
            return 'user_' . $request->user()->id;
        }

        return 'ip_' . $request->ip();
    }

    /**
     * Get max attempts for the limit type
     */
    protected function getMaxAttempts(string $limit): int
    {
        $limits = [
            'login' => 5,           // 5 attempts per minute
            'signup' => 3,          // 3 attempts per hour
            'api' => 60,            // 60 requests per minute
            'default' => 60,
        ];

        return $limits[$limit] ?? 60;
    }

    /**
     * Get decay time in seconds for the limit type
     */
    protected function getDecaySeconds(string $limit): int
    {
        $decays = [
            'login' => 60,          // 1 minute
            'signup' => 3600,       // 1 hour
            'api' => 60,            // 1 minute
            'default' => 60,
        ];

        return $decays[$limit] ?? 60;
    }
}
