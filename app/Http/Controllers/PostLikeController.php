<?php

namespace App\Http\Controllers;

use App\Models\PostLike;
use App\Models\Post;
use App\Http\Requests\StorePostLikeRequest;
use App\Http\Requests\UpdatePostLikeRequest;
use App\Notifications\NewLikeNotification;

class PostLikeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $postLikes = PostLike::all();
        return view('post_likes.index', compact('postLikes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('post_likes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostLikeRequest $request)
    {
        $post = Post::findOrFail($request->post_id);
        $user_id = auth()->id();

        // Check if like already exists
        $existingLike = $post->likes()->where('user_id', $user_id)->first();

        if ($existingLike) {
            $existingLike->delete();
            $like = null;
        } else {
            $like = $post->likes()->create([
                'user_id' => $user_id,
            ]);
        }

        if ($like && $post->user_id !== $user_id) {
            $post->user->notify(new NewLikeNotification($like));
        }

        return response()->json([
            'likes' => $post->likes()->with('user')->get()
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(PostLike $postLike)
    {
        return view('post_likes.show', compact('postLike'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PostLike $postLike)
    {
        return view('post_likes.edit', compact('postLike'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostLikeRequest $request, PostLike $postLike)
    {
        $validatedData = $request->validated();
        $postLike->update($validatedData);
        return redirect()->route('post_likes.index')->with('success', 'Post Like updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PostLike $postLike)
    {
        $postLike->delete();
        return redirect()->route('post_likes.index')->with('success', 'Post Like deleted successfully.');
    }
}
