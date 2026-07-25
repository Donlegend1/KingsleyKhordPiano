<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\PostComment;
use App\Enums\Notification\NotificationSectionEnum;

class NewCommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $comment;

    public function __construct(PostComment $comment)
    {
        $this->comment = $comment;
    }

    public function via($notifiable)
    {
        // Send via database and email
        return ['database', 'mail']; 
    }

    public function toDatabase($notifiable)
    {
        return [
            'data' => [
                'user' => $this->comment->user->full_name,
                'type' => 'comment',
                'section' => NotificationSectionEnum::COMMUNITY->value,
                'url' => route('singlePost', $this->comment->post_id) . '#comment-' . $this->comment->id,
                'by_user_avatar' => $this->comment->user->passport ? asset($this->comment->user->passport) : null,
            ],
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Comment on Your Post')
            ->view('emails.notifications.new_comment', [
                'notifiable' => $notifiable,
                'comment' => $this->comment,
                'url' => url('/member/post/' . $this->comment->post_id . '#comment-' . $this->comment->id),
            ]);
    }

}
