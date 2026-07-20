@extends('layouts.email')

@section('content')

<table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: auto; background: #ffffff; border-radius: 10px; overflow: hidden;">
        <tr>
            <td style="padding: 30px;">
                <h2 style="color: #222; margin-bottom: 20px;">New Coaching Session Booked 🎹</h2>
                
                <p>A member has just booked a live coaching session. Here are their details:</p>
                
                <p>
                    <strong>Member Name:</strong> {{ $memberName }}<br>
                    <strong>Member Email:</strong> {{ $memberEmail }}<br>
                    📅 <strong>Date:</strong> {{ $dateFormatted }}<br>
                    🕗 <strong>Time:</strong> {{ $timeFormatted }} ({{ $timezone }})
                </p>

                @if($zoomLink)
                    <div style="text-align: center; margin: 30px 0;">
                        <a href="{{ $zoomLink }}" style="background-color: #2D8CFF; color: #fff; text-decoration: none; padding: 12px 25px; border-radius: 6px; display: inline-block;">Join Zoom Session</a>
                    </div>
                @else
                    <p style="color: #b91c1c;">⚠️ No Zoom link was generated for this booking. Please create one manually.</p>
                @endif

                <p>Thanks,<br><strong>{{ config('app.name') }}</strong></p>
            </td>
        </tr>
    </table>

@endsection

