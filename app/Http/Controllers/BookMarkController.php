<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BookmarkService;


class BookMarkController extends Controller
{
    public function bookmark(BookmarkService $service)
    {
        $bookmarks = $service->getUserBookmarks();
        // dd($bookmarks);
        
        return view('memberpages.bookmark.index', compact('bookmarks'));
    }

    public function toggle(Request $request, BookmarkService $service)
    {
        $data = $request->validate([
            'bookmarkable_id'   => 'required|integer',
            'bookmarkable_type' => 'required|string',
        ]);
    
        // Resolve the model class
        $modelClass = $this->resolveModel($data['bookmarkable_type']);
        if (!$modelClass) {
            return response()->json(['error' => 'Invalid type'], 400);
        }
    
        // Fetch the model instance
        $bookmarkable = $modelClass::find($data['bookmarkable_id']);
        if (!$bookmarkable) {
            return response()->json(['error' => 'Item not found'], 404);
        }
    
        // Toggle bookmark
        $added = $service->toggle($bookmarkable);
    
        return response()->json([
            'status' => $added ? 'added' : 'removed',
        ]);
    }
    
    /**
     * Resolve string to model class
     */
    protected function resolveModel(string $type)
    {
        return match ($type) {
            'uploads' => \App\Models\Upload::class,
            'courses' => \App\Models\Course::class,
            'posts'   => \App\Models\Post::class,
            default   => null,
        };
    }
}    
