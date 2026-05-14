<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Routing\Controller;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Http;

class TelegramTestController extends Controller
{
    /**
     * Test error notification
     */
    public function testError(TelegramService $telegramService)
    {
        $telegramService->notifyError(
            'Test Error Notification',
            'This is a test error message to verify Telegram notifications are working correctly.',
            [
                'test_type' => 'error',
                'source' => 'TelegramTestController',
                'timestamp' => now()->toDateTimeString(),
            ]
        );

        return response()->json(['message' => 'Test error notification sent!']);
    }

    /**
     * Test exception notification
     */
    public function testException(TelegramService $telegramService)
    {
        try {
            throw new \Exception('This is a test exception for Telegram notifications - thrown from test controller');
        } catch (\Exception $e) {
            $telegramService->notifyException($e, [
                'test_type' => 'exception',
                'source' => 'TelegramTestController',
                'url' => request()->fullUrl(),
            ]);
        }

        return response()->json(['message' => 'Test exception notification sent!']);
    }

    /**
     * Test HTTP 500 error notification
     */
    public function test500Error(TelegramService $telegramService)
    {
        try {
            throw new \Exception('Database connection failed: SQLSTATE[HY000]: General error: unable to connect to database server');
        } catch (\Exception $e) {
            $telegramService->notifyHttpError(500, $e, [
                'url' => request()->fullUrl(),
                'method' => request()->method(),
                'ip' => request()->ip(),
                'user' => auth()->check() ? auth()->user()->email : 'Guest',
            ]);
        }

        return response()->json(['message' => 'Test 500 error notification sent!']);
    }

    /**
     * Test HTTP 404 error notification
     */
    public function test404Error(TelegramService $telegramService)
    {
        try {
            throw new \Exception('Route not found: /api/missing-endpoint');
        } catch (\Exception $e) {
            $telegramService->notifyHttpError(404, $e, [
                'url' => request()->fullUrl(),
                'method' => request()->method(),
                'ip' => request()->ip(),
                'user' => auth()->check() ? auth()->user()->email : 'Guest',
            ]);
        }

        return response()->json(['message' => 'Test 404 error notification sent!']);
    }

    /**
     * Test detailed error notification
     */
    public function testDetailedError(TelegramService $telegramService)
    {
        try {
            $this->simulateDeepError();
        } catch (\Exception $e) {
            $telegramService->notifyDetailedError($e, '🚨 Critical System Error Detected', [
                'request_endpoint' => '/super-admin/merchants/create',
                'user_action' => 'Creating new merchant with admin users',
                'affected_records' => 'Merchant creation failed',
                'user_email' => auth()->user()?->email ?? 'system',
            ]);
        }

        return response()->json(['message' => 'Test detailed error notification sent!']);
    }

    /**
     * Test direct raw send to Telegram API and return API response
     */
    public function testRawSend()
    {
        $token = config('telegram.token');
        $chat = config('telegram.chat_id');
        $api = config('telegram.api_url', 'https://api.telegram.org');

        if (!$token || !$chat) {
            return response()->json(['ok' => false, 'error' => 'Missing token or chat id in config'], 400);
        }

        try {
            $resp = Http::timeout(10)->post("{$api}/bot{$token}/sendMessage", [
                'chat_id' => $chat,
                'text' => 'Raw test message from local app at ' . now(),
            ]);

            return response($resp->body(), $resp->status())->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }
    /**
     * Simulate a deep error stack
     */
    private function simulateDeepError(): void
    {
        $this->level1();
    }

    private function level1(): void
    {
        $this->level2();
    }

    private function level2(): void
    {
        $this->level3();
    }

    private function level3(): void
    {
        throw new \RuntimeException('Simulated merchant creation error: Unable to assign admin user permissions');
    }

    /**
     * Trigger a real application error
     */
    public function triggerRealError()
    {
        // This will trigger a real error that Laravel's exception handler will catch
        throw new \RuntimeException('🔴 Real application error - Testing automatic Telegram notification');
    }
}
