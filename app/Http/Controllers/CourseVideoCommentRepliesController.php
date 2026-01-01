<?php

namespace App\Http\Controllers;

use App\Models\CourseVideoCommentReply;
use App\Models\CourseVideoComment;
use App\Http\Requests\StoreCourseVideoCommentReplyRequest;
use App\Http\Requests\UpdateCourseVideoCommentReplyRequest;

class CourseVideoCommentRepliesController extends Controller
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
    public function store(StoreCourseVideoCommentReplyRequest $request, CourseVideoComment $comment)
    {

        $reply = $comment->replies()->create([
            'reply' => $request->comment,
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Reply created successfully',
            'data' => $reply,
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseVideoCommentReply $CourseVideoCommentReply)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourseVideoCommentReply $CourseVideoCommentReply)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseVideoCommentReplyRequest $request, CourseVideoCommentReply $CourseVideoCommentReply)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseVideoCommentReply $CourseVideoCommentReply)
    {
        //
    }
}
