<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use Symfony\Component\HttpFoundation\Response;

class AuditLoggingMiddleware
{
    /**
     * Handle an incoming request and log it if it's a modification action
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Store the request to log later after the response
        $request->attributes->set('_audit_start_time', microtime(true));
        
        $response = $next($request);

        // Log the action after response is generated
        $this->logAction($request, $response);

        return $response;
    }

    /**
     * Log the action to audit logs
     */
    private function logAction(Request $request, Response $response): void
    {
        // Only log POST, PUT, DELETE, PATCH requests
        if (!in_array($request->method(), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            return;
        }

        // Don't log certain routes
        if ($this->shouldIgnoreRoute($request)) {
            return;
        }

        // Check if settings are enabled for audit logs
        $auditLogsEnabled = \App\Models\Setting::get('enable_audit_logs', true);
        if (!$auditLogsEnabled) {
            return;
        }

        try {
            $action = $this->getAction($request);
            $modelInfo = $this->getModelInfo($request);

            if (!$modelInfo) {
                return;
            }

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'model_type' => $modelInfo['type'],
                'model_id' => $modelInfo['id'],
                'changes' => $modelInfo['changes'] ?? null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Silently fail to avoid breaking the application
            \Log::error('Audit logging failed: ' . $e->getMessage());
        }
    }

    /**
     * Determine if route should be ignored
     */
    private function shouldIgnoreRoute(Request $request): bool
    {
        $ignoredRoutes = [
            'profile.update',
            'change-password',
            'locale.switch',
            'settings.update',
        ];

        $route = $request->route();
        if (!$route) {
            return true;
        }

        return in_array($route->getName(), $ignoredRoutes);
    }

    /**
     * Get the action type (created, updated, deleted)
     */
    private function getAction(Request $request): string
    {
        return match ($request->method()) {
            'POST' => 'created',
            'PUT', 'PATCH' => 'updated',
            'DELETE' => 'deleted',
            default => 'unknown',
        };
    }

    /**
     * Extract model type and ID from request
     */
    private function getModelInfo(Request $request): ?array
    {
        $route = $request->route();
        if (!$route) {
            return null;
        }

        // Try to get model from route parameters
        foreach ($route->parameters() as $name => $value) {
            if (is_object($value) && method_exists($value, 'getTable')) {
                return [
                    'type' => class_basename($value),
                    'id' => $value->id,
                    'changes' => $this->getChanges($request, $value),
                ];
            }
        }

        return null;
    }

    /**
     * Get the changes made to the model
     */
    private function getChanges(Request $request, $model): array
    {
        $changes = [];

        if ($request->method() === 'POST') {
            // For create, all filled values are "new"
            $changes['new'] = $request->except('_token', '_method');
        } elseif ($request->method() === 'PUT' || $request->method() === 'PATCH') {
            // For update, track what changed
            $changes['old'] = $model->getOriginal();
            $changes['new'] = $request->except('_token', '_method');
        }

        return $changes;
    }
}
