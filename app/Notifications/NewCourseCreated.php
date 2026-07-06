<?php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewCourseCreated extends Notification
{
    use Queueable;

    public Course $course;

    public function __construct(Course $course)
    {
        $this->course = $course;
    }

    public function via($notifiable)
    {
        return ['database']; // store in database
    }

    public function toArray($notifiable)
    {
        $section = $this->course->category ? \Illuminate\Support\Str::title($this->course->category) : 'Courses';

        return [
            'course_id' => $this->course->id,
            'message' => $this->course->title,
            'title' => $this->course->title,
            'url'      => "member/course/{$this->course->level}?selected_course={$this->course->id}",
            'category' => $this->course->category,
            'section' => $section,
            'body'    => $this->course->body,
        ];
    }
}
