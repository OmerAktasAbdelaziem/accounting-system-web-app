<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Http\Request;

$user = User::query()->first();
if (! $user) {
    echo "No users found\n";
    exit(1);
}

// Prefer a recipient that has a merchant_id (so super_admin can chat with them)
$recipient = User::query()->whereNotNull('merchant_id')->where('id', '!=', $user->id)->first();
if (! $recipient) {
    // fallback: create a recipient with a merchant_id so super_admin can chat
    $recipient = User::create([
        'name' => 'Temp Recipient',
        'email' => 'temp-recipient+' . time() . '@example.com',
        'password' => bcrypt('secret123'),
        'merchant_id' => 1,
        'user_type' => 'merchant_admin',
        'is_active' => true,
    ]);
}

$request = Request::create('/chat/send', 'POST', [
    'recipient_id' => $recipient->id,
    'recipient_type' => 'user',
    'message' => 'test from script',
]);

// set user resolver so controller->user() works
$request->setUserResolver(function () use ($user) {
    return $user;
});

$controller = app(App\Http\Controllers\ChatController::class);
try {
    // Debug output about users and canChat eligibility
    echo "Sender: {$user->id} ({$user->user_type}), merchant_id={$user->merchant_id}\n";
    echo "Recipient: {$recipient->id} ({$recipient->user_type}), merchant_id={$recipient->merchant_id}\n";

    // replicate canChatWith logic
    $canChat = false;
    if ($user->id === $recipient->id) {
        $canChat = false;
    } elseif (! $recipient->is_active) {
        $canChat = false;
    } elseif ($user->isSuperAdmin()) {
        $canChat = ! $recipient->isSuperAdmin() && ! empty($recipient->merchant_id);
    } elseif ($user->isMerchantAdmin()) {
        $canChat = $recipient->merchant_id === $user->merchant_id && in_array($recipient->user_type, ['merchant_admin', 'employee', 'viewer'], true);
    } else {
        $canChat = $recipient->merchant_id === $user->merchant_id && $recipient->user_type === 'merchant_admin';
    }

    echo "canChat? " . ($canChat ? 'yes' : 'no') . "\n";

    if (! $canChat) {
        echo "Skipping send because canChat is false.\n";
        exit(1);
    }

    $response = $controller->send($request);
    echo "Response: \n" . $response->getContent() . "\n";
} catch (Throwable $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

