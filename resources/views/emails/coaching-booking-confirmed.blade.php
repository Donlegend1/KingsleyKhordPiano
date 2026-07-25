@extends('layouts.email')

@section('content')

<table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: auto; background: #ffffff; border-radius: 10px; overflow: hidden;">
        <tr>
            <td style="padding: 30px;">
                <p>Hi {{ $memberName }},</p>
                
                <p>Your coaching session has been successfully booked.</p>
                
                <p>Here are your session details:</p>
                
                <p>
                    📅 <strong>Date:</strong> {{ $dateFormatted }}<br>
                    🕗 <strong>Time:</strong> {{ $timeFormatted }} ({{ $timezone }})
                    📍 <strong>Platform:</strong> Zoom
                </p>

                @if($zoomLink)
                    <div style="text-align: center; margin: 30px 0;">
                        <a href="{{ $zoomLink }}" style="background-color: #2D8CFF; color: #fff; text-decoration: none; padding: 12px 25px; border-radius: 6px; display: inline-block;">Join Zoom Session</a>
                    </div>
                @else
                    <p>Your meeting link and session details will be sent to your email before the session.</p>
                @endif

                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ $setupLink }}" style="background-color: #007bff; color: #fff; text-decoration: none; padding: 12px 25px; border-radius: 6px; display: inline-block;">Watch Setup Tutorial</a>
                </div>

                <p>See you soon!</p>

                <p>Best regards,<br><strong>Kingsley</strong></p>
            </td>
        </tr>
    </table>

@endsection

