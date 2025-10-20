@extends('layouts.email')

@section('content')
<div style="max-width: 480px; margin: 0 auto; padding: 12px; background: #fff; border-radius: 8px;">
<p>Hey {{ $user->first_name}},</p>

<p>It’s been a week since you started your piano journey, and I just wanted to check in. Have you noticed how your hands are starting to flow across the keys? Even if it’s just a little smoother than before. That’s progress!</p>

<p><strong>Remember:</strong></p>
<ul>
    <li>Small, consistent efforts build mastery.</li>
    <li>Mistakes are just stepping stones to perfection.</li>
    <li>If it feels hard, that means you’re growing.</li>
</ul>

<p>Hit ‘Reply’ and tell me: <strong>What’s been your favorite part of learning so far?</strong> I’d love to hear!</p>

<p>— Kingsley</p>
</div>
@endsection
