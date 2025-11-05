<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class UpdateSubscriptionConfirmationNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public \App\Models\UpdateSubscription $subscription) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = URL::signedRoute('subscribe.confirm', ['subscription' => $this->subscription->id]);

        return (new MailMessage)
            ->subject(__('Confirm your subscription to updates'))
            ->greeting(__('Hello!'))
            ->line(__('Please confirm your email to receive updates about the marketplace platform.'))
            ->action(__('Confirm Email'), $url)
            ->line(__('If you did not request updates, you can ignore this email.'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
