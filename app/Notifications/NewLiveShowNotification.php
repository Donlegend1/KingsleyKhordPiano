<?php

namespace App\Notifications;

use App\Models\Liveshow;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewLiveShowNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Liveshow $liveshow;

    /**
     * Create a new notification instance.
     */
    public function __construct(Liveshow $liveshow)
    {
        $this->liveshow = $liveshow;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Kingsleykhord Live Show 🎹')
            ->view('emails.live-show-announcement', [
                'show' => $this->liveshow,
                'user' => $notifiable,
            ]);
    }
}
