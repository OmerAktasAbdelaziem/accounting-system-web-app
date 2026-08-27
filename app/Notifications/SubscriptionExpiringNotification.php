<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SubscriptionExpiringNotification extends Notification
{
    use Queueable;

    public $subscription;

    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast', 'mail'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Your subscription will expire soon (7 days)',
            'subscription_id' => $this->subscription->id,
            'expires_at' => $this->subscription->expires_at->toDateTimeString(),
            'package' => optional($this->subscription->package)->name,
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Subscription Expiring Soon')
            ->greeting('Hello ' . ($notifiable->name ?? ''))
            ->line('Your subscription will expire on ' . $this->subscription->expires_at->toDateString())
            ->line('Please renew or update payment information to avoid service interruption.')
            ->action('Manage Subscription', url('/'));
    }
}
