<div>
    Hey {{ $user->first_name ?? 'there' }},
    <br><br>
    Embarking on your piano journey is exciting, and knowing where to begin can make all the difference.
    <br><br>
    To tailor your learning experience, could you take a moment to assess your current skill level?
    <br><br>
    Click here to take our quick assessment: <a href="{{ url($assessmentLink ?? '/') }}">Skill Assessment</a>
    <br><br>
    This will help us recommend lessons that fit you just right, ensuring a smooth and enjoyable progression.
    <br><br>
    Looking forward to seeing where you shine!
    <br><br>
    Kingsley
</div>
