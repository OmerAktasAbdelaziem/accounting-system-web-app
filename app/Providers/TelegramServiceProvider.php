<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Locked;
use App\Services\TelegramService;
use App\Listeners\AuthEventListener;
use App\Logging\TelegramLogHandler;

class TelegramServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(TelegramService::class, function ($app) {
            return new TelegramService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register event listeners
        $this->app['events']->listen(Login::class, [AuthEventListener::class, 'handleLogin']);
        $this->app['events']->listen(Logout::class, [AuthEventListener::class, 'handleLogout']);
        $this->app['events']->listen(Failed::class, [AuthEventListener::class, 'handleFailed']);
        $this->app['events']->listen(Locked::class, [AuthEventListener::class, 'handleLocked']);

        // Register log handler
        $this->app['log']->extend('telegram', function ($app, $config) {
            return new TelegramLogHandler();
        });
    }
}
