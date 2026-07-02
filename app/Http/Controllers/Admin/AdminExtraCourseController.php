<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExtraCourse;
use App\Models\ExtraCourseCategory;
use App\Models\User;
use App\Notifications\NewExtraCourseCreated;
use App\Enums\Roles\UserRoles;
use App\Helpers\VideoHelper;
use Illuminate\Support\Facades\DB;

class AdminExtraCourseController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function list(Request $request)
    {
        $fetchLevel = function ($level) {
            $categories = ExtraCourseCategory::where('level', $level)
                ->orderBy('position')
                ->get(['id', 'category', 'position']);

            $categories->load(['courses' => function ($q) use ($level) {
                $q->where('level', $level)
                  ->orderBy('position');
            }]);

            $data = [];
            foreach ($categories as $cat) {
                $data[$cat->category] = $cat->courses->values();
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

    public function allCourses()
    {
        return response()->json(ExtraCourse::get(['id', 'title', 'level']));
    }

    public function storeCourse(Request $request)
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
            'related_courses' => 'nullable|array',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
            'audio_resource' => 'nullable|file|mimes:mp3,wav,ogg,m4a|max:20480',
            'pdf_resource' => 'nullable|file|mimes:pdf|max:20480',
        ]);

        $level = strtolower($request->input('level'));
        $categoryName = $request->input('category');

        $category = ExtraCourseCategory::where('category', $categoryName)
            ->where('level', $level)
            ->first();

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 400);
        }

        $videoType = $request->input('video_type');
        $videoUrl = $request->input('video_url');

        if ($videoType === 'youtube') {
            $videoUrl = VideoHelper::linkToEmbed($videoUrl);
        } elseif ($videoType === 'google') {
            $videoUrl = VideoHelper::linkToEmbed($videoUrl);
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

        $maxPos = ExtraCourse::where('extra_course_category_id', $category->id)->max('position') ?: 0;

        $course = ExtraCourse::create([
            'extra_course_category_id' => $category->id,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'video_type' => $videoType,
            'video_url' => $videoUrl,
            'thumbnail' => $thumbnailPath,
            'level' => $level,
            'status' => $request->input('status'),
            'position' => $maxPos + 1,
            'related_courses' => $request->input('related_courses'),
            'images' => $descriptionImages,
            'audio_resource' => $audioResourcePath,
            'pdf_resource' => $pdfResourcePath,
        ]);

        $members = User::where('role', UserRoles::MEMBER->value)->get();
        foreach ($members as $member) {
            $member->notify(new NewExtraCourseCreated($course));
        }

        return response()->json($course, 201);
    }

    public function updateCourse(Request $request, $id)
    {
        $course = ExtraCourse::findOrFail($id);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'video_type' => 'nullable|string',
            'video_url' => 'nullable|string',
            'status' => 'nullable|string',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:5000',
            'related_courses' => 'nullable|array',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
            'audio_resource' => 'nullable|file|mimes:mp3,wav,ogg,m4a|max:20480',
            'pdf_resource' => 'nullable|file|mimes:pdf|max:20480',
        ]);

        $videoType = $request->input('video_type') ?? $course->video_type;
        $videoUrl = $request->input('video_url') ?? $course->video_url;

        if ($request->has('video_url')) {
            if ($videoType === 'youtube') {
                $videoUrl = VideoHelper::linkToEmbed($videoUrl);
            } elseif ($videoType === 'google') {
                $videoUrl = VideoHelper::linkToEmbed($videoUrl);
            }
        }

        $thumbnailPath = $course->thumbnail;
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

            if ($course->thumbnail && file_exists(public_path($course->thumbnail))) {
                @unlink(public_path($course->thumbnail));
            }

            $thumbnail->move($destination, $filename);
            $thumbnailPath = 'uploads/thumbnails/' . $filename;
        }

        $descriptionImages = $course->images;
        if ($request->hasFile('images')) {
            $destination = base_path('../public_html/uploads/descriptions');
            if (!file_exists($destination)) {
                $destination = public_path('uploads/descriptions');
            }
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            // Delete old images
            if ($course->images && is_array($course->images)) {
                foreach ($course->images as $oldImg) {
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

        $audioResourcePath = $course->audio_resource;
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
            if ($course->audio_resource && file_exists(public_path($course->audio_resource))) {
                @unlink(public_path($course->audio_resource));
            }
            $audio->move($destination, $filename);
            $audioResourcePath = 'uploads/resources/audio/' . $filename;
        }

        $pdfResourcePath = $course->pdf_resource;
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
            if ($course->pdf_resource && file_exists(public_path($course->pdf_resource))) {
                @unlink(public_path($course->pdf_resource));
            }
            $pdf->move($destination, $filename);
            $pdfResourcePath = 'uploads/resources/pdf/' . $filename;
        }

        $course->update([
            'title' => $request->input('title') ?? $course->title,
            'description' => $request->input('description') ?? $course->description,
            'video_type' => $videoType,
            'video_url' => $videoUrl,
            'thumbnail' => $thumbnailPath,
            'status' => $request->input('status') ?? $course->status,
            'related_courses' => $request->input('related_courses') ?? $course->related_courses,
            'images' => $descriptionImages,
            'audio_resource' => $audioResourcePath,
            'pdf_resource' => $pdfResourcePath,
        ]);

        return response()->json($course, 200);
    }

    public function deleteCourse($id)
    {
        $course = ExtraCourse::findOrFail($id);
        if ($course->thumbnail && file_exists(public_path($course->thumbnail))) {
            @unlink(public_path($course->thumbnail));
        }
        $course->delete();
        return response()->json(['message' => 'Course deleted successfully'], 200);
    }

    public function createCategory(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'level' => 'required|string|max:255',
        ]);

        $level = strtolower($request->input('level'));
        $categoryName = $request->input('category');

        $maxPos = ExtraCourseCategory::where('level', $level)->max('position') ?: 0;

        $category = ExtraCourseCategory::create([
            'category' => $categoryName,
            'level' => $level,
            'position' => $maxPos + 1,
        ]);

        return response()->json($category, 201);
    }

    public function deleteCategory($name)
    {
        $category = ExtraCourseCategory::where('category', $name)->firstOrFail();
        
        $existingCourse = ExtraCourse::where('extra_course_category_id', $category->id)->first();
        if ($existingCourse) {
            return response()->json([
                'message' => 'Cannot delete category because it has courses assigned.'
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
            ExtraCourseCategory::where('level', $level)
                ->where('category', $categoryName)
                ->update(['position' => $index + 1]);
        }

        return response()->json([
            'message' => 'Category positions updated successfully',
        ]);
    }
}
