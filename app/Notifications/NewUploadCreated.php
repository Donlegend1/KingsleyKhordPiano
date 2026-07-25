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
        $section = \Illuminate\Support\Str::title($this->post->category);

        return [
            'post_id' => $this->post->id,
            'title'   => $this->post->title,
            'url' => "/member/lesson/{$this->post->id}?type=upload",
            'message' => $this->post->title,
            'section' => $section,
            'body'    => $this->post->body,
        ];
    }
}
