<?php

namespace App\Notifications;

use App\Models\Upload;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewUploadCreated extends Notification
{
    use Queueable;

    public Upload $post;

    public function __construct(Upload $post)
    {
        $this->post = $post;
    }

    public function via($notifiable)
    {
        return ['database']; // store in database
    }

    public function toArray($notifiable)
    {
        return [
            'post_id' => $this->post->id,
            'title'   => $this->post->title,
            'message' => "New video posted added in {$this->post->category}: {$this->post->title}",
            'body'    => $this->post->body,
        ];
    }
}
