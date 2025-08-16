<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\PostComment;

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
            'type' => 'comment',
            'post_id' => $this->comment->post_id,
            'comment_id' => $this->comment->id,
            'comment_body' => $this->comment->body,
            'by_user_id' => $this->comment->user->id,
            'first_name' => $this->comment->user->first_name,
            'last_name' => $this->comment->user->last_name,
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
