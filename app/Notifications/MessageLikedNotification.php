<?php

namespace App\Notifications;

use App\Models\ChatMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class MessageLikedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $message;
    public $user;

    public function __construct(ChatMessage $message, $user)
    {
        $this->message = $message;
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $liker = $this->user->first_name ?? 'Someone';

        return (new MailMessage)
            ->subject("{$liker} liked your message")
            ->view('emails.chat.notifications', [
                'subject' => "{$liker} liked your message",
                'userName' => $notifiable->first_name,
                'senderName' => $liker,
                'introLine' => "{$liker} liked one of your messages in the Premium Chat:",
                'messageBody' => $this->message->body,
                'actionUrl' => url('/member/premium-chat'),
                'actionText' => 'View Message',
            ]);
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'message_liked',
            'message_id' => $this->message->id,
            'liked_by' => $this->user->first_name,
        ];
    }
}

