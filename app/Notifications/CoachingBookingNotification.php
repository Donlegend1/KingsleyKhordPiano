<?php

namespace App\Notifications;

use App\Models\LiveCoachingBooking;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CoachingBookingNotification extends Notification implements ShouldQueue
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
        $memberName = $notifiable->first_name ?: $notifiable->name;
        $userTz = $notifiable->timezone ?: 'UTC';
        $dateTime = Carbon::parse($this->booking->date . ' ' . $this->booking->time, 'Africa/Lagos')->setTimezone($userTz);
        $dateFormatted = $dateTime->format('l, F j, Y');
        $timeFormatted = $dateTime->format('g:i A');
        $setupLink = url('/member/getstarted?step=3');

        $offset = $dateTime->offset;
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
            ->subject('Session Confirmed 🎹')
            ->view('emails.coaching-booking-confirmed', [
                'memberName' => $memberName,
                'dateFormatted' => $dateFormatted,
                'timeFormatted' => $timeFormatted,
                'timezone' => $gmtLabel,
                'setupLink' => $setupLink,
            ]);
    }
}
