<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Models\Post;
use Illuminate\Notifications\Notification;
use App\Enums\Notification\NotificationSectionEnum;

class NewPostNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Post $post)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => "New post added:  by {$this->post->user->name}",
            'data' => [ 
                'title' => "New post added by {$this->post->user->name}",
                'user' => $this->post->user->name,
                'section' => NotificationSectionEnum::COMMUNITY->value,
                'url' => route('singlePost', $this->post),
            ],
        ];
    }
}
