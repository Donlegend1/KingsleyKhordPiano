@extends('layouts.email')

@section('content')

<table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: auto; background: #ffffff; border-radius: 10px; overflow: hidden;">
        <tr>
            <td style="padding: 30px;">
                <h2 style="color: #222; margin-bottom: 20px;">Session Confirmed! 🎹</h2>
                
                <p>Hi {{ $booking->name }},</p>
                
                <p>Thank you for booking a one-on-one piano coaching session with Kingsley Khord! Your session is confirmed — here's a summary of your booking:</p>
                
                @php
                    $startGmt = \Carbon\Carbon::parse($booking->date . ' ' . $booking->time, 'Africa/Lagos')->setTimezone('UTC');
                    $endGmt = $startGmt->copy()->addHour();
                @endphp
                <table width="100%" cellpadding="10" cellspacing="0" style="background-color: #f8f9fa; border-radius: 8px; margin: 20px 0; border-left: 4px solid #1d4ed8; font-size: 15px;">
                    <tr>
                        <td style="padding: 12px 15px; color: #555; line-height: 1.8;">
                            <strong>📅 Date:</strong> {{ $startGmt->format('l, F j, Y') }}<br>
                            <strong>🕗 Time:</strong> {{ $startGmt->format('g:i A') }} – {{ $endGmt->format('g:i A') }} (GMT)<br>
                            <strong>👤 Host:</strong> Kingsley Khord<br>
                            <strong>⏱️ Duration:</strong> 1 Hour<br>
                            <strong>💳 Price:</strong> $60 (Paid via {{ ucfirst($booking->payment_method) }})
                            @if($booking->skill_level)
                                <br><strong>📈 Skill Level:</strong> {{ ucfirst($booking->skill_level) }}
                            @endif
                            @if($booking->focus)
                                <br><strong>🎯 Focus:</strong> {{ $booking->focus }}
                            @endif
                        </td>
                    </tr>
                </table>

                <h3 style="color: #333; margin-top: 25px; margin-bottom: 10px;">Your Meeting Link</h3>

                @if($booking->zoom_join_url)
                    <p>You can join the session directly using the button below:</p>
                    <div style="text-align: center; margin: 25px 0;">
                        <a href="{{ $booking->zoom_join_url }}" style="background-color: #007bff; color: #ffffff; text-decoration: none; padding: 12px 25px; border-radius: 6px; display: inline-block; font-weight: bold;">Join Zoom Meeting</a>
                    </div>
                @elseif($booking->google_meet_link)
                    <p>You can join the session directly using the button below:</p>
                    <div style="text-align: center; margin: 25px 0;">
                        <a href="{{ $booking->google_meet_link }}" style="background-color: #007bff; color: #ffffff; text-decoration: none; padding: 12px 25px; border-radius: 6px; display: inline-block; font-weight: bold;">Join Google Meet</a>
                    </div>
                @else
                    <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; border-radius: 6px; color: #856404; margin: 20px 0;">
                        Your Zoom meeting link is being prepared and <strong>will be sent to you in a separate email soon</strong> — please keep an eye on your inbox in the days leading up to your session.
                    </div>
                @endif

                <p style="margin-top: 30px;">We look forward to a productive and valuable session with you!</p>

                <p>Best regards,<br><strong>Kingsley</strong></p>
            </td>
        </tr>
    </table>

@endsection

