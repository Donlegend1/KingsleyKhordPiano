<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bookmark;

class BookMarkController extends Controller
{
    public function bookmark()
    {
        $bookmarks = Bookmark::where('user_id', auth()->id())
        ->latest()
        ->get()
        ->map(function ($bookmark) {
            $bookmark->video = $bookmark->resolveVideo();
            return $bookmark;
        });
        
        return view('memberpages.bookmark.index', compact('bookmarks'));
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'video_id' => 'required|integer',
            'source'   => 'required|string',
        ]);

        $bookmark = Bookmark::where('user_id', auth()->id())
            ->where('video_id', $request->video_id)
            ->where('source', $request->source)
            ->first();

        if ($bookmark) {
            $bookmark->delete();
            return response()->json(['status' => 'removed']);
        }

        Bookmark::create([
            'user_id'  => auth()->id(),
            'video_id' => $request->video_id,
            'source'   => $request->source,
        ]);

        return response()->json(['status' => 'added']);
    }
}
