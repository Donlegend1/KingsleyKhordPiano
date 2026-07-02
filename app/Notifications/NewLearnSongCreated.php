<?php

namespace App\Notifications;

use App\Models\LearnSong;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewLearnSongCreated extends Notification
{
    use Queueable;

    public function __construct(public LearnSong $song)
    {
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'song_id' => $this->song->id,
            'title'   => $this->song->title,
            'message' => $this->song->title,
            'section' => 'Learn Songs',
            'url'     => "/member/lesson/{$this->song->id}",
        ];
    }
}
