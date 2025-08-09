<?php

namespace App\Http\Controllers;

use App\Models\PostReply;
use App\Http\Requests\StorePostReplyRequest;
use App\Http\Requests\UpdatePostReplyRequest;
use App\Models\PostComment;

class PostReplyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $postReplies = PostReply::all();
        return view('post_replies.index', compact('postReplies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('post_replies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostReplyRequest $request, PostComment $postComment)
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = auth()->id();
        $validatedData['comment_id'] = $postComment->id;
        PostReply::create($validatedData);
        
        return response()->json($validatedData, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(PostReply $postReply)
    {
        return view('post_replies.show', compact('postReply'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PostReply $postReply)
    {
        return view('post_replies.edit', compact('postReply'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostReplyRequest $request, PostReply $postReply)
    {
        $validatedData = $request->validated();
        $postReply->update($validatedData);
        return redirect()->route('post_replies.index')->with('success', 'Post Reply updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PostReply $postReply)
    {
        $postReply->delete();
        return redirect()->route('post_replies.index')->with('success', 'Post Reply deleted successfully.');
    }
}
