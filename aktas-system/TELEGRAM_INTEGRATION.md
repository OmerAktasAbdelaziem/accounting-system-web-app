# Telegram Bot Integration Guide

## Overview

This guide documents the Telegram bot integration for real-time error and event notifications. Any errors, exceptions, form submission failures, and authentication events will be automatically sent to your Telegram bot.

## Configuration

### Environment Variables

The following environment variables are configured in `.env`:

```env
# Telegram Bot Configuration
TELEGRAM_ENABLED=true
TELEGRAM_BOT_TOKEN=8349908920:AAEGIy2sUdFRfEnx4ZnXRcXtCSFxdgbIdAM
TELEGRAM_CHAT_ID=5173560887
TELEGRAM_USERNAME=@hanody_systembot
TELEGRAM_NOTIFY_ERRORS=true
TELEGRAM_NOTIFY_EXCEPTIONS=true
TELEGRAM_NOTIFY_SUBMISSIONS=true
TELEGRAM_NOTIFY_AUTH=true
```

- **TELEGRAM_ENABLED**: Enable/disable all Telegram notifications
- **TELEGRAM_BOT_TOKEN**: Telegram bot API token
- **TELEGRAM_CHAT_ID**: Target chat ID for notifications
- **TELEGRAM_USERNAME**: Bot username for reference
- **TELEGRAM_NOTIFY_ERRORS**: Send system errors
- **TELEGRAM_NOTIFY_EXCEPTIONS**: Send exceptions and stack traces
- **TELEGRAM_NOTIFY_SUBMISSIONS**: Send form submission errors
- **TELEGRAM_NOTIFY_AUTH**: Send authentication events (login/logout/failed)

## Features

### 1. Automatic Exception Handling
All uncaught exceptions are automatically sent to Telegram with:
- Exception type and message
- File name and line number
- Request URL and HTTP method
- User IP address and user agent
- Current authenticated user

### 2. Error Logging
System errors (WARNING, ERROR, CRITICAL) are automatically notified with:
- Error level and title
- Error message
- Contextual information

### 3. Form Submission Errors
Form validation errors are tracked with:
- Form name/route
- Validation error messages
- Form data (sanitized)
- Current user email

### 4. Authentication Events
All auth events are tracked:
- **Login**: Successful user login with IP and user agent
- **Logout**: User logout events
- **Failed**: Failed login attempts with credentials attempted
- **Locked**: Account lockout events

## Usage

### Using in Controllers

```php
<?php

namespace App\Http\Controllers;

use App\Support\TelegramNotifiable;
use App\Services\TelegramService;

class MyController extends Controller
{
    use TelegramNotifiable;

    public function store(Request $request, TelegramService $telegramService)
    {
        try {
            // Your code here
            
            $data = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email',
            ]);
            
        } catch (\Exception $e) {
            // Manual notification
            $this->notifyTelegramException($e, [
                'request_data' => $request->all(),
            ]);
            throw $e;
        }
    }
}
```

### Direct Service Usage

```php
use App\Services\TelegramService;

$telegram = app(TelegramService::class);

// Send custom error
$telegram->notifyError(
    'Database Error',
    'Connection failed to MySQL',
    ['host' => 'localhost', 'port' => 3306]
);

// Send custom submission error
$telegram->notifySubmissionError(
    'Invoice Form',
    'Invalid invoice number format',
    $formData
);

// Send auth event
$telegram->notifyAuthEvent(
    'login',
    'user@example.com',
    ['ip' => '192.168.1.1']
);
```

### Using the Trait

Include the `TelegramNotifiable` trait in any class:

```php
use App\Support\TelegramNotifiable;

class MyService
{
    use TelegramNotifiable;

    public function processData()
    {
        try {
            // Your code
        } catch (\Exception $e) {
            $this->notifyTelegramException($e);
        }
    }
}
```

## Message Format

Messages are sent with HTML formatting for better readability:

