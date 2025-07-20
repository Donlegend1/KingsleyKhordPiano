<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;

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
        $validated = $request->validated();

        $course = Course::create($validated);

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
        $course->update($request->validated());
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
    public function coursesList()
    {
        $courses = Course::paginate(10);
        return response()->json($courses);
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
        $courses = Course::with('progress')->where('level', $level)->get();

        if ($courses->isEmpty()) {
            return response()->json(['message' => 'No courses found for this level'], 404);
        }

        // Group courses by `group_name` (adjust field name as necessary)
        $groupedCourses = $courses->groupBy('category');
        

        return response()->json($groupedCourses);
    }

    public function deleteCourse(Course $course)
    {
        $course->delete();
        return response()->json(['message' => 'Course deleted successfully']);
    }

    
}
