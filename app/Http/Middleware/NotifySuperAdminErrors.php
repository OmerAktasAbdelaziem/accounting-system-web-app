<?php

namespace App\Http\Middleware;

use App\Services\TelegramService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class NotifySuperAdminErrors
{
    public function __construct(protected TelegramService $telegramService)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        try {
            $response = $next($request);

            if ($this->isSuperAdminRequest($request) && $response->getStatusCode() >= 400) {
                $this->notify($request, $response->getStatusCode(), null);
            }

            return $response;
        } catch (Throwable $e) {
            if ($this->isSuperAdminRequest($request)) {
                $this->notify($request, $this->resolveStatusCode($e), $e);
            }

            throw $e;
        }
    }

    private function isSuperAdminRequest(Request $request): bool
    {
        $routeName = optional($request->route())->getName();

        return str_starts_with((string) $routeName, 'super-admin.')
            || $request->is('super-admin*');
    }

    private function resolveStatusCode(Throwable $e): int
    {
        if (method_exists($e, 'getStatusCode')) {
            try {
                return (int) $e->getStatusCode();
            } catch (Throwable $ignored) {
                // fall through to default
            }
        }

        return 500;
    }

    private function notify(Request $request, int $statusCode, ?Throwable $exception): void
    {
        $route = $request->route();
        $context = [
            'area' => 'super-admin',
            'route' => $route?->getName() ?? $request->path(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'status' => $statusCode,
            'user' => auth()->check() ? auth()->user()->email : 'Guest',
            'ip' => $request->ip(),
        ];

        if ($exception) {
            $this->telegramService->notifyHttpError($statusCode, $exception, $context);
            return;
        }

        $this->telegramService->notifyError(
            "Super Admin page error ({$statusCode})",
            $request->path(),
            $context
        );
    }
}