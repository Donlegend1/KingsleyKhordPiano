<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Bookmark;
use App\Models\User;
use App\Notifications\NewCourseCreated;
use App\Enums\Roles\UserRoles;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Helpers\VideoHelper;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.courses.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = \App\Enums\Course\Categories::cases();
        return view('admin.courses.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseRequest $request)
    {
        $category = CourseCategory::where('category', $request->input('category'))->first();

        $validated = $request->validated();
        $validated['course_category_id'] = $category->id;

        $videoType = $validated['video_type'] ?? 'iframe';
        $videoPath = $validated['video_url'] ?? null;

        if ($videoType === 'youtube') {
            $validated['video_url'] = VideoHelper::extractYoutubeId($videoPath);
        }

        if ($videoType === 'google') {
            $validated['video_url'] = VideoHelper::extractGoogleDriveId($videoPath);
        }

        if ($videoType === 'local') {
            $validated['video_url'] = $videoPath;
        }

        if ($videoType === 'iframe') {
            $validated['video_url'] = $videoPath;
        }

        $course = Course::create($validated);
        $members = User::where('role', UserRoles::MEMBER->value)->get();

        foreach ($members as $member) {
            $member->notify(new NewCourseCreated($course));
        }

        return response()->json($course, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        return view('admin.show-course', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        $categories = \App\Enums\Course\Categories::cases();
        return view('admin.edit-course', compact('course', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseRequest $request, Course $course)
    {
        $validated = $request->validated();

        /* ---------------------------------------
        |  HANDLE VIDEO TYPE & EXTRACT ID
        ----------------------------------------*/
        $videoType = $validated['video_type'] ?? $course->video_type; 
        $videoPath = $validated['video_url'] ?? $course->video_url;

        if ($videoType === 'youtube' && isset($validated['video_url'])) {
            $validated['video_url'] = VideoHelper::extractYoutubeId($videoPath);
        }

        if ($videoType === 'google' && isset($validated['video_url'])) {
            $validated['video_url'] = VideoHelper::extractGoogleDriveId($videoPath);
        }

        if ($videoType === 'local' && isset($validated['video_url'])) {
            // Keep local filename as-is
            $validated['video_url'] = $videoPath;
        }

        if ($videoType === 'iframe' && isset($validated['video_url'])) {
            // Keep iframe HTML as-is
            $validated['video_url'] = $videoPath;
        }

        /* ---------------------------------------
        |  UPDATE COURSE
        ----------------------------------------*/
        $course->update($validated);

        return response()->json($course, 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        
    }

    /**
     * Get a list of all courses.
     */
    public function coursesList(Request $request)
    {
        $perPage = $request->per_page === 'all' ? null : ($request->per_page ?? 9);

        $fetchLevel = function ($level) {
            $categories = CourseCategory::where('level', $level)
                ->orderBy('position')
                ->get(['id', 'category', 'position']);

            $categories->load(['courses' => function ($q) use ($level) {
                $q->where('level', $level)
                ->orderBy('position');
            }]);

            $data = [];
            foreach ($categories as $cat) {
                $data[$cat->category] = $cat->courses->values();
            }

            return [
                'data' => $data,
                'current_page' => 1,
                'last_page' => 1,
            ];
        };

        return response()->json([
            'beginner' => $fetchLevel('beginner'),
            'intermediate' => $fetchLevel('intermediate'),
            'advanced' => $fetchLevel('advanced'),
        ]);
    }

    public function membershow($level)
    {
        // Validate the level parameter
        $validLevels = ['beginner', 'intermediate', 'advanced'];
        if (!in_array($level, $validLevels)) {
            return response()->json(['message' => 'Invalid course level'], 400);
        }

        // Fetch courses based on the level
        return view('memberpages.course.details', compact('level'));
    }
    public function membershowAPI($level)
    {
        // Get all course categories for this level (even if they have no courses)
        $categories = \App\Models\CourseCategory::with([
            'courses' => function ($query) use ($level) {
                $query->where('level', $level)
                    ->with('progress')
                    ->orderBy('position');
            }
        ])
        ->where('level', $level)
        ->orderBy('position')
        ->get();

        if ($categories->isEmpty()) {
            return response()->json(['message' => 'No categories found for this level'], 404);
        }

        // Fetch user's bookmarked courses
        $bookmarkedIds = Bookmark::where('user_id', auth()->id())
            ->where('source', 'courses')
            ->pluck('video_id')
            ->toArray();

        // Add bookmark info to each course
        $categories->each(function ($category) use ($bookmarkedIds) {
            $category->courses->transform(function ($course) use ($bookmarkedIds) {
                $course->isBookmarked = in_array($course->id, $bookmarkedIds);
                return $course;
            });
        });

        return response()->json($categories);
    }

    public function deleteCourse(Course $course)
    {
        $course->delete();
        return response()->json(['message' => 'Course deleted successfully']);
    }

    public function updatePositions(Request $request)
    {
        $validated = $request->validate([
            'level' => 'required|string',
            'categories' => 'required|array',
            'categories.*' => 'string',
        ]);

        $level = $validated['level'];
        $categories = $validated['categories'];

        // Loop through each category in the new order
        foreach ($categories as $index => $categoryName) {
            \App\Models\CourseCategory::where('level', $level)
                ->where('category', $categoryName)
                ->update(['position' => $index + 1]);
        }

        return response()->json([
            'message' => 'Category positions updated successfully',
        ]);
    }

}