@extends('layouts.email')

@section('content')

<table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: auto; background: #ffffff; border-radius: 10px; overflow: hidden;">
        <tr>
            <td style="padding: 30px;">
                <p>Hi {{ $memberName }},</p>
                
                <p>A new Live Show is coming up! </p>
                
                <p>Here are your session details:</p>
                
                <p>
                    📅 <strong>Date:</strong> {{ $dateFormatted }}<br>
                    🕗 <strong>Time:</strong> {{ $timeFormatted }} ({{ $timezone }})
                        <strong>Join here:</strong>  
                          <a href="{{ $setupLink }}" style="background-color: #007bff; color: #fff; text-decoration: none; padding: 12px 25px; border-radius: 6px; display: inline-block;">
                            Join Live Show</a>
 
                </p>

                <p>Hope to see you there. !</p>

                <p>Best regards,<br><strong>Kingsley</strong></p>
            </td>
        </tr>
    </table>

@endsection

