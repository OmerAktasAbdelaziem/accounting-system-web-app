<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['unread' => 0, 'notifications' => []]);
        }

        $unread = $user->unreadNotifications()->count();
        $notes = $user->unreadNotifications()->limit(10)->get()->map(function ($n) {
            return [
                'id' => $n->id,
                'type' => class_basename($n->type),
                'data' => $n->data ?? [],
                'created_at' => $n->created_at->toDateTimeString(),
            ];
        });

        return response()->json(['unread' => $unread, 'notifications' => $notes]);
    }

    public function markRead(Request $request)
    {
        $user = $request->user();
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
