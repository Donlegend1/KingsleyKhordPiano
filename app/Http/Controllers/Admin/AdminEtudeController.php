<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Etude;
use App\Models\EtudeCategory;
use App\Models\User;
use App\Notifications\NewEtudeCreated;
use App\Enums\Roles\UserRoles;
use App\Helpers\VideoHelper;
use Illuminate\Support\Facades\DB;

class AdminEtudeController extends Controller
{
    public function list(Request $request)
    {
        $categories = EtudeCategory::orderBy('position')
            ->get(['id', 'category', 'position']);

        $categories->load(['etudes' => function ($q) {
            $q->orderBy('position');
        }]);

        $data = [];
        foreach ($categories as $cat) {
            $data[$cat->category] = $cat->etudes->values();
        }

        return response()->json([
            'data' => $data,
            'current_page' => 1,
            'last_page' => 1,
        ]);
    }

    public function allEtudes()
    {
        return response()->json(Etude::get(['id', 'title']));
    }

    public function storeEtude(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'category' => 'required|string', // Category name
            'video_type' => 'required|string',
            'video_url' => 'required|string',
            'status' => 'required|string',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:5000',
            'related_etudes' => 'nullable|array',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
            'audio_resource' => 'nullable|file|mimes:mp3,wav,ogg,m4a|max:20480',
            'pdf_resource' => 'nullable|file|mimes:pdf|max:20480',
        ]);

        $categoryName = $request->input('category');

        $category = EtudeCategory::where('category', $categoryName)->first();

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

        $maxPos = Etude::where('etude_category_id', $category->id)->max('position') ?: 0;

        $etude = Etude::create([
            'etude_category_id' => $category->id,
            'title' => $request->input('title'),
            'author' => $request->input('author'),
            'description' => $request->input('description'),
            'video_type' => $videoType,
            'video_url' => $videoUrl,
            'thumbnail' => $thumbnailPath,
            'status' => $request->input('status'),
            'position' => $maxPos + 1,
            'related_etudes' => $request->input('related_etudes'),
            'images' => $descriptionImages,
            'audio_resource' => $audioResourcePath,
            'pdf_resource' => $pdfResourcePath,
        ]);

        $members = User::where('role', UserRoles::MEMBER->value)->get();
        foreach ($members as $member) {
            $member->notify(new NewEtudeCreated($etude));
        }

        return response()->json($etude, 201);
    }

    public function updateEtude(Request $request, $id)
    {
        $etude = Etude::findOrFail($id);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'author' => 'nullable|string|max:255',
            'video_type' => 'nullable|string',
            'video_url' => 'nullable|string',
            'status' => 'nullable|string',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:5000',
            'related_etudes' => 'nullable|array',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
            'audio_resource' => 'nullable|file|mimes:mp3,wav,ogg,m4a|max:20480',
            'pdf_resource' => 'nullable|file|mimes:pdf|max:20480',
        ]);

        $videoType = $request->input('video_type') ?? $etude->video_type;
        $videoUrl = $request->input('video_url') ?? $etude->video_url;

        if ($request->has('video_url')) {
            if ($videoType === 'youtube') {
                $videoUrl = VideoHelper::extractYoutubeId($videoUrl);
            } elseif ($videoType === 'google') {
                $videoUrl = VideoHelper::extractGoogleDriveId($videoUrl);
            }
        }

        $thumbnailPath = $etude->thumbnail;
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

            if ($etude->thumbnail && file_exists(public_path($etude->thumbnail))) {
                @unlink(public_path($etude->thumbnail));
            }

            $thumbnail->move($destination, $filename);
            $thumbnailPath = 'uploads/thumbnails/' . $filename;
        }

        $descriptionImages = $etude->images;
        if ($request->hasFile('images')) {
            $destination = base_path('../public_html/uploads/descriptions');
            if (!file_exists($destination)) {
                $destination = public_path('uploads/descriptions');
            }
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            // Delete old images
            if ($etude->images && is_array($etude->images)) {
                foreach ($etude->images as $oldImg) {
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

        $audioResourcePath = $etude->audio_resource;
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
            if ($etude->audio_resource && file_exists(public_path($etude->audio_resource))) {
                @unlink(public_path($etude->audio_resource));
            }
            $audio->move($destination, $filename);
            $audioResourcePath = 'uploads/resources/audio/' . $filename;
        }

        $pdfResourcePath = $etude->pdf_resource;
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
            if ($etude->pdf_resource && file_exists(public_path($etude->pdf_resource))) {
                @unlink(public_path($etude->pdf_resource));
            }
            $pdf->move($destination, $filename);
            $pdfResourcePath = 'uploads/resources/pdf/' . $filename;
        }

        $etude->update([
            'title' => $request->input('title') ?? $etude->title,
            'author' => $request->has('author') ? $request->input('author') : $etude->author,
            'description' => $request->input('description') ?? $etude->description,
            'video_type' => $videoType,
            'video_url' => $videoUrl,
            'thumbnail' => $thumbnailPath,
            'status' => $request->input('status') ?? $etude->status,
            'related_etudes' => $request->input('related_etudes') ?? $etude->related_etudes,
            'images' => $descriptionImages,
            'audio_resource' => $audioResourcePath,
            'pdf_resource' => $pdfResourcePath,
        ]);

        return response()->json($etude, 200);
    }

    public function deleteEtude($id)
    {
        $etude = Etude::findOrFail($id);
        if ($etude->thumbnail && file_exists(public_path($etude->thumbnail))) {
            @unlink(public_path($etude->thumbnail));
        }
        $etude->delete();
        return response()->json(['message' => 'Etude/Piece deleted successfully'], 200);
    }

    public function createCategory(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:255',
        ]);

        $categoryName = $request->input('category');

        $maxPos = EtudeCategory::max('position') ?: 0;

        $category = EtudeCategory::create([
            'category' => $categoryName,
            'position' => $maxPos + 1,
        ]);

        return response()->json($category, 201);
    }

    public function deleteCategory($name)
    {
        $category = EtudeCategory::where('category', $name)->firstOrFail();
        
        $existingEtude = Etude::where('etude_category_id', $category->id)->first();
        if ($existingEtude) {
            return response()->json([
                'message' => 'Cannot delete category because it has etudes/pieces assigned.'
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
            'categories' => 'required|array',
            'categories.*' => 'string',
        ]);

        $categories = $validated['categories'];

        foreach ($categories as $index => $categoryName) {
            EtudeCategory::where('category', $categoryName)
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

        $category = EtudeCategory::where('category', $name)->firstOrFail();
        $category->update([
            'category' => $request->input('category'),
        ]);

        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $category
        ], 200);
    }

    public function updateEtudePositions(Request $request)
    {
        $validated = $request->validate([
            'etudes' => 'required|array',
            'etudes.*' => 'integer',
        ]);

        foreach ($validated['etudes'] as $index => $id) {
            Etude::where('id', $id)->update(['position' => $index + 1]);
        }

        return response()->json([
            'message' => 'Positions updated successfully',
        ]);
    }
}
