<?php

namespace App\Notifications;

use App\Models\Liveshow;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class LiveshowSlothNotification extends Notification implements ShouldQueue
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

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $memberName = $notifiable->first_name ?: $notifiable->name;
        
        $userTz = $notifiable->timezone ?: 'UTC';
        $startTimeLocal = Carbon::parse($this->liveshow->start_time, 'Africa/Lagos')->setTimezone($userTz);
        
        $dateFormatted = $startTimeLocal->format('l, F j, Y');
        $timeFormatted = $startTimeLocal->format('g:i A');
        $setupLink = url('/member/getstarted?step=3');

        return (new MailMessage)
            ->subject('Confirmed! See you at the Live Session 🎹')
            ->view('emails.liveshow-sloth', [
                'memberName' => $memberName,
                'dateFormatted' => $dateFormatted,
                'timeFormatted' => $timeFormatted,
                'timezone' => $userTz,
                'setupLink' => $setupLink,
            ]);
    }
}
