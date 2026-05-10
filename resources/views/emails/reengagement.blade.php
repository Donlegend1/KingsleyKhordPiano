<div>
    Hey {{ $user->first_name ?? 'there' }},
    <br><br>
    I noticed you haven’t been around lately, and I just wanted to check in.
    <br><br>
    Life gets busy, I get it. But remember...your piano is still there, waiting for you. Music never forgets its player.
    <br><br>
    If you’re feeling stuck or need a fresh start, let me help. Reply to this email and tell me where you’re at—I’d love to get you back on track.
    <br><br>
    Whenever you’re ready, your next lesson is right here:
    <br><br>
    <a href="{{ url('/login') }}" style="display:inline-block; padding:10px 20px; background-color:#3490dc; color:#ffffff; text-decoration:none; border-radius:5px;">Login</a>
    <br><br>
    Hope to hear from you soon,<br>
    Kingsley
</div>