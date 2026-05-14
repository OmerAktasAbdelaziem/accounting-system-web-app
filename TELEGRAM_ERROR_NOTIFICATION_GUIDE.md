# Telegram Error Notification System - Implementation Guide

## Overview

The system now automatically sends **ALL error notifications** to Telegram, including:
- 🔴 **HTTP 500 errors** (Server errors)
- 🟠 **HTTP 503 errors** (Service unavailable)
- 🟡 **HTTP 400-499 errors** (Client errors)
- ⚠️ **Exceptions and runtime errors**
- 📝 **Detailed error reports with system diagnostics**

## Features

### 1. Automatic Error Detection
- **Global Exception Handler** catches all unhandled errors
- **HTTP Status Detection** identifies error severity
- **Smart Routing** sends appropriate notification type based on error

### 2. Rich Error Information
Each error notification includes:
- ✅ Error type and exception class
- ✅ Exact file path and line number
- ✅ Error message with context
- ✅ URL, method, IP, and authenticated user
- ✅ Stack trace (first 2 frames in production)
- ✅ **Suggested solutions** based on error type
- ✅ **System diagnostics** (PHP version, Laravel version, memory usage, etc.)

### 3. Intelligent Solutions
The system analyzes exceptions and suggests solutions for:
- 🗄️ **Database Errors**: Connection, migrations, column names
- 🔍 **Model Errors**: Missing resources, findOrFail alternatives
- ⚙️ **Method Errors**: Missing methods, spelling, inheritance
- 📄 **File Errors**: Path verification, permissions, existence
- ✔️ **Validation Errors**: Validation rules, field requirements
- 🔐 **Auth Errors**: Login requirements, permissions, middleware
- 🐍 **Syntax Errors**: Parse errors, missing quotes/braces
- ⏱️ **Timeout Errors**: Network, configuration, external services

## How It Works

### 1. Exception Handler (app/Exceptions/Handler.php)
```
Any Error/Exception Occurs
    ↓
Global Exception Handler catches it
    ↓
Detects HTTP status code (500, 404, etc.)
    ↓
Sends to TelegramService with appropriate method
    ↓
User receives detailed Telegram notification
```

### 2. TelegramService (app/Services/TelegramService.php)
Methods available:
- `notifyException()` - General exceptions
- `notifyHttpError()` - HTTP errors (500, 404, etc.) with suggestions
- `notifyError()` - Custom errors
- `notifyDetailedError()` - Detailed reports with system info
- `sendMessage()` - Raw message sending

### 3. Error Formatting
Each notification includes:
- **Icon** indicating severity (🔴 for 500, ⚠️ for 400, etc.)
- **Timestamp** of when error occurred
- **App environment** (local, production, testing)
- **Formatted code blocks** for easy reading
- **HTML styling** for clear visual hierarchy

## Testing the System

### Quick Test Routes (Local Only)
Visit these URLs to test different error types:

1. **Test Basic Error**
   ```
   http://localhost:8000/super-admin/telegram-test/error
   ```
   Sends a simple error notification

2. **Test Exception**
   ```
   http://localhost:8000/super-admin/telegram-test/exception
   ```
   Throws an exception and notifies

3. **Test 500 Error**
   ```
   http://localhost:8000/super-admin/telegram-test/500-error
   ```
   Simulates a 500 error with database context

4. **Test 404 Error**
   ```
   http://localhost:8000/super-admin/telegram-test/404-error
   ```
   Simulates a 404 not found error

5. **Test Detailed Error**
   ```
   http://localhost:8000/super-admin/telegram-test/detailed-error
   ```
   Sends detailed error with system diagnostics

6. **Trigger Real Error**
   ```
   http://localhost:8000/super-admin/telegram-test/real-error
   ```
   Triggers an actual exception (best test)

### Manual Test Command
```bash
php artisan telegram:test-errors error
php artisan telegram:test-errors exception
php artisan telegram:test-errors http500
php artisan telegram:test-errors http404
php artisan telegram:test-errors detailed
```

## Example Error Messages

### 500 Error Example
```
🔴 HTTP 500 Error
━━━━━━━━━━━━━━━━━━━━━━━━━━
📱 App: Aktaš System (production)
⏰ Time: 2026-05-15 14:32:10
📄 Status: 500 - Internal Server Error
🔗 URL: http://localhost:8000/super-admin/merchants
📌 Method: POST
👤 User: admin@aktas.com
🌐 IP: 192.168.1.100

🔗 Exception Details:
• Class: PDOException
• File: app/Models/Merchant.php
• Line: 42
• Message: SQLSTATE[HY000]: General error: 1 table merchants has no column named...

💡 Possible Solutions:
✓ Check database connection and credentials in .env file
✓ Verify database server is running
✓ Check if migrations have been run: php artisan migrate
✓ Check table and column names in your query

📚 Stack Trace (First 2 frames):
  [0] PDO->prepare() at database/mysql/grammar.php:129
  [1] Query->prepare() at database/processor.php:42

⚙️ System Info:
• php_version: 8.2.12
• laravel_version: 11.12.58.0
• memory_usage: 45.32 MB
• memory_limit: 256M
• max_execution_time: 30s
• environment: production
• debug_mode: Disabled
• database: sqlite
• cache: redis
• queue: database
```

## Configuration

