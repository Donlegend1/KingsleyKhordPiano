<?php

namespace App\Http\Controllers;

use App\Models\CourseVideoComment;
use App\Http\Requests\StoreCourseVideoCommentRequest;
use App\Http\Requests\UpdateCourseVideoCommentRequest;
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
        $comments = CourseVideoComment::query()
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
    public function store(StoreCourseVideoCommentRequest $request)
    {
        $courseVideoComment = CourseVideoComment::create([
            'user_id' => Auth::user()->id,
            'category' => $request->category,
            'course_id' => $request->course_id,
            'comment' => $request->comment,
            'url' => $request->url,
        ]);

        return response()->json([
            'message' => 'Comment created successfully',
            'data' => $courseVideoComment
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseVideoComment $CourseVideoComment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourseVideoComment $CourseVideoComment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseVideoCommentRequest $request, CourseVideoComment $CourseVideoComment)
    {
        $CourseVideoComment->update([
            'comment' => $request->comment,
        ]);

        return response()->json([
            'message' => 'Comment updated successfully',
            'data' => $CourseVideoComment
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
   public function destroy(CourseVideoComment $CourseVideoComment)
    {
        $CourseVideoComment->replies()->delete();

        $CourseVideoComment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully'
        ]);
    }
}
