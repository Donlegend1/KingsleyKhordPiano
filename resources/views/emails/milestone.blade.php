@extends('layouts.email')
@section('content')
<div style="max-width: 480px; margin: 0 auto; padding: 12px; background: #fff; border-radius: 8px;">
 <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="600" cellpadding="20" cellspacing="0" style="background-color: #ffffff; border-radius: 8px;">
                    <tr>
                        <td>
                            <h2>Wow, {{ $user->first_name }}–this calls for a celebration!</h2>

                            <p>Hey {{ $user->first_name }},</p>

                            <p>
                                Guess what? You’ve officially hit a major milestone in your piano learning journey, and that is something worth celebrating!
                            </p>

                            <p>
                                Your hands are getting stronger, your ears are getting sharper, and <strong>YOU</strong> are becoming a musician. That’s a huge deal.
                            </p>

                            <p>
                                🎯 <strong>Your next challenge:</strong> Record yourself playing something, no matter how simple, and send it to me. I’d love to hear it!
                            </p>

                            <p>
                                Keep playing, keep growing, and most importantly?
                                <br><br>
                                <strong>Keep enjoying this journey.</strong>
                            </p>

                            <p>👏 Proud of you,<br>🎶 Kingsley</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

@endsection