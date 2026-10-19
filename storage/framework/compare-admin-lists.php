<?php

require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

function reportSongs(string $level): void
{
    $member = App\Models\LearnSong::where('status', 'active')->where('level', $level)->count();
    $byLevel = App\Models\LearnSong::where('level', $level)->count();
    $inLevelCategories = App\Models\LearnSong::whereHas('category', fn ($q) => $q->where('level', $level))->count();
    $uncategorized = App\Models\LearnSong::where('level', $level)->whereNull('learn_song_category_id')->count();
    $levelMismatch = App\Models\LearnSong::where('level', $level)
        ->whereHas('category', fn ($q) => $q->where('level', '!=', $level))
        ->count();
    $otherCase = App\Models\LearnSong::whereRaw('LOWER(level) = ?', [$level])
        ->where('level', '!=', $level)
        ->count();

    echo "SONGS {$level}: member_active={$member} admin_level={$byLevel} in_level_categories={$inLevelCategories} uncategorized={$uncategorized} category_level_mismatch={$levelMismatch} other_case={$otherCase}\n";
}

foreach (['beginner', 'intermediate', 'advanced'] as $level) {
    reportSongs($level);
}

echo "--- extra courses ---\n";
foreach (['beginner', 'intermediate', 'advanced'] as $level) {
    $memberCats = App\Models\ExtraCourseCategory::where('level', $level)
        ->whereHas('courses', fn ($q) => $q->where('status', 'active'))
        ->count();
    $memberCourses = App\Models\ExtraCourse::where('status', 'active')
        ->whereHas('category', fn ($q) => $q->where('level', $level))
        ->count();
    $adminCourses = App\Models\ExtraCourse::where('level', $level)
        ->whereHas('category', fn ($q) => $q->where('level', $level))
        ->count();
    $mismatch = App\Models\ExtraCourse::where('status', 'active')
        ->whereHas('category', fn ($q) => $q->where('level', $level))
        ->where(function ($q) use ($level) {
            $q->where('level', '!=', $level)->orWhereNull('level');
        })
        ->count();
    $uncat = App\Models\ExtraCourse::where('level', $level)->whereNull('extra_course_category_id')->count();
    echo "EXTRA {$level}: member_cats={$memberCats} member_courses={$memberCourses} admin_matched={$adminCourses} level_mismatch={$mismatch} uncategorized={$uncat}\n";
}

echo "--- piano ---\n";
$levels = ['independence', 'technique', 'flexibility', 'strength', 'dexterity'];
foreach ($levels as $level) {
    $member = App\Models\Upload::where('category', 'piano exercise')->where('status', 'active')->where('level', $level)->count();
    $allLevel = App\Models\Upload::where('category', 'piano exercise')->where('level', $level)->count();
    $inCats = App\Models\Upload::where('category', 'piano exercise')->where('level', $level)
        ->whereNotNull('piano_exercise_category_id')->count();
    echo "PIANO {$level}: member_active={$member} all={$allLevel} with_category={$inCats}\n";
}

$pianoCols = Illuminate\Support\Facades\Schema::getColumnListing('uploads');
echo 'upload category col sample: ';
echo json_encode(App\Models\Upload::where('category', 'piano exercise')->select('level')->distinct()->pluck('level'));
echo "\n";
