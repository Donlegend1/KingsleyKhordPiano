<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionExpiredMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $payment;

    public function __construct($user, $payment)
    {
        $this->user = $user;
        $this->payment = $payment;
    }

    public function build()
    {
        return $this->subject('Your Subscription Has Expired')
                    ->markdown('emails.subscription');
    }
}


