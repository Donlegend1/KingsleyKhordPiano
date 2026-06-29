<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;
use App\Services\BookmarkService;
use App\Models\CourseVideoComment;

class CoursesController extends Controller
{
     public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Show the courses page.
     *
     * @return \Illuminate\View\View
     */
    public function extraCourses(Request $request)
    {
        \App\Models\CategoryView::markViewed(auth()->id(), 'extra_courses');

        $search = $request->input('name');
        $activeTab = $request->input('tab', 'beginner');

        $fetchLevelCategories = function($level) use ($search) {
            $query = \App\Models\ExtraCourseCategory::where('level', $level)
                ->orderBy('position');

            $query->with(['courses' => function($q) use ($search, $level) {
                $q->where('level', $level)
                  ->where('status', 'active')
                  ->when($search, function($subQ) use ($search) {
                      $subQ->where('title', 'like', "%{$search}%")
                           ->orWhere('description', 'like', "%{$search}%");
                  })
                  ->orderBy('position');
            }]);

            $categories = $query->get();

            if ($search) {
                $categories = $categories->filter(function($category) {
                    return $category->courses->isNotEmpty();
                });
            }

            return $categories;
        };

        $beginnerCategories = $fetchLevelCategories('beginner');
        $intermediateCategories = $fetchLevelCategories('intermediate');
        $advancedCategories = $fetchLevelCategories('advanced');

        return view('memberpages.extracources', compact(
            'beginnerCategories',
            'intermediateCategories',
            'advancedCategories',
            'search',
            'activeTab'
        ));
    }

    public function singleCourse($id, BookmarkService $service) 
    {
        // Try ExtraCourse
        $lesson = \App\Models\ExtraCourse::find($id);
        $type = 'extra_course';

        if (!$lesson) {
            // Try LearnSong
            $lesson = \App\Models\LearnSong::find($id);
            $type = 'learn_song';
        }

        if (!$lesson) {
            // Fallback to general Upload
            $lesson = Upload::findOrFail($id);
            $type = 'upload';
        }

        \App\Models\LessonView::record(auth()->id(), $lesson);

        $comments = CourseVideoComment::where('category', 'others')
            ->whereNot('course_id', $id)
            ->with(['user', 'replies.user', 'course'])
            ->latest()
            ->take(5)
            ->get();

        $relatedUploads = collect();
        if ($type === 'learn_song') {
            if (is_array($lesson->related_songs) && count($lesson->related_songs)) {
                $relatedUploads = \App\Models\LearnSong::whereIn('id', $lesson->related_songs)->get();
            }
        } elseif ($type === 'extra_course') {
            if (is_array($lesson->related_courses) && count($lesson->related_courses)) {
                $relatedUploads = \App\Models\ExtraCourse::whereIn('id', $lesson->related_courses)->get();
            }
        } else {
            if (is_array($lesson->tags) && count($lesson->tags)) {
                $relatedUploads = Upload::whereIn('id', $lesson->tags)->get();
            }
        }

        $isBookmarked = $service->isBookmarked($lesson);

        // Find next and previous video in the same category (if applicable)
        $previousVideo = null;
        $nextVideo = null;

        if ($type === 'learn_song') {
            $previousVideo = \App\Models\LearnSong::where('learn_song_category_id', $lesson->learn_song_category_id)
                ->where('position', '<', $lesson->position)
                ->orderBy('position', 'desc')
                ->first();

            $nextVideo = \App\Models\LearnSong::where('learn_song_category_id', $lesson->learn_song_category_id)
                ->where('position', '>', $lesson->position)
                ->orderBy('position', 'asc')
                ->first();
        } elseif ($type === 'extra_course') {
            $previousVideo = \App\Models\ExtraCourse::where('extra_course_category_id', $lesson->extra_course_category_id)
                ->where('position', '<', $lesson->position)
                ->orderBy('position', 'desc')
                ->first();

            $nextVideo = \App\Models\ExtraCourse::where('extra_course_category_id', $lesson->extra_course_category_id)
                ->where('position', '>', $lesson->position)
                ->orderBy('position', 'asc')
                ->first();
        }

        return view('memberpages.singleExtracourse', compact(
            'lesson',
            'relatedUploads',
            'comments',
            'isBookmarked',
            'previousVideo',
            'nextVideo'
        ));
    }
}