The Telegram bot configuration is in `config/telegram.php`:

```php
return [
    'enabled' => env('TELEGRAM_ENABLED', true),
    'token' => env('TELEGRAM_BOT_TOKEN'),
    'chat_id' => env('TELEGRAM_CHAT_ID'),
    'notifications' => [
        'errors' => env('TELEGRAM_NOTIFY_ERRORS', true),
        'exceptions' => env('TELEGRAM_NOTIFY_EXCEPTIONS', true),
        'submissions' => env('TELEGRAM_NOTIFY_SUBMISSIONS', false),
        'auth' => env('TELEGRAM_NOTIFY_AUTH', true),
    ],
];
```

### Environment Variables (.env)
```
TELEGRAM_ENABLED=true
TELEGRAM_BOT_TOKEN=your_bot_token_here
TELEGRAM_CHAT_ID=your_chat_id_here
TELEGRAM_NOTIFY_ERRORS=true
TELEGRAM_NOTIFY_EXCEPTIONS=true
TELEGRAM_NOTIFY_SUBMISSIONS=true
TELEGRAM_NOTIFY_AUTH=true
```

## When Errors Are Sent to Telegram

### Automatic (No Configuration Needed)
✅ **Unhandled Exceptions** - Any uncaught exception
✅ **HTTP 500 Errors** - Server errors
✅ **HTTP 400-499 Errors** - Client errors
✅ **Database Connection Errors** - Connection failures
✅ **Route Not Found Errors** - 404 errors
✅ **Validation Errors** - Field validation failures
✅ **Authentication Errors** - Auth failures

### Manual (Use in Controllers)
```php
use App\Services\TelegramService;

// In your controller
public function someMethod(TelegramService $telegram)
{
    try {
        // Your code
    } catch (\Exception $e) {
        // Send detailed error
        $telegram->notifyDetailedError($e, 'Custom Title', [
            'action' => 'Creating merchant',
            'data' => $data,
        ]);
        
        // Or send simple error
        $telegram->notifyError('Title', 'Message', ['context' => 'data']);
    }
}
```

## Key Files Modified

1. **app/Exceptions/Handler.php**
   - Added HTTP status detection
   - Added smart routing based on error type
   - Integrated TelegramService

2. **app/Services/TelegramService.php**
   - Added `notifyHttpError()` method
   - Added `getSolutionsForException()` method
   - Added `getSystemDiagnostics()` method
   - Added `notifyDetailedError()` method
   - Enhanced `formatHttpErrorMessage()` with suggestions

3. **app/Http/Controllers/SuperAdmin/TelegramTestController.php**
   - Created test controller for various error types
   - Includes methods to trigger different error scenarios

4. **routes/web.php**
   - Added test routes under `/super-admin/telegram-test/*`
   - Routes only available in local environment

5. **app/Console/Commands/TestTelegramErrors.php**
   - Created artisan command for testing
   - Can test different error types

## Error Solutions Database

The system includes intelligent error detection for:

| Error Type | Example | Suggested Solutions |
|------------|---------|-------------------|
| PDOException | Database connection failed | Check .env, verify server, run migrations |
| ModelNotFoundException | Model not found | Check resource ID, use findOrFail |
| BadMethodCallException | Method doesn't exist | Check method name, verify class |
| FileNotFoundException | File not found | Check path, permissions, existence |
| ValidationException | Validation failed | Check rules, verify fields |
| AuthenticationException | Auth failed | Ensure logged in, check permissions |
| ParseError | Syntax error | Check syntax, PHP -l filename |
| Timeout | Request timeout | Check network, increase timeout |

## Production Considerations

1. **Debug Mode** - Errors are always sent regardless of `APP_DEBUG`
2. **Environment** - Test routes only work in `local` environment
3. **Rate Limiting** - Consider implementing to prevent spam
4. **Sensitive Data** - Ensure `.env` is never exposed in stack traces
5. **Performance** - Telegram notifications are sent asynchronously

## Troubleshooting

### Errors Not Appearing in Telegram
1. Check `TELEGRAM_ENABLED=true` in .env
2. Verify `TELEGRAM_BOT_TOKEN` is correct
3. Verify `TELEGRAM_CHAT_ID` is correct
4. Check Laravel logs in `storage/logs/laravel.log`
5. Ensure bot has permission to send messages to chat

### Only Seeing Login/Logout Notifications
- Previous version only sent auth events
- Now sends ALL errors automatically
- Test with `/super-admin/telegram-test/real-error`

### Test Routes Not Working
- Routes only available in `local` environment
- Check if `APP_ENV=local` in .env
- Routes are under `/super-admin/` (requires super admin auth)

## Future Enhancements

Potential improvements:
- [ ] Error grouping and deduplication
- [ ] Error tracking dashboard
- [ ] Alert severity levels
- [ ] Different Telegram groups for different error types
- [ ] Error history and analytics
- [ ] Automatic error recovery suggestions
- [ ] Performance metrics with errors
- [ ] Integration with external error tracking services

## Summary

The system now provides **comprehensive error monitoring** with:
- ✅ Automatic detection of all errors
- ✅ Detailed information with suggestions
- ✅ System diagnostics for debugging
- ✅ Clean, formatted Telegram messages
- ✅ Easy testing and verification
- ✅ Zero configuration needed
