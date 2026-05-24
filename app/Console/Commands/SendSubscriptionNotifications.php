<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Notifications\SubscriptionExpiringNotification;
use App\Notifications\SubscriptionEndedNotification;

class SendSubscriptionNotifications extends Command
{
    protected $signature = 'subscriptions:notify';
    protected $description = 'Send subscription expiring and ended notifications to merchants';

    public function handle()
    {
        $this->info('Running subscription notifications...');

        // Expiring in 7 days and not yet notified
        $soon = Subscription::where('is_active', true)
            ->whereNull('reminder_7days_sent_at')
            ->whereBetween('expires_at', [now(), now()->addDays(7)])
            ->get();

        foreach ($soon as $sub) {
            try {
                $admins = $sub->merchant->users()->where('user_type', 'merchant_admin')->get();
                $admins->each->notify(new SubscriptionExpiringNotification($sub));
                $sub->update(['reminder_7days_sent_at' => now()]);
            } catch (\Exception $e) {
                // ignore per-merchant failures
            }
        }

        // Expired subscriptions: mark inactive and notify
        $expired = Subscription::where('is_active', true)
            ->where('expires_at', '<=', now())
            ->get();

        foreach ($expired as $sub) {
            try {
                $sub->update(['is_active' => false, 'ended_notified_at' => now()]);
                $sub->merchant->update(['subscription_expires_at' => $sub->expires_at]);
                $admins = $sub->merchant->users()->where('user_type', 'merchant_admin')->get();
                $admins->each->notify(new SubscriptionEndedNotification($sub));
            } catch (\Exception $e) {
                // ignore
            }
        }

        $this->info('Done.');
        return 0;
    }
}
