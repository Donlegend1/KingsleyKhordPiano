<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Roles\UserRoles;
use App\Http\Controllers\Admin\Concerns\HandlesLessonMedia;
use App\Http\Controllers\Controller;
use App\Models\PianoExerciseCategory;
use App\Models\Upload;
use App\Models\User;
use App\Notifications\NewUploadCreated;
use Illuminate\Http\Request;

class AdminPianoExerciseController extends Controller
{
    use HandlesLessonMedia;

    private array $levels = ['independence', 'technique', 'flexibility', 'strength', 'dexterity'];

    public function list()
    {
        $payload = [];
        foreach ($this->levels as $level) {
            $categories = PianoExerciseCategory::where('level', $level)
                ->orderBy('position')
                ->get(['id', 'category', 'position']);

            $categories->load(['lessons' => function ($q) use ($level) {
                $q->where('category', 'piano exercise')
                    ->where('level', $level)
                    ->orderBy('position');
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
            Upload::where('category', 'piano exercise')->get(['id', 'title', 'level', 'skill_level'])
        );
    }

    public function storeLesson(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'level' => 'required|string',
            'skill_level' => 'nullable|string',
            'video_type' => 'required|string',
            'video_url' => 'required|string',
            'status' => 'required|string',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:5000',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
            'audio_resource' => 'nullable|file|mimes:mp3,wav,ogg,m4a|max:20480',
            'pdf_resource' => 'nullable|file|mimes:pdf|max:20480',
            'related_lessons' => 'nullable|array',
        ]);

        $level = strtolower($request->input('level'));
        $category = PianoExerciseCategory::where('category', $request->input('category'))
            ->where('level', $level)
            ->first();

        if (! $category) {
            return response()->json(['message' => 'Category not found'], 400);
        }

        $media = $this->collectMediaFromRequest($request, null, true);
        $maxPos = Upload::where('piano_exercise_category_id', $category->id)->max('position') ?: 0;

        $lesson = Upload::create([
            'piano_exercise_category_id' => $category->id,
            'category' => 'piano exercise',
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'video_type' => $request->input('video_type'),
            'video_url' => $this->processVideoUrl($request->input('video_type'), $request->input('video_url'), 'id'),
            'thumbnail' => $media['thumbnail'],
            'level' => $level,
            'skill_level' => $request->input('skill_level', 'Basic'),
            'series' => $category->category,
            'status' => $request->input('status'),
            'position' => $maxPos + 1,
            'tags' => $request->input('related_lessons'),
            'images' => $media['images'],
            'audio_resource' => $media['audio_resource'],
            'pdf_resource' => $media['pdf_resource'],
        ]);

        $members = User::where('role', UserRoles::MEMBER->value)->get();
        foreach ($members as $member) {
            $member->notify(new NewUploadCreated($lesson));
        }

        return response()->json($lesson, 201);
    }

    public function updateLesson(Request $request, $id)
    {
        $lesson = Upload::where('category', 'piano exercise')->findOrFail($id);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'skill_level' => 'nullable|string',
            'video_type' => 'nullable|string',
            'video_url' => 'nullable|string',
            'status' => 'nullable|string',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:5000',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
            'audio_resource' => 'nullable|file|mimes:mp3,wav,ogg,m4a|max:20480',
            'pdf_resource' => 'nullable|file|mimes:pdf|max:20480',
            'related_lessons' => 'nullable|array',
        ]);

        $videoType = $request->input('video_type') ?? $lesson->video_type;
        $videoUrl = $lesson->video_url;
        if ($request->has('video_url')) {
            $videoUrl = $this->processVideoUrl($videoType, $request->input('video_url'), 'id');
        }

        $media = $this->collectMediaFromRequest($request, $lesson, true);

        $lesson->update([
            'title' => $request->input('title') ?? $lesson->title,
            'description' => $request->input('description') ?? $lesson->description,
            'video_type' => $videoType,
            'video_url' => $videoUrl,
            'thumbnail' => $media['thumbnail'],
            'skill_level' => $request->input('skill_level') ?? $lesson->skill_level,
            'status' => $request->input('status') ?? $lesson->status,
            'tags' => $request->has('related_lessons') ? $request->input('related_lessons') : $lesson->tags,
            'images' => $media['images'],
            'audio_resource' => $media['audio_resource'],
            'pdf_resource' => $media['pdf_resource'],
        ]);

        return response()->json($lesson, 200);
    }

    public function deleteLesson($id)
    {
        $lesson = Upload::where('category', 'piano exercise')->findOrFail($id);
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
        $maxPos = PianoExerciseCategory::where('level', $level)->max('position') ?: 0;

        $category = PianoExerciseCategory::create([
            'category' => $request->input('category'),
            'level' => $level,
            'position' => $maxPos + 1,
        ]);

        return response()->json($category, 201);
    }

    public function deleteCategory(Request $request, $name)
    {
        $query = PianoExerciseCategory::where('category', $name);
        if ($request->filled('level')) {
            $query->where('level', strtolower($request->input('level')));
        }
        $category = $query->firstOrFail();

        if (Upload::where('piano_exercise_category_id', $category->id)->exists()) {
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

        $query = PianoExerciseCategory::where('category', $name);
        if ($request->filled('level')) {
            $query->where('level', strtolower($request->input('level')));
        }
        $category = $query->firstOrFail();
        $category->update(['category' => $request->input('category')]);

        Upload::where('piano_exercise_category_id', $category->id)
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
            PianoExerciseCategory::where('level', $level)
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
            Upload::where('id', $id)->where('category', 'piano exercise')->update(['position' => $index + 1]);
        }

        return response()->json(['message' => 'Lesson positions updated successfully']);
    }
}
