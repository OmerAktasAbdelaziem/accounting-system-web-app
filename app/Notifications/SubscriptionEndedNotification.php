<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SubscriptionEndedNotification extends Notification
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
            'message' => 'Your subscription has ended',
            'subscription_id' => $this->subscription->id,
            'expires_at' => $this->subscription->expires_at->toDateTimeString(),
            'package' => optional($this->subscription->package)->name,
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Subscription Ended')
            ->greeting('Hello ' . ($notifiable->name ?? ''))
            ->line('Your subscription ended on ' . $this->subscription->expires_at->toDateString())
            ->line('Please contact support to reactivate your subscription and restore access.')
            ->action('Contact Support', url('/contact'));
    }
}
