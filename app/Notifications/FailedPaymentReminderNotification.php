<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Bus\Queueable;

class FailedPaymentReminderNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("{$notifiable->first_name}, your payment didn’t go through—let’s fix it!")
             ->view('emails.failed-payment-reminder', ['user' => $notifiable]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
