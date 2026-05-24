<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SubscriptionExtendedNotification extends Notification
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
            'message' => 'Your subscription has been extended',
            'subscription_id' => $this->subscription->id,
            'starts_at' => $this->subscription->start_date->toDateTimeString(),
            'expires_at' => $this->subscription->expires_at->toDateTimeString(),
            'package' => optional($this->subscription->package)->name,
            'amount' => (string) $this->subscription->amount_paid,
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Subscription Extended')
            ->greeting('Hello ' . ($notifiable->name ?? ''))
            ->line('Your subscription has been extended.')
            ->line('Package: ' . optional($this->subscription->package)->name)
            ->line('Starts: ' . $this->subscription->start_date->toDateString())
            ->line('Expires: ' . $this->subscription->expires_at->toDateString())
            ->line('Amount paid: ' . ($this->subscription->amount_paid ?? ''))
            ->action('View Account', url('/'));
    }
}
