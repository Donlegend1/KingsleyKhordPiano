<?php

namespace App\Http\Controllers;

use App\Models\course_video_comment_replies;
use App\Models\Course_video_comments;
use App\Http\Requests\Storecourse_video_comment_repliesRequest;
use App\Http\Requests\Updatecourse_video_comment_repliesRequest;

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
    public function store(Storecourse_video_comment_repliesRequest $request, Course_video_comments $comment)
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
    public function show(course_video_comment_replies $course_video_comment_replies)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(course_video_comment_replies $course_video_comment_replies)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Updatecourse_video_comment_repliesRequest $request, course_video_comment_replies $course_video_comment_replies)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(course_video_comment_replies $course_video_comment_replies)
    {
        //
    }
}
