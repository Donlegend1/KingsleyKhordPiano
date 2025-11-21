@php
    $senderName = $senderName ?? 'Someone';
    $introLine = $introLine ?? '';
    $messageBody = $messageBody ?? '';
    $actionUrl = $actionUrl ?? url('/');
    $actionText = $actionText ?? 'View Message';
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $subject ?? 'Premium Chat Notification' }}</title>
</head>
<body style="font-family: Arial, sans-serif; background-color:#f9f9f9; padding:30px;">
    <div style="max-width:600px; margin:auto; background:#fff; border-radius:10px; padding:25px;">
        <h2 style="color:#333;">{{ $subject ?? 'Premium Chat Update' }}</h2>

        <p style="color:#555; font-size:15px;">Hi {{ $userName ?? 'there' }},</p>

        <p style="color:#555; font-size:15px;">{{ $introLine }}</p>

        <blockquote style="background:#f4f4f4; padding:10px 15px; border-left:4px solid #555; color:#444;">
            {{ $messageBody }}
        </blockquote>

        <p style="text-align:center; margin:30px 0;">
            <a href="{{ $actionUrl }}" style="background:#000; color:#fff; padding:12px 24px; border-radius:6px; text-decoration:none;">
                {{ $actionText }}
            </a>
        </p>

        <p style="color:#999; font-size:13px;">
            Thanks for being a valued Premium member!  
            <br>
            — {{ config('app.name') }}
        </p>
    </div>
</body>
</html>
