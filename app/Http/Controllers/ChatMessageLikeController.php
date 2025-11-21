<?php

namespace App\Http\Controllers;
use App\Models\ChatMessage;
use App\Notifications\MessageLikedNotification;

use Illuminate\Http\Request;

class ChatMessageLikeController extends Controller
{
    public function toggle(Request $r, ChatMessage $message)
    {
        $user = $r->user();
        $existing = $message->likes()->where('user_id', $user->id)->first();

        if ($existing) {
            $existing->delete();
            $action = 'unliked';
        } else {
            $message->likes()->create(['user_id' => $user->id]);
            $action = 'liked';

            if ($message->user_id !== $user->id) {
                $message->user->notify(new MessageLikedNotification($message, $user));
            }
        }

        return response()->json([
            'action' => $action,
            'likes_count' => $message->likes()->count(),
        ]);
    }
}
