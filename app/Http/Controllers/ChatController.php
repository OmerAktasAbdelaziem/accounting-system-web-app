<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function contacts(Request $request): JsonResponse
    {
        $user = $request->user();
        $contacts = $this->allowedContacts($user);

        $data = $contacts->map(function (User $contact) use ($user) {
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
                'name' => $contact->name,
                'user_type' => $contact->user_type,
                'merchant_id' => $contact->merchant_id,
                'merchant_name' => $contact->merchant?->business_name,
                'unread_count' => $unreadCount,
                'last_message' => $lastMessage ? [
                    'id' => $lastMessage->id,
                    'sender_id' => $lastMessage->sender_id,
                    'recipient_id' => $lastMessage->recipient_id,
                    'message' => $lastMessage->message,
                    'created_at' => $lastMessage->created_at?->toISOString(),
                ] : null,
            ];
        })->sortByDesc(function (array $item) {
            return $item['last_message']['id'] ?? 0;
        })->values();

        $unreadTotal = ChatMessage::query()
            ->where('recipient_id', $user->id)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'contacts' => $data,
            'unread_total' => $unreadTotal,
        ]);
    }

    public function messages(Request $request, User $contact): JsonResponse
    {
        $user = $request->user();

        if (! $this->canChatWith($user, $contact)) {
            abort(403);
        }

        ChatMessage::query()
            ->where('sender_id', $contact->id)
            ->where('recipient_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = ChatMessage::query()
            ->where(function ($query) use ($user, $contact) {
                $query->where('sender_id', $user->id)
                    ->where('recipient_id', $contact->id);
            })
            ->orWhere(function ($query) use ($user, $contact) {
                $query->where('sender_id', $contact->id)
                    ->where('recipient_id', $user->id);
            })
            ->latest('id')
            ->take(100)
            ->get()
            ->reverse()
            ->values()
            ->map(function (ChatMessage $message) {
                return [
                    'id' => $message->id,
                    'sender_id' => $message->sender_id,
                    'recipient_id' => $message->recipient_id,
                    'message' => $message->message,
                    'read_at' => $message->read_at?->toISOString(),
                    'created_at' => $message->created_at?->toISOString(),
                ];
            });

        return response()->json(['messages' => $messages]);
    }

    public function send(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'message' => 'required|string|max:2000',
        ]);

        $recipient = User::findOrFail($validated['recipient_id']);

        if (! $this->canChatWith($user, $recipient)) {
            abort(403);
        }

        $chatMessage = ChatMessage::create([
            'sender_id' => $user->id,
            'recipient_id' => $recipient->id,
            'message' => trim($validated['message']),
        ]);

        return response()->json([
            'message' => [
                'id' => $chatMessage->id,
                'sender_id' => $chatMessage->sender_id,
                'recipient_id' => $chatMessage->recipient_id,
                'message' => $chatMessage->message,
                'created_at' => $chatMessage->created_at?->toISOString(),
            ],
        ]);
    }

    public function markRead(Request $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'contact_id' => 'required|exists:users,id',
        ]);

        $contact = User::findOrFail($validated['contact_id']);

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

    private function allowedContacts(User $user)
    {
        if ($user->isSuperAdmin()) {
            return User::query()
                ->with('merchant')
                ->where('user_type', 'merchant_admin')
                ->whereNotNull('merchant_id')
                ->where('is_active', true)
                ->where('id', '!=', $user->id)
                ->orderBy('name')
                ->get();
        }

        if ($user->isMerchantAdmin()) {
            return User::query()
                ->with('merchant')
                ->where('merchant_id', $user->merchant_id)
                ->whereIn('user_type', ['merchant_admin', 'employee', 'viewer'])
                ->where('is_active', true)
                ->where('id', '!=', $user->id)
                ->orderBy('name')
                ->get();
        }

        return User::query()
            ->with('merchant')
            ->where('merchant_id', $user->merchant_id)
            ->where('user_type', 'merchant_admin')
            ->where('is_active', true)
            ->where('id', '!=', $user->id)
            ->orderBy('name')
            ->get();
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
}
