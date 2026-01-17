<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\NewChatMessageNotification;
use App\Models\ChatMessage;

class PremiumChatController extends Controller
{
    public function index()
    {
        $members = User::where('premium', 1)->get();
        return view('memberpages.premium.index', compact('members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $message = ChatMessage::create([
            'user_id' => auth()->id(),
            'body' => $validated['body'],
        ]);

        $sender = auth()->user();
        $recipients = User::where('is_premium', true)
            ->where('id', '!=', $sender->id)
            ->get();

        foreach ($recipients as $user) {
            $user->notify(new NewChatMessageNotification($message, $sender));
        }

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully!',
            'data' => $message
        ]);
    }
}


