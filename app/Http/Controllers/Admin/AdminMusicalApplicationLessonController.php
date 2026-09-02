<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Roles\UserRoles;
use App\Http\Controllers\Admin\Concerns\HandlesLessonMedia;
use App\Http\Controllers\Controller;
use App\Models\MusicalApplication;
use App\Models\MusicalApplicationCategory;
use App\Models\User;
use App\Notifications\NewMusicalApplicationCreated;
use Illuminate\Http\Request;

class AdminMusicalApplicationLessonController extends Controller
{
    use HandlesLessonMedia;

    private array $levels = ['beginner', 'intermediate', 'advanced'];

    public function list()
    {
        $payload = [];
        foreach ($this->levels as $level) {
            $categories = MusicalApplicationCategory::where('level', $level)
                ->orderBy('position')
                ->get(['id', 'category', 'position']);

            $skillLevel = ucfirst($level);
            $categories->load(['lessons' => function ($q) use ($skillLevel) {
                $q->where('skill_level', $skillLevel)->orderBy('position');
            }]);

            $data = [];
            foreach ($categories as $cat) {
                $data[$cat->category] = $cat->lessons->values();
            }

            $payload[$level] = [
                'data' => $data,
                'current_page' => 1,
                'last_page' => 1,
            ];
        }

        return response()->json($payload);
    }

    public function allLessons()
    {
        return response()->json(
            MusicalApplication::get(['id', 'title', 'skill_level'])
        );
    }

    public function storeLesson(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'level' => 'required|string',
            'video_type' => 'required|string',
            'video_url' => 'required|string',
            'status' => 'required|string',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:5000',
            'audio_resource' => 'nullable|file|mimes:mp3,wav,ogg,m4a|max:20480',
            'pdf_resource' => 'nullable|file|mimes:pdf|max:20480',
            'midi_resource' => 'nullable|file|mimes:mid,midi|max:20480',
            'related_lessons' => 'nullable|array',
        ]);

        $level = strtolower($request->input('level'));
        $category = MusicalApplicationCategory::where('category', $request->input('category'))
            ->where('level', $level)
            ->first();

        if (! $category) {
            return response()->json(['message' => 'Category not found'], 400);
        }

        $media = $this->collectMediaFromRequest($request, null, false);
        $maxPos = MusicalApplication::where('musical_application_category_id', $category->id)->max('position') ?: 0;

        $lesson = MusicalApplication::create([
            'musical_application_category_id' => $category->id,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'video_type' => $request->input('video_type'),
            'video_url' => $this->processVideoUrl($request->input('video_type'), $request->input('video_url'), 'embed'),
            'thumbnail' => $media['thumbnail'],
            'skill_level' => ucfirst($level),
            'series' => $category->category,
            'status' => $request->input('status'),
            'position' => $maxPos + 1,
            'tags' => $request->input('related_lessons'),
            'audio_resource' => $media['audio_resource'],
            'pdf_resource' => $media['pdf_resource'],
            'midi_resource' => $media['midi_resource'],
        ]);

        $members = User::where('role', UserRoles::MEMBER->value)->get();
        foreach ($members as $member) {
            $member->notify(new NewMusicalApplicationCreated($lesson));
        }

        return response()->json($lesson, 201);
    }

    public function updateLesson(Request $request, $id)
    {
        $lesson = MusicalApplication::findOrFail($id);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'video_type' => 'nullable|string',
            'video_url' => 'nullable|string',
            'status' => 'nullable|string',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:5000',
            'audio_resource' => 'nullable|file|mimes:mp3,wav,ogg,m4a|max:20480',
            'pdf_resource' => 'nullable|file|mimes:pdf|max:20480',
            'midi_resource' => 'nullable|file|mimes:mid,midi|max:20480',
            'related_lessons' => 'nullable|array',
        ]);

        $videoType = $request->input('video_type') ?? $lesson->video_type;
        $videoUrl = $lesson->video_url;
        if ($request->has('video_url')) {
            $videoUrl = $this->processVideoUrl($videoType, $request->input('video_url'), 'embed');
        }

        $media = $this->collectMediaFromRequest($request, $lesson, false);

        $lesson->update([
            'title' => $request->input('title') ?? $lesson->title,
            'description' => $request->input('description') ?? $lesson->description,
            'video_type' => $videoType,
            'video_url' => $videoUrl,
            'thumbnail' => $media['thumbnail'],
            'status' => $request->input('status') ?? $lesson->status,
            'tags' => $request->has('related_lessons') ? $request->input('related_lessons') : $lesson->tags,
            'audio_resource' => $media['audio_resource'],
            'pdf_resource' => $media['pdf_resource'],
            'midi_resource' => $media['midi_resource'],
        ]);

        return response()->json($lesson, 200);
    }

    public function deleteLesson($id)
    {
        $lesson = MusicalApplication::findOrFail($id);
        $this->deletePublicFile($lesson->thumbnail);
        $lesson->delete();

        return response()->json(['message' => 'Lesson deleted successfully'], 200);
    }

    public function createCategory(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'level' => 'required|string|max:255',
        ]);

        $level = strtolower($request->input('level'));
        $maxPos = MusicalApplicationCategory::where('level', $level)->max('position') ?: 0;

        $category = MusicalApplicationCategory::create([
            'category' => $request->input('category'),
            'level' => $level,
            'position' => $maxPos + 1,
        ]);

        return response()->json($category, 201);
    }

    public function deleteCategory(Request $request, $name)
    {
        $query = MusicalApplicationCategory::where('category', $name);
        if ($request->filled('level')) {
            $query->where('level', strtolower($request->input('level')));
        }
        $category = $query->firstOrFail();

        if (MusicalApplication::where('musical_application_category_id', $category->id)->exists()) {
            return response()->json([
                'message' => 'Cannot delete category because it has lessons assigned.',
            ], 400);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully'], 200);
    }

    public function updateCategory(Request $request, $name)
    {
        $request->validate([
            'category' => 'required|string|max:255',
        ]);

        $query = MusicalApplicationCategory::where('category', $name);
        if ($request->filled('level')) {
            $query->where('level', strtolower($request->input('level')));
        }
        $category = $query->firstOrFail();
        $category->update(['category' => $request->input('category')]);

        MusicalApplication::where('musical_application_category_id', $category->id)
            ->update(['series' => $category->category]);

        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $category,
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
        foreach ($validated['categories'] as $index => $categoryName) {
            MusicalApplicationCategory::where('level', $level)
                ->where('category', $categoryName)
                ->update(['position' => $index + 1]);
        }

        return response()->json(['message' => 'Category positions updated successfully']);
    }

    public function updateLessonPositions(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*' => 'integer',
        ]);

        foreach ($validated['items'] as $index => $id) {
            MusicalApplication::where('id', $id)->update(['position' => $index + 1]);
        }

        return response()->json(['message' => 'Lesson positions updated successfully']);
    }
}
