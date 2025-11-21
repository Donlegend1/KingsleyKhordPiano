<?php

namespace App\Notifications;

use App\Models\ChatMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class MessageRepliedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $reply;

    public function __construct(ChatMessage $reply)
    {
        $this->reply = $reply;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $sender = $this->reply->user->first_name ?? 'Someone';

        return (new MailMessage)
            ->subject("New Reply from {$sender}")
            ->view('emails.chat.notification', [
                'subject' => "New Reply from {$sender}",
                'userName' => $notifiable->first_name,
                'senderName' => $sender,
                'introLine' => "{$sender} replied to your message in the Premium Chat:",
                'messageBody' => $this->reply->body,
                'actionUrl' => url('/member/premium-chat'),
                'actionText' => 'View Conversation',
            ]);
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'message_replied',
            'reply_id' => $this->reply->id,
            'message_id' => $this->reply->parent_id,
            'user_id' => $this->reply->user_id,
            'body' => $this->reply->body,
        ];
    }
}

