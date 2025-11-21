<?php

namespace App\Notifications;

use App\Models\ChatMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewChatMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $message;
    protected $sender;

    public function __construct(ChatMessage $message, $sender)
    {
        $this->message = $message;
        $this->sender = $sender;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("New Message from {$this->sender->first_name}")
            ->view('emails.chat.notifications', [
                'subject' => "New Message from {$this->sender->first_name}",
                'userName' => $notifiable->first_name,
                'senderName' => $this->sender->first_name,
                'introLine' => "{$this->sender->first_name} just sent a new message in the Premium Chat:",
                'messageBody' => $this->message->body,
                'actionUrl' => url('/member/premium-chat'),
                'actionText' => 'View Message',
            ]);
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'new_message',
            'message_id' => $this->message->id,
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->first_name,
            'body' => $this->message->body,
        ];
    }
}
