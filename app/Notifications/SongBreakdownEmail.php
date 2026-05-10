<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SongBreakdownEmail extends Notification
{
    use Queueable;

    public function __construct() {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("{$notifiable->first_name}, let’s break it down—one song at a time!")
            ->view('emails.song-breakdown', ['user' => $notifiable]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
