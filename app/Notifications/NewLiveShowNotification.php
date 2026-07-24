<?php

namespace App\Notifications;

use App\Models\Liveshow;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewLiveShowNotification extends Notification
{
    public function __construct(
        public Liveshow $liveshow,
        public User $user
    ) {
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Kingsleykhord Live Show 🎹')
            ->view('emails.live-show-announcement', [
                'show' => $this->liveshow,
                'user' => $this->user,
            ]);
    }
}