### Error Message Format
```
🚨 Error Notification
━━━━━━━━━━━━━━━━━━━━
📱 App: Accounting System
⏰ Time: 2026-05-11 10:30:45
❌ Title: Database Error
📝 Message: Connection refused
```

### Exception Message Format
```
🔴 Exception Alert
━━━━━━━━━━━━━━━━━━━━
📱 App: Accounting System (production)
⏰ Time: 2026-05-11 10:30:45
🔗 Exception: PDOException
📄 File: /var/www/html/app/Services/UserService.php
📍 Line: 45
💬 Message: SQLSTATE[HY000]: Connection error
```

### Form Submission Error Format
```
⚠️ Form Submission Error
━━━━━━━━━━━━━━━━━━━━
📱 App: Accounting System
⏰ Time: 2026-05-11 10:30:45
📋 Form: /invoices/store
❌ Error: The email field is required
👤 User: admin@hamit.tech
```

### Auth Event Format
```
✅ Authentication Event
━━━━━━━━━━━━━━━━━━━━
📱 App: Accounting System
⏰ Time: 2026-05-11 10:30:45
🔐 Event: Login
👤 Username: admin@hamit.tech
```

## Security Considerations

1. **Token Protection**: Keep your bot token secure, store it in environment variables only
2. **Sensitive Data**: The service sanitizes passwords and sensitive fields
3. **Rate Limiting**: Telegram API has rate limits; excessive errors will be throttled
4. **Error Details**: In production, detailed error messages are sent with full context
5. **Data Privacy**: Form data is truncated to 100 characters per field for privacy

## Configuration

### Disable Specific Notifications

In `.env`, set any of these to `false` to disable:

```env
TELEGRAM_NOTIFY_ERRORS=false      # Disable error notifications
TELEGRAM_NOTIFY_EXCEPTIONS=false  # Disable exception notifications
TELEGRAM_NOTIFY_SUBMISSIONS=false # Disable form error notifications
TELEGRAM_NOTIFY_AUTH=false        # Disable auth event notifications
```

### Disable All Notifications

```env
TELEGRAM_ENABLED=false
```

## Troubleshooting

### Notifications Not Arriving

1. Check if `TELEGRAM_ENABLED=true` in `.env`
2. Verify bot token and chat ID are correct
3. Check Laravel logs: `storage/logs/laravel.log`
4. Verify Telegram bot has permission to send messages to the chat

### Check Logs

```bash
# View recent errors
tail -f storage/logs/laravel.log

# Filter Telegram errors
grep -i telegram storage/logs/laravel.log
```

### Test Connection

```bash
# SSH to server and test
ssh root@72.62.119.39
docker exec hamit-tech php artisan tinker

# In tinker:
$telegram = app(\App\Services\TelegramService::class);
$telegram->notifyError('Test Error', 'This is a test notification');
```

## Files Modified

- `config/telegram.php` - Configuration file
- `app/Services/TelegramService.php` - Core service
- `app/Exceptions/Handler.php` - Exception handling
- `app/Http/Middleware/HandleFormSubmissionErrors.php` - Form error handling
- `app/Listeners/AuthEventListener.php` - Auth event tracking
- `app/Providers/TelegramServiceProvider.php` - Service provider
- `app/Support/TelegramNotifiable.php` - Helper trait
- `app/Logging/TelegramLogHandler.php` - Log handler
- `.env` - Environment variables updated
- `.env.example` - Example environment variables

## Testing

Test the integration locally:

```bash
php artisan tinker

# Test error notification
\App\Services\TelegramService::notifyError('Test Title', 'Test Message', ['test' => 'data']);

# Test exception notification
try {
    throw new Exception('Test Exception');
} catch (\Exception $e) {
    app(\App\Services\TelegramService::class)->notifyException($e);
}
```

## Support

For issues or questions, check the logs and verify:
1. Network connectivity to Telegram API
2. Environment variables are correctly set
3. Laravel application is running without errors
4. Telegram bot token is valid

---

**Last Updated**: 2026-05-11
**Integration Version**: 1.0
**Support Email**: admin@hamit.tech
