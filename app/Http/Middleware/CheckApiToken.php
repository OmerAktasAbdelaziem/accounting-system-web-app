<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

/**
 * CheckApiToken Middleware - Validates API token for protected routes
 *
 * Checks for API token in:
 * 1. Authorization header (Bearer token)
 * 2. Query parameter (token)
 * 3. Request body (token field)
 *
 * Sets request user if valid token found
 */
class CheckApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $this->extractToken($request);

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated - No API token provided',
            ], 401);
        }

        // Find user by API token
        $user = User::where('api_token', $token)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated - Invalid API token',
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated - User account is inactive',
            ], 401);
        }

        // Check if token has expired
        if ($user->api_token_expires_at && now()->isAfter($user->api_token_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated - Token has expired. Please login again.',
            ], 401);
        }

        // Set user on request
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }

    /**
     * Extract token from request
     *
     * Looks for token in multiple locations:
     * - Authorization header (Bearer scheme)
     * - Query parameter: ?token=xxx
     * - Request body: {"token": "xxx"}
     *
     * @param Request $request
     * @return string|null
     */
    protected function extractToken(Request $request): ?string
    {
        // Check Authorization header (Bearer scheme)
        $authHeader = $request->header('Authorization');
        if ($authHeader && strpos($authHeader, 'Bearer ') === 0) {
            return substr($authHeader, 7);
        }

        // Check query parameter
        if ($request->has('token')) {
            return $request->query('token');
        }

        // Check request body
        if ($request->has('token')) {
            return $request->input('token');
        }

        return null;
    }
}
