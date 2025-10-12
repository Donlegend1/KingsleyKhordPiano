 @extends('layouts.email')

@section('content')

<div style="max-width: 480px; margin: 0 auto; padding: 12px; background: #fff; border-radius: 8px;">
<table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="600" cellpadding="20" cellspacing="0" style="background-color: #ffffff; border-radius: 8px;">
                    <tr>
                        <td>
                            <h2>{{ $user->first_name }}, your piano journey is waiting for you</h2>

                            <p>Hey {{ $user->first_name }},</p>

                            <p>
                                I noticed you haven’t been around lately, and I just wanted to check in.
                            </p>

                            <p>
                                Life gets busy, I get it. But remember... your piano is still there, waiting for you. Music never forgets its player.
                            </p>

                            <p>
                                If you’re feeling stuck or need a fresh start, let me help. Reply to this email and tell me where you’re at—I’d love to get you back on track.
                            </p>

                            <p>
                                Whenever you’re ready, your next lesson is right here:
                            </p>

                            <p style="text-align: center;">
                                <a href="{{ config('app.url') }}/login" style="background-color: #4caf50; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;">Log In</a>
                            </p>

                            <p>Hope to hear from you soon,<br>🎶 Kingsley</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</div>

    @endsection