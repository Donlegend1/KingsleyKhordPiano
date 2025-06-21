<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DocumentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    public function build()
    {
        return $this->subject('Here is your document')
                    ->markdown('emails.document')
                    ->attach($this->filePath, [
                        'as' => 'kingsleyKhordRoadmap.pdf',
                        'mime' => 'application/pdf',
                    ]);
    }
}
