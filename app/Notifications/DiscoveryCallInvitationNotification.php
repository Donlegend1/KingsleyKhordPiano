<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DiscoveryCallInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $firstName;
    public string $bookingUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $firstName, string $bookingUrl)
    {
        $this->firstName = $firstName;
        $this->bookingUrl = $bookingUrl;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Book Your Free 1-on-1 Discovery Call with Kingsley')
            ->view('emails.discovery-call-invitation', [
                'firstName' => $this->firstName,
                'bookingUrl' => $this->bookingUrl,
            ]);
    }
}
