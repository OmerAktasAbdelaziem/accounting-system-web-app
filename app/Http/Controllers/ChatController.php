<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Throwable;

class ChatController extends Controller
{
    public function contacts(Request $request): JsonResponse
    {
        $user = $request->user();
        $this->touchLastSeen($user);
        $sections = $this->contactSections($user);

        $unreadTotal = ChatMessage::query()
            ->where('recipient_id', $user->id)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'sections' => $sections,
            'unread_total' => $unreadTotal,
        ]);
    }

    public function messages(Request $request, string $contact): JsonResponse
    {
        $user = $request->user();
        $this->touchLastSeen($user);

        if ($contact === 'support') {
            if ($user->isSuperAdmin()) {
                abort(403);
            }

            $superAdmin = $this->supportRecipient($user);

            if (! $superAdmin) {
                return response()->json(['messages' => []]);
            }

            ChatMessage::query()
                ->where('sender_id', $superAdmin->id)
                ->where('recipient_id', $user->id)
                ->whereNull('delivered_at')
                ->update(['delivered_at' => now()]);

            ChatMessage::query()
                ->where('sender_id', $superAdmin->id)
                ->where('recipient_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            $messages = ChatMessage::query()
                ->where(function ($query) use ($user, $superAdmin) {
                    $query->where('sender_id', $user->id)
                        ->where('recipient_id', $superAdmin->id);
                })
                ->orWhere(function ($query) use ($user, $superAdmin) {
                    $query->where('sender_id', $superAdmin->id)
                        ->where('recipient_id', $user->id);
                })
                ->latest('id')
                ->take(100)
                ->get()
                ->reverse()
                ->values()
                ->map(fn (ChatMessage $message) => $this->messagePayload($message));

            return response()->json(['messages' => $messages]);
        }

        if (str_starts_with($contact, 'employee:')) {
            $employee = Employee::query()->findOrFail((int) substr($contact, 9));
            $contactUser = $this->resolveEmployeeUser($employee);

            // If the employee has no linked chat user, return an empty conversation
            // so the frontend can show a friendly placeholder instead of a 403.
            if (! $contactUser) {
                return response()->json(['messages' => []]);
            }

            if (! $this->canChatWith($user, $contactUser)) {
                abort(403);
            }

            $this->markConversationDeliveredAndRead($user, $contactUser);

            $messages = $this->conversationMessages($user, $contactUser);

            return response()->json(['messages' => $messages]);
        }

        $contactUser = User::findOrFail((int) $contact);

        if (! $this->canChatWith($user, $contactUser)) {
            abort(403);
        }

        ChatMessage::query()
            ->where('sender_id', $contactUser->id)
            ->where('recipient_id', $user->id)
            ->whereNull('delivered_at')
            ->update(['delivered_at' => now()]);

        ChatMessage::query()
            ->where('sender_id', $contactUser->id)
            ->where('recipient_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = ChatMessage::query()
            ->where(function ($query) use ($user, $contactUser) {
                $query->where('sender_id', $user->id)
                    ->where('recipient_id', $contactUser->id);
            })
            ->orWhere(function ($query) use ($user, $contactUser) {
                $query->where('sender_id', $contactUser->id)
                    ->where('recipient_id', $user->id);
            })
            ->latest('id')
            ->take(100)
            ->get()
            ->reverse()
            ->values()
            ->map(fn (ChatMessage $message) => $this->messagePayload($message));

        return response()->json(['messages' => $messages]);
    }

    public function send(Request $request): JsonResponse
    {
        $user = $request->user();
        $this->touchLastSeen($user);

        Log::info('chat.send.enter', [
            'user_id' => $user?->id ?? null,
            'route' => $request->path(),
            'method' => $request->method(),
            'payload' => $request->all(),
            'headers' => [
                'x-csrf-token' => $request->header('X-CSRF-TOKEN'),
                'cookie' => $request->header('Cookie'),
            ],
        ]);

        try {

        $validated = $request->validate([
            'recipient_id' => 'nullable',
            'recipient_type' => 'nullable|in:user,employee,support',
            'message' => 'required|string|max:2000',
        ]);

        $messageText = trim($validated['message']);

        if (($validated['recipient_type'] ?? 'user') === 'support') {
            if ($user->isSuperAdmin()) {
                abort(403);
            }

            $superAdmin = $this->supportRecipient($user);

            if (! $superAdmin) {
                abort(422, 'No support users are available.');
            }

            $createdMessage = ChatMessage::create([
                'sender_id' => $user->id,
                'recipient_id' => $superAdmin->id,
                'message' => $messageText,
            ]);
            // notify Telegram
            try {
                $this->notifyTelegram('chat.send.success', [
                    'user_id' => $user->id,
                    'recipient_type' => 'support',
                    'recipient_id' => null,
                    'recipient_resolved_id' => $superAdmin->id,
                    'message_id' => $createdMessage->id,
                    'message' => $messageText,
                ]);
            } catch (Throwable $ignored) {
                Log::warning('telegram.notify.failed', ['reason' => $ignored->getMessage()]);
            }

            return response()->json([
                'message' => $this->messagePayload($createdMessage),
            ]);
        }

        if (($validated['recipient_type'] ?? 'user') === 'employee') {
            $employee = Employee::query()->findOrFail((int) $validated['recipient_id']);
            $recipient = $this->resolveEmployeeUser($employee);

            if (! $recipient || ! $this->canChatWith($user, $recipient)) {
                abort(422, 'Selected employee is not linked to a chat account.');
            }

            $chatMessage = ChatMessage::create([
                'sender_id' => $user->id,
                'recipient_id' => $recipient->id,
                'message' => $messageText,
            ]);
            try {
                $this->notifyTelegram('chat.send.success', [
                    'user_id' => $user->id,
                    'recipient_type' => 'employee',
                    'recipient_id' => $validated['recipient_id'] ?? null,
                    'recipient_resolved_id' => $recipient->id,
                    'message_id' => $chatMessage->id,
                    'message' => $messageText,
                ]);
            } catch (Throwable $ignored) {
                Log::warning('telegram.notify.failed', ['reason' => $ignored->getMessage()]);
            }

            return response()->json([
                'message' => $this->messagePayload($chatMessage),
            ]);
        }

        $recipient = User::findOrFail((int) $validated['recipient_id']);

        if (! $this->canChatWith($user, $recipient)) {
            abort(403);
        }

        $chatMessage = ChatMessage::create([
            'sender_id' => $user->id,
            'recipient_id' => $recipient->id,
            'message' => $messageText,
        ]);
        $response = response()->json([
            'message' => $this->messagePayload($chatMessage),
        ]);

        // Send Telegram notification for successful message
        try {
            $this->notifyTelegram('chat.send.success', [
                'user_id' => $user->id,
                'recipient_type' => $validated['recipient_type'] ?? 'user',
                'recipient_id' => $validated['recipient_id'] ?? null,
                'recipient_resolved_id' => $recipient->id ?? null,
                'message_id' => $chatMessage->id,
                'message' => $messageText,
            ]);
        } catch (Throwable $ignored) {
            Log::warning('telegram.notify.failed', ['reason' => $ignored->getMessage()]);
        }

        return $response;
        } catch (Throwable $e) {
            // Notify Telegram about the error
            try {
                $this->notifyTelegram('chat.send.error', [
                    'user_id' => $request->user()?->id ?? null,
                    'payload' => $request->all(),
                    'error' => $e->getMessage(),
                    'trace' => substr($e->getTraceAsString(), 0, 1500),
                ]);
            } catch (Throwable $ignored) {
                Log::error('telegram.notify.error', ['reason' => $ignored->getMessage()]);
            }

            throw $e;
        }
    }

    private function notifyTelegram(string $title, array $data): void
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        if (empty($token) || empty($chatId)) {
            return;
        }

        $text = "[$title]\n" . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        try {
            // In local dev environments with self-signed certs, disable SSL verification.
            Http::withoutVerifying()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML',
            ]);
        } catch (Throwable $e) {
            Log::error('telegram.send.failed', ['error' => $e->getMessage()]);
        }
    }

    public function markRead(Request $request): JsonResponse
    {
        $user = $request->user();
        $this->touchLastSeen($user);
        $validated = $request->validate([
            'contact_id' => 'required',
        ]);

        if ($validated['contact_id'] === 'support') {
            if ($user->isSuperAdmin()) {
                abort(403);
            }

            $superAdmin = $this->supportRecipient($user);
            if (! $superAdmin) {
                return response()->json(['ok' => true]);
            }

            ChatMessage::query()
                ->where('sender_id', $superAdmin->id)
                ->where('recipient_id', $user->id)
                ->whereNull('delivered_at')
                ->update(['delivered_at' => now()]);

            ChatMessage::query()
                ->where('sender_id', $superAdmin->id)
                ->where('recipient_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return response()->json(['ok' => true]);
        }

        if (str_starts_with((string) $validated['contact_id'], 'employee:')) {
            $employee = Employee::query()->findOrFail((int) substr((string) $validated['contact_id'], 9));
            $contact = $this->resolveEmployeeUser($employee);

            if (! $contact || ! $this->canChatWith($user, $contact)) {
                abort(403);
            }

            $this->markConversationDeliveredAndRead($user, $contact);

            return response()->json(['ok' => true]);
        }

        $contact = User::findOrFail((int) $validated['contact_id']);

        if (! $this->canChatWith($user, $contact)) {
            abort(403);
        }

        ChatMessage::query()
            ->where('sender_id', $contact->id)
            ->where('recipient_id', $user->id)
            ->whereNull('delivered_at')
            ->update(['delivered_at' => now()]);

        ChatMessage::query()
            ->where('sender_id', $contact->id)
            ->where('recipient_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }

    public function typing(Request $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'contact_id' => 'required',
            'typing' => 'boolean',
        ]);

        $isTyping = $request->boolean('typing', true);
        $recipientKey = $validated['contact_id'];

        if ($recipientKey !== 'support') {
            $recipient = User::findOrFail((int) $recipientKey);

            if (! $this->canChatWith($user, $recipient)) {
                abort(403);
            }

            $recipientKey = (string) $recipient->id;
        }

        Cache::put($this->typingCacheKey($user->id, $recipientKey), $isTyping ? 1 : 0, now()->addSeconds(5));

        return response()->json(['ok' => true]);
    }

    private function contactSections(User $user): array
    {
        if ($user->isSuperAdmin()) {
            $merchants = User::query()
                ->with('merchant')
                ->where('user_type', 'merchant_admin')
                ->whereNotNull('merchant_id')
                ->where('is_active', true)
                ->where('users.id', '!=', $user->id)
                ->orderBy('name')
                ->get()
                ->map(fn (User $contact) => $this->contactPayload($user, $contact));

            return [[
                'key' => 'merchants',
                'label' => 'Merchants',
                'items' => $merchants->values(),
            ]];
        }

        $employees = Employee::query()
            ->where('merchant_id', $user->merchant_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn (Employee $employee) => $this->employeeContactPayload($user, $employee))
            ->values();

        $supportRecipient = $this->supportRecipient($user);
        $supportUnread = $supportRecipient
            ? ChatMessage::query()
                ->where('sender_id', $supportRecipient->id)
                ->where('recipient_id', $user->id)
                ->whereNull('read_at')
                ->count()
            : 0;

        return [
            [
                'key' => 'employees',
                'label' => 'Employees',
                'items' => $employees->values(),
            ],
            [
                'key' => 'support',
                'label' => 'Support System',
                'items' => [[
                    'id' => 'support',
                    'kind' => 'support',
                    'name' => 'Support System',
                    'meta' => $supportRecipient ? 'Connected to ' . ($supportRecipient->name ?? 'Support') : 'No support agent available',
                    'unread_count' => $supportUnread,
                    'is_typing' => $supportRecipient ? $this->isTyping($supportRecipient->id, (string) $user->id) : false,
                    'is_online' => $supportRecipient ? $this->isOnline($supportRecipient) : false,
                    'last_seen_at' => $supportRecipient?->last_seen_at?->toISOString(),
                    'last_message' => $this->supportLastMessage($user),
                ]],
            ],
        ];
    }

    private function employeeContactPayload(User $user, Employee $employee): array
    {
        $employeeUser = $this->resolveEmployeeUser($employee);
        $lastMessage = $employeeUser ? $this->lastMessageBetween($user, $employeeUser) : null;

        return [
            'id' => $employee->id,
            'kind' => 'employee',
            'name' => $employee->name,
            'meta' => $employee->position ?? 'Employee',
            'user_type' => 'employee',
            'merchant_id' => $employee->merchant_id,
            'merchant_name' => $user->merchant?->business_name,
            'is_linked' => (bool) $employeeUser,
            'recipient_user_id' => $employeeUser?->id,
            'unread_count' => $employeeUser ? $this->unreadCountBetween($employeeUser->id, $user->id) : 0,
            'is_typing' => $employeeUser ? $this->isTyping($employeeUser->id, (string) $user->id) : false,
            'is_online' => $employeeUser ? $this->isOnline($employeeUser) : false,
            'last_seen_at' => $employeeUser?->last_seen_at?->toISOString(),
            'last_message' => $this->messagePayload($lastMessage),
        ];
    }

    private function contactPayload(User $user, User $contact): array
    {
        $lastMessage = ChatMessage::query()
            ->where(function ($query) use ($user, $contact) {
                $query->where('sender_id', $user->id)->where('recipient_id', $contact->id);
            })
            ->orWhere(function ($query) use ($user, $contact) {
                $query->where('sender_id', $contact->id)->where('recipient_id', $user->id);
            })
            ->latest('id')
            ->first();

        $unreadCount = ChatMessage::query()
            ->where('sender_id', $contact->id)
            ->where('recipient_id', $user->id)
            ->whereNull('read_at')
            ->count();

        return [
            'id' => $contact->id,
            'kind' => 'user',
            'name' => $user->isSuperAdmin() ? ($contact->merchant?->business_name ?? $contact->name) : $contact->name,
            'meta' => $user->isSuperAdmin() ? 'Merchant' : ($contact->position ?? $contact->user_type),
            'user_type' => $contact->user_type,
            'merchant_id' => $contact->merchant_id,
            'merchant_name' => $contact->merchant?->business_name,
            'unread_count' => $unreadCount,
            'is_typing' => $this->isTyping($contact->id, (string) $user->id),
            'is_online' => $this->isOnline($contact),
            'last_seen_at' => $contact->last_seen_at?->toISOString(),
            'last_message' => $this->messagePayload($lastMessage),
        ];
    }

    private function canChatWith(User $user, User $target): bool
    {
        if ($user->id === $target->id) {
            return false;
        }

        if (! $target->is_active) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return ! $target->isSuperAdmin() && ! empty($target->merchant_id);
        }

        if ($user->isMerchantAdmin()) {
            return $target->merchant_id === $user->merchant_id
                && in_array($target->user_type, ['merchant_admin', 'employee', 'viewer'], true);
        }

        return $target->merchant_id === $user->merchant_id
            && $target->user_type === 'merchant_admin';
    }

    private function touchLastSeen(User $user): void
    {
        $user->forceFill(['last_seen_at' => now()])->save();
    }

    private function isOnline(User $user): bool
    {
        if ($user->last_seen_at === null) {
            return false;
        }

        return $user->last_seen_at->greaterThanOrEqualTo(now()->subSeconds(45));
    }

    private function isTyping(int $senderId, string $recipientId): bool
    {
        return Cache::get($this->typingCacheKey($senderId, $recipientId), 0) === 1;
    }

    private function typingCacheKey(int $senderId, string $recipientId): string
    {
        return 'chat.typing.' . $senderId . '.to.' . $recipientId;
    }

    private function supportRecipient(User $user): ?User
    {
        $merchant = $user->merchant()->with('superAdmin')->first();
        $merchantSuperAdmin = $merchant?->superAdmin;

        if ($merchantSuperAdmin && $merchantSuperAdmin->isSuperAdmin() && $merchantSuperAdmin->is_active) {
            return $merchantSuperAdmin;
        }

        return User::query()
            ->where('user_type', 'super_admin')
            ->where('is_active', true)
            ->orderBy('id')
            ->first();
    }

    private function supportLastMessage(User $user): ?array
    {
        $superAdmin = $this->supportRecipient($user);

        if (! $superAdmin) {
            return null;
        }

        $message = ChatMessage::query()
            ->where(function ($query) use ($user, $superAdmin) {
                $query->where('sender_id', $superAdmin->id)
                    ->where('recipient_id', $user->id);
            })
            ->orWhere(function ($query) use ($user, $superAdmin) {
                $query->where('sender_id', $user->id)
                    ->where('recipient_id', $superAdmin->id);
            })
            ->latest('id')
            ->first();

        return $this->messagePayload($message);
    }

    private function resolveEmployeeUser(Employee $employee): ?User
    {
        if (empty($employee->email)) {
            return null;
        }

        return User::query()
            ->where('merchant_id', $employee->merchant_id)
            ->where('user_type', 'employee')
            ->where('email', $employee->email)
            ->first();
    }

    private function lastMessageBetween(User $left, User $right): ?ChatMessage
    {
        return ChatMessage::query()
            ->where(function ($query) use ($left, $right) {
                $query->where('sender_id', $left->id)
                    ->where('recipient_id', $right->id);
            })
            ->orWhere(function ($query) use ($left, $right) {
                $query->where('sender_id', $right->id)
                    ->where('recipient_id', $left->id);
            })
            ->latest('id')
            ->first();
    }

    private function conversationMessages(User $left, User $right)
    {
        return ChatMessage::query()
            ->where(function ($query) use ($left, $right) {
                $query->where('sender_id', $left->id)
                    ->where('recipient_id', $right->id);
            })
            ->orWhere(function ($query) use ($left, $right) {
                $query->where('sender_id', $right->id)
                    ->where('recipient_id', $left->id);
            })
            ->latest('id')
            ->take(100)
            ->get()
            ->reverse()
            ->values()
            ->map(fn (ChatMessage $message) => $this->messagePayload($message));
    }

    private function unreadCountBetween(int $senderId, int $recipientId): int
    {
        return ChatMessage::query()
            ->where('sender_id', $senderId)
            ->where('recipient_id', $recipientId)
            ->whereNull('read_at')
            ->count();
    }

    private function markConversationDeliveredAndRead(User $recipient, User $sender): void
    {
        ChatMessage::query()
            ->where('sender_id', $sender->id)
            ->where('recipient_id', $recipient->id)
            ->whereNull('delivered_at')
            ->update(['delivered_at' => now()]);

        ChatMessage::query()
            ->where('sender_id', $sender->id)
            ->where('recipient_id', $recipient->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    private function messagePayload(?ChatMessage $message): ?array
    {
        if (! $message) {
            return null;
        }

        return [
            'id' => $message->id,
            'sender_id' => $message->sender_id,
            'recipient_id' => $message->recipient_id,
            'message' => $message->message,
            'delivered_at' => $message->delivered_at?->toISOString(),
            'read_at' => $message->read_at?->toISOString(),
            'created_at' => $message->created_at?->toISOString(),
        ];
    }
}
