<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SkillAssessmentEmail extends Notification
{
    use Queueable;

    public string $assessmentLink;

    public function __construct(string $assessmentLink)
    {
        $this->assessmentLink = $assessmentLink;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Let's find your perfect starting note!")
            ->view('emails.skill-assessment', [
                'user' => $notifiable,
                'assessmentLink' => $this->assessmentLink,
            ]);
    }
}
