<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Merchant;
use App\Notifications\AdminPreviewNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use App\Models\AuditLog;

class SubscriptionNotificationPreviewController extends Controller
{
    public function index(Merchant $merchant)
    {
        $user = auth()->user();
        if (!$user || !$user->isSuperAdmin()) {
            abort(403);
        }

        $admins = $merchant->users()->whereHas('role', function($q){ $q->where('name', 'merchant_admin'); })->get();
        return view('super-admin.subscriptions.recipients_preview', compact('merchant','admins'));
    }

    public function send(Request $request, Merchant $merchant)
    {
        $user = $request->user();
        if (!$user || !$user->isSuperAdmin()) {
            abort(403);
        }

        $admins = $merchant->users()->whereHas('role', function($q){ $q->where('name', 'merchant_admin'); })->get();
        $message = $request->input('message', 'This is a preview notification regarding subscriptions.');
        Notification::send($admins, new AdminPreviewNotification($message));

        // Audit/log the preview send
        try {
            AuditLog::logAction('sent_subscription_preview', Merchant::class, $merchant->id, [
                'recipients_count' => $admins->count(),
                'recipients' => $admins->pluck('id')->all(),
                'message' => $message,
            ], $user);
        } catch (\Exception $e) {
            Log::warning('Failed to write audit log for subscription preview: '.$e->getMessage());
        }

        Log::info('Subscription preview sent', ['merchant_id' => $merchant->id, 'sent_by' => $user->id ?? null, 'recipients' => $admins->pluck('id')->all()]);

        return redirect()->back()->with('status', 'Preview notification sent to ' . $admins->count() . ' recipient(s).');
    }
}
