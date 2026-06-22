@extends('layouts.email')

@section('content')

<table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: auto; background: #ffffff; border-radius: 10px; overflow: hidden;">
        <tr>
            <td style="padding: 30px;">
                <p>Hi {{ $memberName }},</p>
                
                <p>Your slot for the Progress report session is officially locked in!</p>
                
                <p>Here are your session details:</p>
                
                <p>
                    📅 <strong>Date:</strong> {{ $dateFormatted }}<br>
                    🕗 <strong>Time:</strong> {{ $timeFormatted }} ({{ $timezone }})
                </p>
                
                <p>If you haven't already, I highly recommend watching the quick setup tutorial on how to set up for the live session here:</p>

                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ $setupLink }}" style="background-color: #007bff; color: #fff; text-decoration: none; padding: 12px 25px; border-radius: 6px; display: inline-block;">Watch Setup Tutorial</a>
                </div>

                <p>Can't wait to see you there!</p>

                <p>Best regards,<br><strong>Kingsley</strong></p>
            </td>
        </tr>
    </table>

@endsection

