<?php

namespace App\Http\Controllers;

use App\Models\CourseProgress;
use App\Models\Course;
use App\Http\Requests\StoreCourseProgressRequest;
use App\Http\Requests\UpdateCourseProgressRequest;
use Illuminate\Support\Facades\Auth;

class CourseProgressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseProgressRequest $request, Course $course)
    {

    $user = Auth::user();
    if (!$user) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    CourseProgress::firstOrCreate([
        'user_id' => $user->id,
        'course_id' => $course->id,
    ], [
        'course_category' => $course->category,
    ]);

    return response()->json(['message' => 'Course marked as completed']);

    }

    /**
     * Display the specified resource.
     */
    public function show(CourseProgress $courseProgress)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourseProgress $courseProgress)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseProgressRequest $request, CourseProgress $courseProgress)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseProgress $courseProgress)
    {
        //
    }
}
