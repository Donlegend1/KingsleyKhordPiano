@extends('layouts.email')

@section('content')

<table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: auto; background: #ffffff; border-radius: 10px; overflow: hidden;">
        <tr>
            <td style="padding: 30px;">
                <h2 style="color: #222; margin-bottom: 20px;">Book Your Free 1-on-1 Discovery Call with Kingsley</h2>
                <p>Hi {{ $firstName }},</p>
                
                <p>If you’d prefer a one-on-one approach to your piano learning journey, you can book a <strong>free 10-minute live discovery call</strong> directly with me.</p>
                
                <p>During the session, I’ll get to know your goals, challenges, and what you’re hoping to achieve so I can guide you in the best direction.</p>
                
                <p>Whenever you’re ready, just pick a time that works for you and I’ll take it from there.</p>

                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ $bookingUrl }}" style="background-color: #007bff; color: #fff; text-decoration: none; padding: 12px 25px; border-radius: 6px; display: inline-block;">Click Here to Book Your Call</a>
                </div>

                <p>Looking forward to speaking with you,<br><strong>Kingsley</strong></p>
            </td>
        </tr>
    </table>

@endsection