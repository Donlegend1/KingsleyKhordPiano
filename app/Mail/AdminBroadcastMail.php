<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminBroadcastMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $subjectText;
    public string $body;

    /**
     * Create a new message instance.
     */
    public function __construct(string $subjectText, string $body)
    {
        $this->subjectText = $subjectText;
        $this->body = $body;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this
            ->subject($this->subjectText)
            ->view('emails.admin-broadcast')
            ->with([
                'body' => $this->body,
            ]);
    }
}
