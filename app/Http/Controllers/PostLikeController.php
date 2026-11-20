<?php

namespace App\Http\Controllers;

use App\Models\PostLike;
use App\Models\Post;
use App\Http\Requests\StorePostLikeRequest;
use App\Http\Requests\UpdatePostLikeRequest;
use App\Notifications\NewLikeNotification;
use Illuminate\Support\Facades\DB;

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
        if ($request->has('liked')) {
            $liked = $request->boolean('liked');

            if ($liked) {
                $like = $post->likes()->firstOrCreate(['user_id' => $user_id], ['type' => 'like']);
                if ($like->wasRecentlyCreated) {
                    $this->notifyFirstLike($post, $like);
                }
            } else {
                $post->likes()->where('user_id', $user_id)->delete();
            }

            return response()->json(['liked' => $liked, 'likes_count' => $post->likes()->count()]);
        }

        $type = $request->input('type', 'like');

        // Check if like already exists
        $existingLike = $post->likes()->where('user_id', $user_id)->first();

        if ($existingLike) {
            if ($existingLike->type === $type) {
                // same reaction tapped again -> remove it
                $existingLike->delete();
                $like = null;
            } else {
                // switching to a different reaction
                $existingLike->update(['type' => $type]);
                $like = $existingLike;
            }
        } else {
            $like = $post->likes()->create([
                'user_id' => $user_id,
                'type' => $type,
            ]);
        }

        if ($like?->wasRecentlyCreated) {
            $this->notifyFirstLike($post, $like);
        }

        return response()->json([
            'likes' => $post->likes()->with('user')->get()
        ]);
    }

    private function notifyFirstLike(Post $post, PostLike $like): void
    {
        if ((int) $post->user_id === (int) $like->user_id) {
            return;
        }

        try {
            $claimed = DB::table('post_like_notification_receipts')->insertOrIgnore([
                'post_id' => $post->id,
                'user_id' => $like->user_id,
            ]);

            if ($claimed) {
                $post->user?->notify(new NewLikeNotification($like));
            }
        } catch (\Throwable $error) {
            report($error);
        }
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
