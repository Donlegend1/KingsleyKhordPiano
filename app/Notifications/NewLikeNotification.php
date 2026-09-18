<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Enums\Notification\NotificationSectionEnum;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

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
        return match ($notifiable->notification_preference) {
            'disabled' => ['database'],
            'push' => ['database', WebPushChannel::class],
            default => ['database', 'mail'],
        };
    }

    /**
     * Send notification via web push.
     */
    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('New Like on Your Post')
            ->body($this->like->user->full_name . ' liked your post')
            ->data(['url' => route('singlePost', $this->like->post_id)]);
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
