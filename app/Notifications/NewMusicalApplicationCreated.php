<?php

namespace App\Notifications;

use App\Models\MusicalApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewMusicalApplicationCreated extends Notification
{
    use Queueable;

    public function __construct(public MusicalApplication $application)
    {
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'application_id' => $this->application->id,
            'title'          => $this->application->title,
            'message'        => $this->application->title,
            'section'        => 'Guided Practice',
            'url'            => route('piano.exercise.player', [
                'series'      => $this->application->series,
                'skill_level' => strtolower($this->application->skill_level),
                'video_id'    => $this->application->id,
            ]),
        ];
    }
}
