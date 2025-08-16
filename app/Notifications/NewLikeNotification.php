<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewLikeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $like;

    /**
     * Create a new notification instance.
     */
    public function __construct($like)
    {
        $this->like = $like;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Store notification in database.
     */
    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'like',
            'post_id' => $this->like->post_id,
            'by_user_id' => $this->like->user->id,
            'first_name' => $this->like->user->first_name,
            'last_name' => $this->like->user->last_name,
        ];
    }

    /**
     * Send notification via email.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Like on Your Post')
            ->view('emails.notifications.new_like', [
                'notifiable' => $notifiable,
                'like' => $this->like,
                'url' => url('/member/post/' . $this->like->post_id),
            ]);
    }
}
