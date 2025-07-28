<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class WelcomeEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public $user) {}

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("{$this->user->first_name}, welcome home to Kingsleykhord Piano Academy!")
            ->view('emails.welcome', ['user' => $this->user]);
    }
}
