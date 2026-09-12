<?php

namespace App\Http\Controllers;

use App\Models\CommentLike;
use App\Models\PostComment;
use Illuminate\Http\Request;

class CommentLikeController extends Controller
{
    public function toggle(Request $request, PostComment $comment)
    {
        $userId = auth()->id();

        $existingLike = CommentLike::where('post_comment_id', $comment->id)
            ->where('user_id', $userId)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
        } else {
            CommentLike::create([
                'post_comment_id' => $comment->id,
                'user_id' => $userId,
            ]);
        }

        return response()->json([
            'liked' => ! $existingLike,
            'likes_count' => $comment->likes()->count(),
        ]);
    }
}
