<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCheckpoint;
use App\Support\Checkpoints\CheckpointCatalog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseCheckpointController extends Controller
{
    public function catalog()
    {
        return response()->json(CheckpointCatalog::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_category_id' => 'required|exists:course_categories,id',
            'checkpoint_key' => ['required', 'string', Rule::in(array_keys(collect(CheckpointCatalog::all())->pluck('key', 'key')->all()))],
            'linked_course_id' => 'nullable|exists:courses,id',
            'redirect_url' => 'nullable|string|max:2048',
        ]);

        $nextPosition = max(
            (int) Course::where('course_category_id', $validated['course_category_id'])->max('position'),
            (int) CourseCheckpoint::where('course_category_id', $validated['course_category_id'])->max('position')
        ) + 1;

        $checkpoint = CourseCheckpoint::create([
            'course_category_id' => $validated['course_category_id'],
            'checkpoint_key' => $validated['checkpoint_key'],
            'linked_course_id' => $validated['linked_course_id'] ?? null,
            'redirect_url' => $validated['redirect_url'] ?? null,
            'position' => $nextPosition,
        ]);

        return response()->json($checkpoint->load('linkedCourse'), 201);
    }

    public function destroy(CourseCheckpoint $checkpoint)
    {
        $checkpoint->delete();

        return response()->json(['message' => 'Checkpoint deleted successfully']);
    }
}
