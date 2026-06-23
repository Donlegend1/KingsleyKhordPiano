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
        
        $userTz = ($this->booking->user && $this->booking->user->timezone) ? $this->booking->user->timezone : 'UTC';
        $startUser = $startLagos->copy()->setTimezone($userTz);
        $endUser = $startUser->copy()->addMinutes(45);
        $timeFormatted = $startUser->format('g:i A') . ' – ' . $endUser->format('g:i A');

        $offset = $startUser->offset;
        $hours = intval($offset / 3600);
        $minutes = abs(intval(($offset % 3600) / 60));
        if ($offset == 0) {
            $gmtLabel = 'GMT';
        } else {
            $gmtLabel = 'GMT' . ($hours >= 0 ? '+' : '-') . abs($hours);
            if ($minutes > 0) {
                $gmtLabel .= ':' . sprintf('%02d', $minutes);
            }
        }

        return (new MailMessage)
            ->subject('New Live Coaching Session Booked 🎹')
            ->view('emails.admin-coaching-booking-notification', [
                'memberName' => $memberName,
                'memberEmail' => $memberEmail,
                'dateFormatted' => $startUser->format('l, F j, Y'),
                'timeFormatted' => $timeFormatted,
                'timezone' => $gmtLabel,
            ]);
    }
}
