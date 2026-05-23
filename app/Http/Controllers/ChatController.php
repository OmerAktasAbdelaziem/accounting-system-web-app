<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ChatController extends Controller
{
    public function contacts(Request $request): JsonResponse
    {
        $user = $request->user();
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

        if ($contact === 'support') {
            if ($user->isSuperAdmin()) {
                abort(403);
            }

            $superAdminIds = $this->activeSuperAdminIds();

            ChatMessage::query()
                ->whereIn('sender_id', $superAdminIds)
                ->where('recipient_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            $messages = ChatMessage::query()
                ->where(function ($query) use ($user, $superAdminIds) {
                    $query->where('sender_id', $user->id)
                        ->whereIn('recipient_id', $superAdminIds);
                })
                ->orWhere(function ($query) use ($user, $superAdminIds) {
                    $query->whereIn('sender_id', $superAdminIds)
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

            $superAdmins = User::query()
                ->where('user_type', 'super_admin')
                ->where('is_active', true)
                ->orderBy('id')
                ->get();

            if ($superAdmins->isEmpty()) {
                abort(422, 'No support users are available.');
            }

            $createdMessages = $superAdmins->map(function (User $superAdmin) use ($user, $messageText) {
                return ChatMessage::create([
                    'sender_id' => $user->id,
                    'recipient_id' => $superAdmin->id,
                    'message' => $messageText,
                ]);
            });

            return response()->json([
                'message' => $this->messagePayload($createdMessages->first()),
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
        $validated = $request->validate([
            'contact_id' => 'required',
        ]);

        if ($validated['contact_id'] === 'support') {
            if ($user->isSuperAdmin()) {
                abort(403);
            }

            ChatMessage::query()
                ->whereIn('sender_id', $this->activeSuperAdminIds())
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
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

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
            ->whereIn('sender_id', $this->activeSuperAdminIds())
            ->where('recipient_id', $user->id)
            ->whereNull('read_at')
            ->count();

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
                    'meta' => 'Connect with super admin',
                    'unread_count' => $supportUnread,
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

    private function supportLastMessage(User $user): ?array
    {
        $message = ChatMessage::query()
            ->whereIn('sender_id', $this->activeSuperAdminIds())
            ->where('recipient_id', $user->id)
            ->orWhere(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->whereIn('recipient_id', $this->activeSuperAdminIds());
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
            'read_at' => $message->read_at?->toISOString(),
            'created_at' => $message->created_at?->toISOString(),
        ];
    }
}
