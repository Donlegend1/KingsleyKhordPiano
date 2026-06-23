@extends('layouts.email')

@section('content')

@php
    $userTz = $booking->timezone ?: 'UTC';
    $start = \Carbon\Carbon::parse($booking->date . ' ' . $booking->time, 'Africa/Lagos')->setTimezone($userTz);
    $end = $start->copy()->addHour();

    $offset = $start->offset;
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
@endphp

<table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: auto; background: #ffffff; border-radius: 10px; overflow: hidden;">
        <tr>
            <td style="padding: 30px;">
                <h2 style="color: #222; margin-bottom: 20px;">New Guest Booking 🎹</h2>
                
                <p>A guest has just booked and paid for a one-on-one piano coaching session. Here are the details:</p>
                
                <table width="100%" cellpadding="10" cellspacing="0" style="background-color: #f8f9fa; border-radius: 8px; margin: 20px 0; border-left: 4px solid #1d4ed8; font-size: 15px;">
                    <tr>
                        <td style="padding: 12px 15px; color: #555; line-height: 1.8;">
                            <strong>👤 Name:</strong> {{ $booking->name }}<br>
                            <strong>✉️ Email:</strong> {{ $booking->email }}<br>
                            <strong>📅 Date:</strong> {{ $start->format('l, F j, Y') }}<br>
                            <strong>🕗 Time:</strong> {{ $start->format('g:i A') }} – {{ $end->format('g:i A') }} ({{ $gmtLabel }})<br>
                            <strong>💳 Payment:</strong> $60 via {{ ucfirst($booking->payment_method) }} ({{ ucfirst($booking->payment_status) }})
                            @if($booking->skill_level)
                                <br><strong>📈 Skill Level:</strong> {{ ucfirst($booking->skill_level) }}
                            @endif
                            @if($booking->focus)
                                <br><strong>🎯 Focus:</strong> {{ $booking->focus }}
                            @endif
                        </td>
                    </tr>
                </table>

                <p><strong>Action needed:</strong> Prepare a Zoom meeting link for this session and add it to the booking in the admin panel so it gets sent to {{ $booking->name }} before their session.</p>

                <div style="text-align: center; margin: 25px 0;">
                    <a href="{{ route('admin.guest-bookings.index') }}" style="background-color: #007bff; color: #ffffff; text-decoration: none; padding: 12px 25px; border-radius: 6px; display: inline-block; font-weight: bold;">View Booking in Admin Panel</a>
                </div>

                <p>Thanks,<br><strong>{{ config('app.name') }}</strong></p>
            </td>
        </tr>
    </table>

@endsection
