{{--
    "Submit video here" button shown after "Mark as Complete" on lesson pages.
    Sends the student to the forum group for the lesson's skill level so they can
    post their video for that lesson. Expects $lesson (Upload, MusicalApplication or LearnSong).
--}}
@php
    $attributes = $lesson->getAttributes();
    $rawLevel = strtolower(trim((string) (($attributes['skill_level'] ?? null) ?: ($attributes['level'] ?? ''))));

    // Piano exercises are labelled Basic / Competent / Challenging; the forum groups are
    // Beginner / Intermediate / Advanced.
    $forumGroup = match ($rawLevel) {
        'basic', 'beginner' => 'beginner_guided_practice',
        'competent', 'intermediate' => 'intermediate_guided_practice',
        'challenging', 'advanced' => 'advanced_guided_practice',
        default => null,
    };

    $submitVideoUrl = $forumGroup
        ? route('community.forum.category', $forumGroup)
        : route('community.forum');
@endphp

<a href="{{ $submitVideoUrl }}"
    class="flex w-full sm:w-auto items-center justify-center gap-2 text-[14px] font-semibold px-5 py-2.5 rounded-lg border border-[#C85A5A] bg-white text-[#C85A5A] hover:bg-red-50 transition-colors">
    <i class="fa-solid fa-video text-[15px]"></i>
    Submit video here
</a>
