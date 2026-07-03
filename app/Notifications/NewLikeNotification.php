<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Enums\Notification\NotificationSectionEnum;

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
            'data' => [
                'user' => $this->like->user->full_name,
                'type' => 'like',
                'section' => NotificationSectionEnum::COMMUNITY->value,
                'url' => route('singlePost', $this->like->post_id),
                'by_user_avatar' => $this->like->user->passport ? asset($this->like->user->passport) : null,
            ],
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
