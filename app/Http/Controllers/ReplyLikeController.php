<?php

namespace App\Http\Controllers;

use App\Models\ReplyLike;
use App\Models\PostReply;
use Illuminate\Http\Request;

class ReplyLikeController extends Controller
{
    public function toggle(Request $request, PostReply $reply)
    {
        $userId = auth()->id();

        $existingLike = ReplyLike::where('post_reply_id', $reply->id)
            ->where('user_id', $userId)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
        } else {
            ReplyLike::create([
                'post_reply_id' => $reply->id,
                'user_id' => $userId,
            ]);
        }

        return response()->json([
            'liked' => ! $existingLike,
            'likes_count' => $reply->likes()->count(),
        ]);
    }
}
