<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        // If the notifications table doesn't exist (e.g. migrations not run),
        // return an empty response instead of throwing a QueryException.
        if (!Schema::hasTable('notifications')) {
            return response()->json(['unread' => 0, 'notifications' => []]);
        }
        if (!$user) {
            return response()->json(['unread' => 0, 'notifications' => []]);
        }

        $unread = $user->unreadNotifications()->count();
        $notes = $user->notifications()->latest()->limit(20)->get()->map(function ($n) {
            return [
                'id' => $n->id,
                'type' => class_basename($n->type),
                'data' => $n->data ?? [],
                'created_at' => $n->created_at->toDateTimeString(),
                'read_at' => optional($n->read_at)?->toDateTimeString(),
            ];
        });

        return response()->json(['unread' => $unread, 'notifications' => $notes]);
    }

    public function markRead(Request $request)
    {
        $user = $request->user();
        if (!Schema::hasTable('notifications')) {
            return response()->json(['success' => true]);
        }
        $ids = $request->input('ids', []);
        if ($user) {
            if (!empty($ids) && is_array($ids)) {
                $user->unreadNotifications()->whereIn('id', $ids)->get()->each->markAsRead();
            } else {
                $user->unreadNotifications->each->markAsRead();
            }
        }

        return response()->json(['success' => true]);
    }
}
