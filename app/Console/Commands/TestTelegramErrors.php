<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramService;

class TestTelegramErrors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:test-errors {type?}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Test Telegram error notifications. Types: error, exception, http500, http404, detailed';

    /**
     * Execute the console command.
     */
    public function handle(TelegramService $telegramService)
    {
        $type = $this->argument('type') ?? 'error';

        try {
            match ($type) {
                'error' => $this->testError($telegramService),
                'exception' => $this->testException($telegramService),
                'http500' => $this->testHttp500($telegramService),
                'http404' => $this->testHttp404($telegramService),
                'detailed' => $this->testDetailed($telegramService),
                default => $this->testError($telegramService),
            };

            $this->info("✅ Test message sent for type: {$type}");
        } catch (\Exception $e) {
            $this->error("❌ Failed to send test message: " . $e->getMessage());
        }
    }

    /**
     * Test error notification
     */
    private function testError(TelegramService $telegramService): void
    {
        $telegramService->notifyError(
            'Test Error Notification',
            'This is a test error message to verify Telegram notifications are working correctly.',
            [
                'test_type' => 'error',
                'command' => 'telegram:test-errors',
                'timestamp' => now()->toDateTimeString(),
            ]
        );
    }

    /**
     * Test exception notification
     */
    private function testException(TelegramService $telegramService): void
    {
        try {
            throw new \Exception('This is a test exception for Telegram notifications');
        } catch (\Exception $e) {
            $telegramService->notifyException($e, [
                'test_type' => 'exception',
                'command' => 'telegram:test-errors',
            ]);
        }
    }

    /**
     * Test HTTP 500 error notification
     */
    private function testHttp500(TelegramService $telegramService): void
    {
        try {
            // Simulate a 500 error
            throw new \Exception('Database connection failed: connection refused');
        } catch (\Exception $e) {
            $telegramService->notifyHttpError(500, $e, [
                'url' => url('/test-route'),
                'method' => 'GET',
                'ip' => '127.0.0.1',
                'user' => 'test-user@example.com',
            ]);
        }
    }

    /**
     * Test HTTP 404 error notification
     */
    private function testHttp404(TelegramService $telegramService): void
    {
        try {
            throw new \Exception('Route not found');
        } catch (\Exception $e) {
            $telegramService->notifyHttpError(404, $e, [
                'url' => url('/non-existent-page'),
                'method' => 'GET',
                'ip' => '127.0.0.1',
                'user' => 'Guest',
            ]);
        }
    }

    /**
     * Test detailed error notification
     */
    private function testDetailed(TelegramService $telegramService): void
    {
        try {
            // Create a nested stack trace for more realistic error
            $this->causeDeepError();
        } catch (\Exception $e) {
            $telegramService->notifyDetailedError($e, 'Test Detailed Error Report', [
                'request_data' => 'POST /api/test-endpoint',
                'user_action' => 'Creating new merchant',
                'affected_records' => 'Merchant ID: 123, User ID: 456',
            ]);
        }
    }

    /**
     * Cause a deep error for testing
     */
    private function causeDeepError(): void
    {
        $this->nestedCall1();
    }

    private function nestedCall1(): void
    {
        $this->nestedCall2();
    }

    private function nestedCall2(): void
    {
        throw new \RuntimeException('Simulated database query error at line 42');
    }
}
