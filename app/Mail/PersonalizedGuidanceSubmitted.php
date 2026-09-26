<?php

namespace App\Mail;

use App\Models\PersonalizedGuidanceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PersonalizedGuidanceSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PersonalizedGuidanceRequest $guidanceRequest)
    {
        $this->guidanceRequest->loadMissing('user');
    }

    public function envelope(): Envelope
    {
        $name = $this->guidanceRequest->user->full_name ?? 'A premium member';

        return new Envelope(
            subject: 'New Personalized Guidance Request: ' . $name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.personalized-guidance-submitted',
        );
    }
}
