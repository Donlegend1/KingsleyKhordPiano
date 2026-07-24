<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LearnSong;
use App\Models\LearnSongCategory;
use App\Models\User;
use App\Notifications\NewLearnSongCreated;
use App\Enums\Roles\UserRoles;
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
            'author' => 'nullable|string|max:255',
            'category' => 'required|string', // Category name
            'level' => 'required|string',
            'video_type' => 'required|string',
            'video_url' => 'required|string',
            'status' => 'required|string',
            'description' => 'nullable|string',
            'tonal_center' => 'nullable|string|max:10',
            'thumbnail' => 'nullable|image|max:5000',
            'related_songs' => 'nullable|array',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
            'audio_resource' => 'nullable|file|mimes:mp3,wav,ogg,m4a|max:20480',
            'pdf_resource' => 'nullable|file|mimes:pdf|max:20480',
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

        $descriptionImages = [];
        if ($request->hasFile('images')) {
            $destination = base_path('../public_html/uploads/descriptions');
            if (!file_exists($destination)) {
                $destination = public_path('uploads/descriptions');
            }
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            foreach ($request->file('images') as $imgFile) {
                $filename = time() . '_' . uniqid() . '_' . $imgFile->getClientOriginalName();
                $imgFile->move($destination, $filename);
                $descriptionImages[] = 'uploads/descriptions/' . $filename;
            }
        }

        $audioResourcePath = null;
        if ($request->hasFile('audio_resource')) {
            $audio = $request->file('audio_resource');
            $filename = time() . '_' . $audio->getClientOriginalName();
            $destination = base_path('../public_html/uploads/resources/audio');
            if (!file_exists($destination)) {
                $destination = public_path('uploads/resources/audio');
            }
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            $audio->move($destination, $filename);
            $audioResourcePath = 'uploads/resources/audio/' . $filename;
        }

        $pdfResourcePath = null;
        if ($request->hasFile('pdf_resource')) {
            $pdf = $request->file('pdf_resource');
            $filename = time() . '_' . $pdf->getClientOriginalName();
            $destination = base_path('../public_html/uploads/resources/pdf');
            if (!file_exists($destination)) {
                $destination = public_path('uploads/resources/pdf');
            }
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            $pdf->move($destination, $filename);
            $pdfResourcePath = 'uploads/resources/pdf/' . $filename;
        }

        $maxPos = LearnSong::where('learn_song_category_id', $category->id)->max('position') ?: 0;

        $song = LearnSong::create([
            'learn_song_category_id' => $category->id,
            'title' => $request->input('title'),
            'author' => $request->input('author'),
            'description' => $request->input('description'),
            'video_type' => $videoType,
            'video_url' => $videoUrl,
            'thumbnail' => $thumbnailPath,
            'level' => $level,
            'tonal_center' => $request->input('tonal_center'),
            'status' => $request->input('status'),
            'position' => $maxPos + 1,
            'related_songs' => $request->input('related_songs'),
            'images' => $descriptionImages,
            'audio_resource' => $audioResourcePath,
            'pdf_resource' => $pdfResourcePath,
        ]);

        $members = User::where('role', UserRoles::MEMBER->value)->get();
        foreach ($members as $member) {
            $member->notify(new NewLearnSongCreated($song));
        }

        return response()->json($song, 201);
    }

    public function updateSong(Request $request, $id)
    {
        $song = LearnSong::findOrFail($id);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'author' => 'nullable|string|max:255',
            'video_type' => 'nullable|string',
            'video_url' => 'nullable|string',
            'status' => 'nullable|string',
            'description' => 'nullable|string',
            'tonal_center' => 'nullable|string|max:10',
            'thumbnail' => 'nullable|image|max:5000',
            'related_songs' => 'nullable|array',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
            'audio_resource' => 'nullable|file|mimes:mp3,wav,ogg,m4a|max:20480',
            'pdf_resource' => 'nullable|file|mimes:pdf|max:20480',
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

        $descriptionImages = $song->images;
        if ($request->hasFile('images')) {
            $destination = base_path('../public_html/uploads/descriptions');
            if (!file_exists($destination)) {
                $destination = public_path('uploads/descriptions');
            }
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            // Delete old images
            if ($song->images && is_array($song->images)) {
                foreach ($song->images as $oldImg) {
                    if (file_exists(public_path($oldImg))) {
                        @unlink(public_path($oldImg));
                    }
                }
            }

            $descriptionImages = [];
            foreach ($request->file('images') as $imgFile) {
                $filename = time() . '_' . uniqid() . '_' . $imgFile->getClientOriginalName();
                $imgFile->move($destination, $filename);
                $descriptionImages[] = 'uploads/descriptions/' . $filename;
            }
        }

        $audioResourcePath = $song->audio_resource;
        if ($request->hasFile('audio_resource')) {
            $audio = $request->file('audio_resource');
            $filename = time() . '_' . $audio->getClientOriginalName();
            $destination = base_path('../public_html/uploads/resources/audio');
            if (!file_exists($destination)) {
                $destination = public_path('uploads/resources/audio');
            }
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            if ($song->audio_resource && file_exists(public_path($song->audio_resource))) {
                @unlink(public_path($song->audio_resource));
            }
            $audio->move($destination, $filename);
            $audioResourcePath = 'uploads/resources/audio/' . $filename;
        }

        $pdfResourcePath = $song->pdf_resource;
        if ($request->hasFile('pdf_resource')) {
            $pdf = $request->file('pdf_resource');
            $filename = time() . '_' . $pdf->getClientOriginalName();
            $destination = base_path('../public_html/uploads/resources/pdf');
            if (!file_exists($destination)) {
                $destination = public_path('uploads/resources/pdf');
            }
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            if ($song->pdf_resource && file_exists(public_path($song->pdf_resource))) {
                @unlink(public_path($song->pdf_resource));
            }
            $pdf->move($destination, $filename);
            $pdfResourcePath = 'uploads/resources/pdf/' . $filename;
        }

        $song->update([
            'title' => $request->input('title') ?? $song->title,
            'author' => $request->has('author') ? $request->input('author') : $song->author,
            'description' => $request->input('description') ?? $song->description,
            'video_type' => $videoType,
            'video_url' => $videoUrl,
            'thumbnail' => $thumbnailPath,
            'tonal_center' => $request->input('tonal_center') ?? $song->tonal_center,
            'status' => $request->input('status') ?? $song->status,
            'related_songs' => $request->input('related_songs') ?? $song->related_songs,
            'images' => $descriptionImages,
            'audio_resource' => $audioResourcePath,
            'pdf_resource' => $pdfResourcePath,
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

    public function updateCategory(Request $request, $name)
    {
        $request->validate([
            'category' => 'required|string|max:255',
        ]);

        $category = LearnSongCategory::where('category', $name)->firstOrFail();
        $category->update([
            'category' => $request->input('category'),
        ]);

        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $category
        ], 200);
    }

    public function updateSongPositions(Request $request)
    {
        $validated = $request->validate([
            'songs' => 'required|array',
            'songs.*' => 'integer',
        ]);

        foreach ($validated['songs'] as $index => $id) {
            LearnSong::where('id', $id)->update(['position' => $index + 1]);
        }

        return response()->json([
            'message' => 'Song positions updated successfully',
        ]);
    }
}
