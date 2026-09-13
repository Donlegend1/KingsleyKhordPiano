<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCheckpoint;
use App\Models\CourseCheckpointDownload;
use Illuminate\Http\Request;

class CourseCheckpointController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_category_id' => 'required|exists:course_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'nullable|string|max:2048',
            'overview' => 'nullable|string',
            'linked_course_id' => 'nullable|exists:courses,id',
            'redirect_url' => 'nullable|string|max:2048',
            'downloads' => 'nullable|array',
            'downloads.*.title' => 'required_with:downloads|string|max:255',
            'downloads.*.file' => 'required_with:downloads|file|mimes:pdf|max:20480',
        ]);

        $nextPosition = max(
            (int) Course::where('course_category_id', $validated['course_category_id'])->max('position'),
            (int) CourseCheckpoint::where('course_category_id', $validated['course_category_id'])->max('position')
        ) + 1;

        $checkpoint = CourseCheckpoint::create([
            'course_category_id' => $validated['course_category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'overview' => $validated['overview'] ?? null,
            'linked_course_id' => $validated['linked_course_id'] ?? null,
            'redirect_url' => $validated['redirect_url'] ?? null,
            'position' => $nextPosition,
        ]);

        foreach ($request->file('downloads', []) as $index => $entry) {
            $title = $request->input("downloads.{$index}.title");
            if (!empty($entry['file']) && $title) {
                $this->storeDownloadFile($checkpoint, $title, $entry['file']);
            }
        }

        return response()->json($checkpoint->load(['linkedCourse', 'downloads']), 201);
    }

    public function update(Request $request, CourseCheckpoint $checkpoint)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'nullable|string|max:2048',
            'overview' => 'nullable|string',
            'linked_course_id' => 'nullable|exists:courses,id',
            'redirect_url' => 'nullable|string|max:2048',
        ]);

        $checkpoint->update($validated);

        return response()->json($checkpoint->fresh(['linkedCourse', 'downloads']));
    }

    public function destroy(CourseCheckpoint $checkpoint)
    {
        foreach ($checkpoint->downloads as $download) {
            if ($download->file_path && file_exists(public_path($download->file_path))) {
                @unlink(public_path($download->file_path));
            }
        }

        $checkpoint->delete();

        return response()->json(['message' => 'Checkpoint deleted successfully']);
    }

    public function storeDownload(Request $request, CourseCheckpoint $checkpoint)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf|max:20480',
        ]);

        $download = $this->storeDownloadFile($checkpoint, $validated['title'], $request->file('file'));

        return response()->json($download, 201);
    }

    private function storeDownloadFile(CourseCheckpoint $checkpoint, string $title, $file): CourseCheckpointDownload
    {
        $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
        $destination = base_path('../public_html/uploads/checkpoint-downloads');
        if (!file_exists($destination)) {
            $destination = public_path('uploads/checkpoint-downloads');
        }
        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }
        $file->move($destination, $filename);

        $nextPosition = (int) $checkpoint->downloads()->max('position') + 1;

        return CourseCheckpointDownload::create([
            'course_checkpoint_id' => $checkpoint->id,
            'title' => $title,
            'file_path' => 'uploads/checkpoint-downloads/' . $filename,
            'position' => $nextPosition,
        ]);
    }

    public function destroyDownload(CourseCheckpointDownload $download)
    {
        if ($download->file_path && file_exists(public_path($download->file_path))) {
            @unlink(public_path($download->file_path));
        }

        $download->delete();

        return response()->json(['message' => 'Download removed successfully']);
    }
}
