<?php

namespace App\Notifications;

use App\Models\ExtraCourse;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewExtraCourseCreated extends Notification
{
    use Queueable;

    public function __construct(public ExtraCourse $course)
    {
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'course_id' => $this->course->id,
            'title'     => $this->course->title,
            'message'   => $this->course->title,
            'section'   => 'Extra Courses',
            'url'       => "/member/lesson/{$this->course->id}",
        ];
    }
}
