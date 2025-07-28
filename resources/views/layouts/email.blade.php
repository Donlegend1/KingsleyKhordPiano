<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? config('app.name') }}</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f9fafb;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .wrapper {
            width: 100%;
            padding: 40px 0;
            background: #f9fafb;
        }
        .email-container {
            max-width: 620px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        .email-header {
            background-color: #1d4ed8;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 22px;
            font-weight: bold;
        }
        .email-body {
            padding: 30px;
        }
        .email-footer {
            background-color: #f1f5f9;
            color: #6b7280;
            padding: 15px 30px;
            text-align: center;
            font-size: 14px;
        }
        h1, h2, h3 {
            color: #1f2937;
        }
        a.button {
            display: inline-block;
            background-color: #1d4ed8;
            color: #ffffff !important;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
        }
        p {
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        a {
            color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="email-container">
            <div class="email-header">
                {{ config('app.name') }}
            </div>
            <div class="email-body">
                @yield('content')
            </div>
            <div class="email-footer">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>
