<?php

namespace App\Notifications;

use App\Models\LiveCoachingBooking;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AdminCoachingBookingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public LiveCoachingBooking $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct(LiveCoachingBooking $booking)
    {
        $this->booking = $booking;
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
    public function toMail($notifiable): MailMessage
    {
        $memberName = $this->booking->user ? ($this->booking->user->first_name ?: $this->booking->user->name) : 'A member';
        $memberEmail = $this->booking->user ? $this->booking->user->email : 'N/A';
        $startLagos = Carbon::parse($this->booking->date . ' ' . $this->booking->time, 'Africa/Lagos');
        $startGmt = $startLagos->copy()->setTimezone('UTC');
        
        $userTz = ($this->booking->user && $this->booking->user->timezone) ? $this->booking->user->timezone : 'UTC';
        $startUser = $startLagos->copy()->setTimezone($userTz);

        return (new MailMessage)
            ->subject('New Live Coaching Session Booked 🎹')
            ->view('emails.admin-coaching-booking-notification', [
                'memberName' => $memberName,
                'memberEmail' => $memberEmail,
                'dateFormatted' => $startLagos->format('l, F j, Y'),
                'timeLagos' => $startLagos->format('g:i A'),
                'timeUser' => $startUser->format('g:i A'),
                'timeGmt' => $startGmt->format('g:i A'),
                'userTz' => $userTz,
            ]);
    }
}
