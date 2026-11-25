<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class PianoGuideMail extends Mailable
{
    public function __construct(
        public string $guideTitle,
        public string $pdfUrl,
        public ?string $practiceTip = null,
    ) {
    }

    public function build()
    {
        return $this->subject('Your ' . $this->guideTitle . ' PDF')
            ->view('emails.piano-guide')
            ->text('emails.piano-guide-text');
    }
}
