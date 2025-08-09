<?php

namespace App\Http\Controllers;

use App\Models\PostComment;
use App\Models\Post;
use App\Http\Requests\StorePostCommentRequest;
use App\Http\Requests\UpdatePostCommentRequest;
use App\Notifications\NewCommentNotification;

class PostCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $postComments = PostComment::all();
        return view('post_comments.index', compact('postComments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('post_comments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostCommentRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = auth()->id() ?? 1;
        $post = Post::find($request->post_id);

        $comment = PostComment::create($validatedData);

        if ($post->user_id !== auth()->id()) {
            $post->user->notify(new NewCommentNotification($comment));
        }
        return response()->json($validatedData, 200,);
    }

    /**
     * Display the specified resource.
     */
    public function show(PostComment $postComment)
    {
        return view('post_comments.show', compact('postComment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PostComment $postComment)
    {
        return view('post_comments.edit', compact('postComment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostCommentRequest $request, PostComment $postComment)
    {
        $validatedData = $request->validated();
        $postComment->update($validatedData);
        return redirect()->route('post_comments.index')->with('success', 'Post Comment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PostComment $postComment)
    {
        $postComment->delete();
        return redirect()->route('post_comments.index')->with('success', 'Post Comment deleted successfully.');
    }
}
