<?php

namespace App\Http\Controllers;

use App\Models\Course_video_comments;
use App\Http\Requests\StoreCourse_video_commentsRequest;
use App\Http\Requests\UpdateCourse_video_commentsRequest;
use App\Http\Requests\IndexCourseVideoCommentsRequest;
use App\Models\Upload;
use App\Models\Course;
use App\Models\AudioComment;
use Illuminate\Support\Facades\Auth;

class CourseVideoCommentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexCourseVideoCommentsRequest $request)
    {
        $comments = Course_video_comments::query()
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->where('category', $request->category);
            })
            ->when($request->filled('course_id'), function ($query) use ($request) {
                $query->where('course_id', $request->course_id);
            })
            ->with(['user', 'replies.user'])
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Comments retrieved successfully',
            'data' => $comments,
        ], 200);
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
    public function store(StoreCourse_video_commentsRequest $request)
    {
        $courseVideoComment = Course_video_comments::create([
            'user_id' => Auth::user()->id,
            'category' => $request->category,
            'course_id' => $request->course_id,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'message' => 'Comment created successfully',
            'data' => $courseVideoComment
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Course_video_comments $course_video_comments)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course_video_comments $course_video_comments)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourse_video_commentsRequest $request, Course_video_comments $course_video_comments)
    {
        $course_video_comments->update([
            'comment' => $request->comment,
        ]);

        return response()->json([
            'message' => 'Comment updated successfully',
            'data' => $course_video_comments
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
   public function destroy(Course_video_comments $course_video_comments)
    {
        $course_video_comments->replies()->delete();

        $course_video_comments->delete();

        return response()->json([
            'message' => 'Comment deleted successfully'
        ]);
    }
}
