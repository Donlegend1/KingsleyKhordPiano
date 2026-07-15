<?php

namespace App\Notifications;

use App\Models\Etude;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewEtudeCreated extends Notification
{
    use Queueable;

    public function __construct(public Etude $etude)
    {
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'etude_id'  => $this->etude->id,
            'title'     => $this->etude->title,
            'message'   => $this->etude->title,
            'section'   => 'Etudes & Pieces',
            'url'       => "/member/lesson/{$this->etude->id}?type=etudes",
        ];
    }
}
