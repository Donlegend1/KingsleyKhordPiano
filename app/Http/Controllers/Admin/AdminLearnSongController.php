<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LearnSong;
use App\Models\LearnSongCategory;
use App\Helpers\VideoHelper;
use Illuminate\Support\Facades\DB;

class AdminLearnSongController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function list(Request $request)
    {
        $fetchLevel = function ($level) {
            $categories = LearnSongCategory::where('level', $level)
                ->orderBy('position')
                ->get(['id', 'category', 'position']);

            $categories->load(['songs' => function ($q) use ($level) {
                $q->where('level', $level)
                  ->orderBy('position');
            }]);

            $data = [];
            foreach ($categories as $cat) {
                $data[$cat->category] = $cat->songs->values();
            }

            return [
                'data' => $data,
                'current_page' => 1,
                'last_page' => 1,
            ];
        };

        return response()->json([
            'beginner' => $fetchLevel('beginner'),
            'intermediate' => $fetchLevel('intermediate'),
            'advanced' => $fetchLevel('advanced'),
        ]);
    }

    public function allSongs()
    {
        return response()->json(LearnSong::get(['id', 'title', 'level']));
    }

    public function storeSong(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string', // Category name
            'level' => 'required|string',
            'video_type' => 'required|string',
            'video_url' => 'required|string',
            'status' => 'required|string',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:5000',
            'related_songs' => 'nullable|array',
        ]);

        $level = strtolower($request->input('level'));
        $categoryName = $request->input('category');

        $category = LearnSongCategory::where('category', $categoryName)
            ->where('level', $level)
            ->first();

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 400);
        }

        $videoType = $request->input('video_type');
        $videoUrl = $request->input('video_url');

        if ($videoType === 'youtube') {
            $videoUrl = VideoHelper::extractYoutubeId($videoUrl);
        } elseif ($videoType === 'google') {
            $videoUrl = VideoHelper::extractGoogleDriveId($videoUrl);
        }

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $filename = time() . '_' . $thumbnail->getClientOriginalName();
            $destination = base_path('../public_html/uploads/thumbnails');
            if (!file_exists($destination)) {
                $destination = public_path('uploads/thumbnails');
            }
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            $thumbnail->move($destination, $filename);
            $thumbnailPath = 'uploads/thumbnails/' . $filename;
        }

        $maxPos = LearnSong::where('learn_song_category_id', $category->id)->max('position') ?: 0;

        $song = LearnSong::create([
            'learn_song_category_id' => $category->id,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'video_type' => $videoType,
            'video_url' => $videoUrl,
            'thumbnail' => $thumbnailPath,
            'level' => $level,
            'status' => $request->input('status'),
            'position' => $maxPos + 1,
            'related_songs' => $request->input('related_songs'),
        ]);

        return response()->json($song, 201);
    }

    public function updateSong(Request $request, $id)
    {
        $song = LearnSong::findOrFail($id);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'video_type' => 'nullable|string',
            'video_url' => 'nullable|string',
            'status' => 'nullable|string',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:5000',
            'related_songs' => 'nullable|array',
        ]);

        $videoType = $request->input('video_type') ?? $song->video_type;
        $videoUrl = $request->input('video_url') ?? $song->video_url;

        if ($request->has('video_url')) {
            if ($videoType === 'youtube') {
                $videoUrl = VideoHelper::extractYoutubeId($videoUrl);
            } elseif ($videoType === 'google') {
                $videoUrl = VideoHelper::extractGoogleDriveId($videoUrl);
            }
        }

        $thumbnailPath = $song->thumbnail;
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $filename = time() . '_' . $thumbnail->getClientOriginalName();
            $destination = base_path('../public_html/uploads/thumbnails');
            if (!file_exists($destination)) {
                $destination = public_path('uploads/thumbnails');
            }
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            if ($song->thumbnail && file_exists(public_path($song->thumbnail))) {
                @unlink(public_path($song->thumbnail));
            }

            $thumbnail->move($destination, $filename);
            $thumbnailPath = 'uploads/thumbnails/' . $filename;
        }

        $song->update([
            'title' => $request->input('title') ?? $song->title,
            'description' => $request->input('description') ?? $song->description,
            'video_type' => $videoType,
            'video_url' => $videoUrl,
            'thumbnail' => $thumbnailPath,
            'status' => $request->input('status') ?? $song->status,
            'related_songs' => $request->input('related_songs') ?? $song->related_songs,
        ]);

        return response()->json($song, 200);
    }

    public function deleteSong($id)
    {
        $song = LearnSong::findOrFail($id);
        if ($song->thumbnail && file_exists(public_path($song->thumbnail))) {
            @unlink(public_path($song->thumbnail));
        }
        $song->delete();
        return response()->json(['message' => 'Song deleted successfully'], 200);
    }

    public function createCategory(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'level' => 'required|string|max:255',
        ]);

        $level = strtolower($request->input('level'));
        $categoryName = $request->input('category');

        $maxPos = LearnSongCategory::where('level', $level)->max('position') ?: 0;

        $category = LearnSongCategory::create([
            'category' => $categoryName,
            'level' => $level,
            'position' => $maxPos + 1,
        ]);

        return response()->json($category, 201);
    }

    public function deleteCategory($name)
    {
        $category = LearnSongCategory::where('category', $name)->firstOrFail();
        
        $existingSong = LearnSong::where('learn_song_category_id', $category->id)->first();
        if ($existingSong) {
            return response()->json([
                'message' => 'Cannot delete category because it has songs assigned.'
            ], 400);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ], 200);
    }

    public function updatePositions(Request $request)
    {
        $validated = $request->validate([
            'level' => 'required|string',
            'categories' => 'required|array',
            'categories.*' => 'string',
        ]);

        $level = strtolower($validated['level']);
        $categories = $validated['categories'];

        foreach ($categories as $index => $categoryName) {
            LearnSongCategory::where('level', $level)
                ->where('category', $categoryName)
                ->update(['position' => $index + 1]);
        }

        return response()->json([
            'message' => 'Category positions updated successfully',
        ]);
    }
}
