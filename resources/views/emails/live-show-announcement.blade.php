@php
    $userTz = (isset($user) && $user && $user->timezone) ? $user->timezone : 'UTC';
    $startLocal = \Carbon\Carbon::parse($show['start_time'], 'Africa/Lagos')->setTimezone($userTz);
    
    // For calendar integration, convert to UTC/GMT
    $startUtc = \Carbon\Carbon::parse($show['start_time'], 'Africa/Lagos')->setTimezone('UTC');
    $endUtc = $startUtc->copy()->addHour();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Live Show — {{ $show['title'] ?? 'Kingsley Khord' }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f7;font-family:'Segoe UI',Arial,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f7;padding:40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">

                    <!-- Header Banner -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#1E1B4B 0%,#4C1D95 60%,#7C3AED 100%);padding:48px 40px 36px;text-align:center;">
                            <img src="{{ url('/logo/logoblack.png') }}" alt="Kingsley Khord" style="height:36px;filter:brightness(0) invert(1);margin-bottom:24px;" />
                            <div style="display:inline-block;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.3);border-radius:100px;padding:6px 18px;margin-bottom:20px;">
                                <span style="color:#E9D5FF;font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;">🔴 Live Event</span>
                            </div>
                            <h1 style="margin:0;color:#ffffff;font-size:28px;font-weight:800;line-height:1.3;letter-spacing:-0.5px;">
                                {{ $show['title'] ?? 'New Live Show Announced' }}
                            </h1>
                        </td>
                    </tr>

                    <!-- Date / Time strip -->
                    <tr>
                        <td style="background:#F5F3FF;border-bottom:1px solid #EDE9FE;padding:0;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="50%" style="padding:18px 24px;text-align:center;border-right:1px solid #EDE9FE;">
                                        <p style="margin:0 0 2px;font-size:10px;font-weight:700;color:#7C3AED;text-transform:uppercase;letter-spacing:1.5px;">Date</p>
                                        <p style="margin:0;font-size:16px;font-weight:700;color:#1E1B4B;">{{ $startLocal->format('l, F j, Y') }}</p>
                                    </td>
                                    <td width="50%" style="padding:18px 24px;text-align:center;">
                                        <p style="margin:0 0 2px;font-size:10px;font-weight:700;color:#7C3AED;text-transform:uppercase;letter-spacing:1.5px;">Time</p>
                                        <p style="margin:0;font-size:16px;font-weight:700;color:#1E1B4B;">{{ $startLocal->format('g:i A') }} ({{ $userTz }})</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:36px 40px;">
                            <p style="margin:0 0 12px;font-size:17px;color:#111827;font-weight:600;">Hey {{ $user->first_name }},</p>

                            <p style="margin:0 0 16px;font-size:15px;color:#4B5563;line-height:1.7;">
                                I'm excited to announce a brand new live session — and I want you there!
                                This is going to be a great time of learning, playing, and growing together at the keys.
                            </p>

                            @if(!empty($show['description']))
                            <p style="margin:0 0 16px;font-size:15px;color:#4B5563;line-height:1.7;">
                                {{ $show['description'] }}
                            </p>
                            @endif

                            <!-- What to expect box -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background:#F5F3FF;border-left:4px solid #7C3AED;border-radius:0 8px 8px 0;margin:24px 0;">
                                <tr>
                                    <td style="padding:20px 24px;">
                                        <p style="margin:0 0 10px;font-size:13px;font-weight:700;color:#7C3AED;text-transform:uppercase;letter-spacing:1px;">What to expect</p>
                                        <ul style="margin:0;padding:0 0 0 18px;color:#374151;font-size:14px;line-height:2;">
                                            <li>Live piano instruction and Q&amp;A</li>
                                            <li>Techniques you can apply immediately</li>
                                            <li>Real-time feedback and tips</li>
                                            <li>Community connection with fellow students</li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin:28px 0 8px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ url('/member/live-session') }}"
                                           style="display:inline-block;background:#7C3AED;color:#ffffff;text-decoration:none;font-size:15px;font-weight:700;padding:16px 40px;border-radius:10px;letter-spacing:0.3px;">
                                            View Live Show →
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Add to Calendar -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin:14px 0 0;">
                                <tr>
                                    <td align="center">
                                        <a href="https://www.google.com/calendar/render?action=TEMPLATE&text={{ urlencode($show['title'] ?? 'Kingsley Khord Live Show') }}&dates={{ $startUtc->format('Ymd\THis\Z') }}/{{ $endUtc->format('Ymd\THis\Z') }}&details={{ urlencode('Join the live session at kingsleykhord.com') }}"
                                           target="_blank"
                                           style="display:inline-block;border:1.5px solid #D1D5DB;color:#6B7280;text-decoration:none;font-size:13px;font-weight:600;padding:11px 28px;border-radius:8px;">
                                            📅 Add to Google Calendar
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:32px 0 0;font-size:15px;color:#374151;line-height:1.7;">
                                See you at the show,<br/>
                                <strong style="color:#1E1B4B;">Kingsley Khord</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#F9FAFB;border-top:1px solid #F3F4F6;padding:24px 40px;text-align:center;">
                            <p style="margin:0 0 8px;font-size:13px;color:#9CA3AF;">
                                You're receiving this because you're a member of Kingsley Khord Piano Academy.
                            </p>
                            <p style="margin:0;font-size:12px;color:#D1D5DB;">
                                &copy; {{ date('Y') }} Kingsley Khord. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
