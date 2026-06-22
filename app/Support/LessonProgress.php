<?php

namespace App\Support;

use App\Models\ExtraCourse;
use App\Models\LearnSong;
use Illuminate\Support\Facades\DB;

/**
 * Shared lesson-completion math used by both the member dashboard
 * (HomeController) and the community overview (CommunityIndexController),
 * so "lessons completed" / milestone counts stay consistent across pages.
 *
 * Courses are tracked via the legacy `course_progress` table (its own
 * "Mark as Completed" UI). Every other lesson type (extra courses, learn
 * songs, quizzes, uploads) is tracked via the generic `lesson_completions`
 * polymorphic table (its own "Mark as Complete" UI). Both are combined here
 * so any completed lesson — regardless of type — counts toward the totals.
 */
class LessonProgress
{
    /**
     * Completed lessons for a given level, across courses, extra courses,
     * and learn songs (the three content types that have a level).
     */
    public static function completedForLevel(int $userId, string $level): int
    {
        $courseCompleted = DB::table('course_progress')
            ->join('courses', 'course_progress.course_id', '=', 'courses.id')
            ->where('course_progress.user_id', $userId)
            ->where('courses.level', $level)
            ->distinct('course_progress.course_id')
            ->count('course_progress.course_id');

        $extraCourseCompleted = DB::table('lesson_completions')
            ->join('extra_courses', 'lesson_completions.completable_id', '=', 'extra_courses.id')
            ->where('lesson_completions.completable_type', ExtraCourse::class)
            ->where('lesson_completions.user_id', $userId)
            ->whereRaw('LOWER(extra_courses.level) = ?', [strtolower($level)])
            ->distinct('lesson_completions.completable_id')
            ->count('lesson_completions.completable_id');

        $learnSongCompleted = DB::table('lesson_completions')
            ->join('learn_songs', 'lesson_completions.completable_id', '=', 'learn_songs.id')
            ->where('lesson_completions.completable_type', LearnSong::class)
            ->where('lesson_completions.user_id', $userId)
            ->whereRaw('LOWER(learn_songs.level) = ?', [strtolower($level)])
            ->distinct('lesson_completions.completable_id')
            ->count('lesson_completions.completable_id');

        return $courseCompleted + $extraCourseCompleted + $learnSongCompleted;
    }

    /**
     * Total available lessons for a given level, across the same three types.
     */
    public static function totalForLevel(string $level): int
    {
        $coursesTotal = DB::table('courses')->where('level', $level)->count();
        $extraCoursesTotal = DB::table('extra_courses')->whereRaw('LOWER(level) = ?', [strtolower($level)])->count();
        $learnSongsTotal = DB::table('learn_songs')->whereRaw('LOWER(level) = ?', [strtolower($level)])->count();

        return $coursesTotal + $extraCoursesTotal + $learnSongsTotal;
    }

    /**
     * Total completed lessons for a user across every content type,
     * including ones without a level (quizzes, uploads). Drives milestones.
     */
    public static function totalCompleted(int $userId): int
    {
        $courseCompleted = DB::table('course_progress')
            ->where('user_id', $userId)
            ->distinct('course_id')
            ->count('course_id');

        // Excludes the Course type to avoid double-counting against
        // course_progress, in case anything ever posts that type here too.
        $otherCompleted = DB::table('lesson_completions')
            ->where('user_id', $userId)
            ->where('completable_type', '!=', \App\Models\Course::class)
            ->count();

        return $courseCompleted + $otherCompleted;
    }

    /**
     * Builds the {level => ['total' => x, 'completed' => y]} map used by
     * both the dashboard progress bars and the milestone calculations.
     */
    public static function progressByLevel(int $userId, array $levels): array
    {
        return collect($levels)->mapWithKeys(function ($level) use ($userId) {
            return [$level => [
                'total' => self::totalForLevel($level),
                'completed' => self::completedForLevel($userId, $level),
            ]];
        })->toArray();
    }
}
