<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommunityInviteEmail extends Notification implements ShouldQueue
{
    use Queueable;

    public string $communityLink;

    public function __construct(string $communityLink)
    {
        $this->communityLink = $communityLink;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Let’s make this journey even more fun, ' . $notifiable->first_name . '!')
            ->view('emails.community-invite', [
                'user' => $notifiable,
                'link' => $this->communityLink,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}

