<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Locked;
use App\Services\TelegramService;

class AuthEventListener
{
    protected TelegramService $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * Handle login event
     */
    public function handleLogin(Login $event)
    {
        try {
            $this->telegramService->notifyAuthEvent(
                'login',
                $event->user->email ?? 'Unknown',
                [
                    'user_id' => $event->user->id ?? null,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]
            );
        } catch (\Exception $e) {
            \Log::error('Failed to notify login via Telegram', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Handle logout event
     */
    public function handleLogout(Logout $event)
    {
        try {
            $this->telegramService->notifyAuthEvent(
                'logout',
                $event->user->email ?? 'Unknown',
                [
                    'user_id' => $event->user->id ?? null,
                ]
            );
        } catch (\Exception $e) {
            \Log::error('Failed to notify logout via Telegram', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Handle failed login event
     */
    public function handleFailed(Failed $event)
    {
        try {
            $this->telegramService->notifyAuthEvent(
                'failed',
                $event->credentials['email'] ?? 'Unknown',
                [
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]
            );
        } catch (\Exception $e) {
            \Log::error('Failed to notify login failure via Telegram', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Handle locked event
     */
    public function handleLocked(Locked $event)
    {
        try {
            $this->telegramService->notifyAuthEvent(
                'locked',
                $event->user->email ?? 'Unknown',
                [
                    'user_id' => $event->user->id ?? null,
                    'ip_address' => request()->ip(),
                ]
            );
        } catch (\Exception $e) {
            \Log::error('Failed to notify locked account via Telegram', ['error' => $e->getMessage()]);
        }
    }
}
