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
                
                <p>Thanks,<br><strong>{{ config('app.name') }}</strong></p>
            </td>
        </tr>
    </table>

@endsection

