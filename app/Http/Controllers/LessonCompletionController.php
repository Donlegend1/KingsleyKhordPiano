<?php

namespace App\Http\Controllers;

use App\Models\LessonCompletion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonCompletionController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'completable_id'   => 'required|integer',
            'completable_type' => 'required|string',
        ]);

        $modelClass = $this->resolveModel($data['completable_type']);
        if (!$modelClass) {
            return $this->respond($request, ['error' => 'Invalid type'], 400);
        }

        $completable = $modelClass::find($data['completable_id']);
        if (!$completable) {
            return $this->respond($request, ['error' => 'Item not found'], 404);
        }

        LessonCompletion::firstOrCreate([
            'user_id'           => Auth::id(),
            'completable_id'    => $completable->id,
            'completable_type'  => $modelClass,
        ]);

        return $this->respond($request, ['status' => 'completed']);
    }

    /**
     * Respond as JSON for AJAX callers, or redirect back for plain form submissions.
     */
    protected function respond(Request $request, array $data, int $status = 200)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($data, $status);
        }

        if ($status >= 400) {
            return back()->with('error', $data['error']);
        }

        return back()->with('success', 'Marked as completed!');
    }

    /**
     * Resolve string to model class (mirrors BookMarkController's convention).
     */
    protected function resolveModel(string $type)
    {
        return match ($type) {
            'uploads' => \App\Models\Upload::class,
            'courses' => \App\Models\Course::class,
            'learn_songs' => \App\Models\LearnSong::class,
            'extra_courses' => \App\Models\ExtraCourse::class,
            'quizzes' => \App\Models\Quiz::class,
            default => null,
        };
    }
}
