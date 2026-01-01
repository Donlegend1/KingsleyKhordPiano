<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Notifications\MessageRepliedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\PostMedia;
use App\Models\User;
use App\Models\ChatRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Notifications\NewChatMessageNotification;

class ChatMessageController extends Controller
{
    public function index(ChatRoom $room)
    {
        $messages = $room->messages()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user', 'likes', 'media'])
            ->paginate(30);

        return response()->json($messages);
    }

    public function store(Request $request, ChatRoom $room)
    {
    $validated = $request->validate([
        'body' => 'required|string|max:2000',
        'media.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:20480', // 20MB max
    ]);

    $user = auth()->user();

    try {
        DB::beginTransaction();

        $message = $room->messages()->create([
            'user_id' => $user->id,
            'body' => $validated['body'],
        ]);

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $extension = strtolower($file->getClientOriginalExtension());
                $type = in_array($extension, ['mp4', 'mov', 'avi']) ? 'video' : 'image';

                $fileName = Str::uuid() . '.' . $extension;
                $uploadPath = public_path('uploads/posts');

                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $file->move($uploadPath, $fileName);

                PostMedia::create([
                    'premium_post_id' => $message->id,
                    'file_path' => 'uploads/posts/' . $fileName,
                    'type' => $type,
                ]);
            }
        }

        $recipients = User::where('premium', true)
            ->where('id', '!=', $user->id)
            ->get();

        // foreach ($recipients as $recipient) {
        //     $recipient->notify(new NewChatMessageNotification($message, $user));
        // }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully.',
            'data' => $message->load('user')
        ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to send message.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function reply(Request $r, ChatMessage $message)
    {
        $r->validate(['body' => 'required|string|max:3000']);

        $reply = ChatMessage::create([
            'room_id' => $message->room_id,
            'user_id' => $r->user()->id,
            'parent_id' => $message->id,
            'body' => $r->body,
        ]);

        if ($message->user_id !== $r->user()->id) {
            $message->user->notify(new MessageRepliedNotification($reply));
        }

        return response()->json($reply->load('user'));
    }

    public function destroy(ChatMessage $message)
    {
    $user = auth()->user();

    // ✅ Authorization check — only message owner or admin can delete
    if ($user->id !== $message->user_id && !$user->role === 'admin') {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized to delete this message.',
        ], 403);
    }

    try {
        DB::beginTransaction();

        // ✅ Delete associated media (from DB + filesystem)
        foreach ($message->media as $media) {
            $filePath = public_path($media->file_path);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
            $media->delete();
        }

        $message->delete();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully.',
        ]);
    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Failed to delete message.',
            'error' => $e->getMessage(),
        ], 500);
    }
    }
}
