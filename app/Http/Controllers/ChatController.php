<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

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

        $validated = $request->validate([
            'recipient_id' => 'nullable',
            'recipient_type' => 'nullable|in:user,support',
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

            return response()->json([
                'message' => $this->messagePayload($createdMessage),
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

        return response()->json([
            'message' => $this->messagePayload($chatMessage),
        ]);
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
                ->where('id', '!=', $user->id)
                ->orderBy('name')
                ->get()
                ->map(fn (User $contact) => $this->contactPayload($user, $contact));

            return [[
                'key' => 'merchants',
                'label' => 'Merchants',
                'items' => $merchants->values(),
            ]];
        }

        $employees = User::query()
            ->with('merchant')
            ->where('merchant_id', $user->merchant_id)
            ->where('user_type', 'employee')
            ->where('is_active', true)
            ->where('id', '!=', $user->id)
            ->orderBy('name')
            ->get()
            ->map(fn (User $contact) => $this->contactPayload($user, $contact));

        $supportUnread = ChatMessage::query()
            ->where('sender_id', optional($this->supportRecipient($user))->id)
            ->where('recipient_id', $user->id)
            ->whereNull('read_at')
            ->count();

        $supportRecipient = $this->supportRecipient($user);

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

    private function activeSuperAdminIds(): Collection
    {
        return User::query()
            ->where('user_type', 'super_admin')
            ->where('is_active', true)
            ->pluck('id');
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
