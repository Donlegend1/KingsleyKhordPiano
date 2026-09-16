<?php

namespace App\Mail;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionLifecycleMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $type,
        public ?Plan $plan = null,
        public bool $isRenewal = false,
    ) {
        $this->isRenewal = $isRenewal || $type === 'renewed';
    }

    public function build()
    {
        $firstName = $this->user->first_name ?: 'there';
        $subject = $this->isRenewal
            ? "Your membership has been renewed, {$firstName}"
            : "Welcome to Kingsleykhord Piano Academy, {$firstName}";

        return $this->subject($subject)
            ->view('emails.subscription-lifecycle', [
                'user' => $this->user,
                'plan' => $this->plan,
                'isRenewal' => $this->isRenewal,
            ]);
    }
}
