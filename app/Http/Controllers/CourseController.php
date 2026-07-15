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
use  \App\Enums\Course\Categories;

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
        $categories = Categories::cases();
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

        $videoType = $validated['video_type'];
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

        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $filename = time() . '_' . $thumbnail->getClientOriginalName();
            $destination = base_path('../public_html/uploads/thumbnails');
            if (!file_exists($destination)) {
                $destination = public_path('uploads/thumbnails');
            }
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            $thumbnail->move($destination, $filename);
            $validated['thumbnail'] = 'uploads/thumbnails/' . $filename;
        }

        if ($request->hasFile('images')) {
            $destination = base_path('../public_html/uploads/descriptions');
            if (!file_exists($destination)) {
                $destination = public_path('uploads/descriptions');
            }
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            $descriptionImages = [];
            foreach ($request->file('images') as $imgFile) {
                $filename = time() . '_' . uniqid() . '_' . $imgFile->getClientOriginalName();
                $imgFile->move($destination, $filename);
                $descriptionImages[] = 'uploads/descriptions/' . $filename;
            }
            $validated['images'] = $descriptionImages;
        }

        if ($request->hasFile('pdf_resource')) {
            $pdf = $request->file('pdf_resource');
            $filename = time() . '_' . $pdf->getClientOriginalName();
            $destination = base_path('../public_html/uploads/resources/pdf');
            if (!file_exists($destination)) {
                $destination = public_path('uploads/resources/pdf');
            }
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            $pdf->move($destination, $filename);
            $validated['pdf_resource'] = 'uploads/resources/pdf/' . $filename;
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

       logger()->info(['vedoe' => $validated]);
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

        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $filename = time() . '_' . $thumbnail->getClientOriginalName();
            $destination = base_path('../public_html/uploads/thumbnails');
            if (!file_exists($destination)) {
                $destination = public_path('uploads/thumbnails');
            }
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            
            // Delete old thumbnail if it exists
            if ($course->thumbnail && file_exists(public_path($course->thumbnail))) {
                @unlink(public_path($course->thumbnail));
            }
            
            $thumbnail->move($destination, $filename);
            $validated['thumbnail'] = 'uploads/thumbnails/' . $filename;
        }

        if ($request->hasFile('images')) {
            $destination = base_path('../public_html/uploads/descriptions');
            if (!file_exists($destination)) {
                $destination = public_path('uploads/descriptions');
            }
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            // Delete old images
            if ($course->images && is_array($course->images)) {
                foreach ($course->images as $oldImg) {
                    if (file_exists(public_path($oldImg))) {
                        @unlink(public_path($oldImg));
                    }
                }
            }

            $descriptionImages = [];
            foreach ($request->file('images') as $imgFile) {
                $filename = time() . '_' . uniqid() . '_' . $imgFile->getClientOriginalName();
                $imgFile->move($destination, $filename);
                $descriptionImages[] = 'uploads/descriptions/' . $filename;
            }
            $validated['images'] = $descriptionImages;
        }

        if ($request->hasFile('pdf_resource')) {
            $pdf = $request->file('pdf_resource');
            $filename = time() . '_' . $pdf->getClientOriginalName();
            $destination = base_path('../public_html/uploads/resources/pdf');
            if (!file_exists($destination)) {
                $destination = public_path('uploads/resources/pdf');
            }
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            // Delete old PDF if it exists
            if ($course->pdf_resource && file_exists(public_path($course->pdf_resource))) {
                @unlink(public_path($course->pdf_resource));
            }

            $pdf->move($destination, $filename);
            $validated['pdf_resource'] = 'uploads/resources/pdf/' . $filename;
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

    public function allCourses(Request $request)
    {
        $query = Course::query();
        if ($request->has('level')) {
            $query->where('level', $request->level);
        }
        return response()->json($query->get(['id', 'title', 'level', 'category']));
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

    public function recordView(Course $course)
    {
        \App\Models\LessonView::record(auth()->id(), $course);

        return response()->json(['status' => 'ok']);
    }

    public function membershowAPI($level)
    {
        $userId = auth()->id();

        $categories = CourseCategory::with([
            'courses' => function ($query) use ($level, $userId) {
                $query->where('level', $level)
                    ->with([
                        'progress' => function ($q) use ($userId) {
                            $q->where('user_id', $userId);
                        },
                        'bookmarks' => function ($q) use ($userId) {
                            $q->where('user_id', $userId);
                        }
                    ])
                    ->orderByRaw('position IS NULL, position ASC')
                    ->orderBy('id');
            }
        ])
        ->where('level', $level)
        ->orderBy('position')
        ->get();

        if ($categories->isEmpty()) {
            return response()->json(['message' => 'No categories found for this level'], 404);
        }

        // Add `isBookmarked` flag to each course and load related courses if any
        $categories->each(function ($category) use ($userId) {
            $category->hasNewLessons = \App\Models\LessonView::anyNewUnviewed($userId, $category->courses);

            $category->courses->transform(function ($course) {
                $course->isBookmarked = $course->bookmarks->isNotEmpty();
                unset($course->bookmarks); // optional, remove bookmarks relation to clean response

                if ($course->related_courses && count($course->related_courses)) {
                    $course->related = Course::whereIn('id', $course->related_courses)->get();
                } else {
                    $course->related = [];
                }

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

    public function updateCoursePositions(Request $request)
    {
        $validated = $request->validate([
            'courses' => 'required|array',
            'courses.*' => 'integer',
        ]);

        foreach ($validated['courses'] as $index => $id) {
            Course::where('id', $id)->update(['position' => $index + 1]);
        }

        return response()->json([
            'message' => 'Course positions updated successfully',
        ]);
    }
}