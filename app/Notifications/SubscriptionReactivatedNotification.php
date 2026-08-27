<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionReactivatedNotification extends Notification
{
    use Queueable;

    public function __construct(public Subscription $subscription)
    {
    }

    public function via($notifiable): array
    {
        return ['database', 'broadcast', 'mail'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Subscription Reactivated',
            'message' => 'Your subscription has been reactivated successfully.',
            'subscription_id' => $this->subscription->id,
            'merchant_id' => $this->subscription->merchant_id,
            'package' => optional($this->subscription->package)->name,
            'expires_at' => optional($this->subscription->expires_at)?->toDateTimeString(),
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Subscription Reactivated')
            ->greeting('Hello ' . ($notifiable->name ?? ''))
            ->line('Your subscription has been reactivated.')
            ->line('Package: ' . (optional($this->subscription->package)->name ?? 'N/A'))
            ->line('Valid until: ' . optional($this->subscription->expires_at)?->toDateString())
            ->action('Open Dashboard', url('/dashboard'));
    }
}
