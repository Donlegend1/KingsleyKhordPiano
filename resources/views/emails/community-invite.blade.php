@extends('layouts.email') 
@section('content')

<table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="600" cellpadding="20" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
                    <tr>
                        <td>
                            <h2 style="margin-top: 0;">Let’s make this journey even more fun, {{ $user->first_name }}!</h2>

                            <p>Hey {{ $user->first_name }},</p>

                            <p>
                                Learning piano is even better when you have people cheering you on. That’s why I’d love for you to join our <strong>Kingsleykhord community</strong>!
                            </p>

                            <ul style="line-height: 1.6;">
                                <li>🎵 Share your progress</li>
                                <li>💡 Get practice tips</li>
                                <li>🤝 Stay motivated with fellow learners</li>
                            </ul>

                            <p>You belong here, {{ $user->first_name }}. Come say hi:</p>

                            <p style="text-align: center; margin: 30px 0;">
                                <a href="{{ $link }}"
                                   style="background-color: #1a73e8; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                                    Join the Community
                                </a>
                            </p>

                            <p>I hope to see you inside.</p>

                            <p>🎶 Kingsley</p>
                        </td>
                    </tr>
                </table>

                <p style="margin-top: 20px; color: #aaa; font-size: 12px;">
                    If you received this email by mistake or wish to unsubscribe, please ignore or update your preferences.
                </p>
            </td>
        </tr>
    </table>
@endsection