<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class AdminStudentChallengeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $challenges = Post::where('subcategory', 'student_challenges')
            ->whereNull('parent_post_id')
            ->withCount(['submissions'])
            ->orderByDesc('is_pinned')
            ->latest()
            ->get();

        return view('admin.student-challenges.index', compact('challenges'));
    }

    public function create()
    {
        return view('admin.student-challenges.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'nullable|string|max:500',
            'is_pinned' => 'nullable|boolean',
        ]);

        $challenge = Post::create([
            'title' => $request->input('title'),
            'category' => 'forum',
            'subcategory' => 'student_challenges',
            'user_id' => auth()->id(),
            'parent_post_id' => null,
            'is_pinned' => $request->boolean('is_pinned'),
            'video_url' => $request->input('video_url'),
        ]);

        if ($request->filled('description')) {
            $challenge->blocks()->create([
                'type' => 'text',
                'content' => $request->input('description'),
                'position' => 0,
            ]);
        }

        return redirect()->route('admin.student-challenges.index')->with('success', 'Challenge topic created successfully!');
    }

    public function edit(Post $studentChallenge)
    {
        $description = $studentChallenge->blocks()->where('type', 'text')->value('content') ?? '';

        return view('admin.student-challenges.edit', ['challenge' => $studentChallenge, 'description' => $description]);
    }

    public function update(Request $request, Post $studentChallenge)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'nullable|string|max:500',
            'is_pinned' => 'nullable|boolean',
        ]);

        $studentChallenge->update([
            'title' => $request->input('title'),
            'is_pinned' => $request->boolean('is_pinned'),
            'video_url' => $request->input('video_url'),
        ]);

        $textBlock = $studentChallenge->blocks()->where('type', 'text')->first();
        if ($textBlock) {
            $textBlock->update(['content' => $request->input('description', '')]);
        } elseif ($request->filled('description')) {
            $studentChallenge->blocks()->create([
                'type' => 'text',
                'content' => $request->input('description'),
                'position' => 0,
            ]);
        }

        return redirect()->route('admin.student-challenges.index')->with('success', 'Challenge topic updated successfully!');
    }

    public function destroy(Post $studentChallenge)
    {
        $studentChallenge->delete();

        return redirect()->route('admin.student-challenges.index')->with('success', 'Challenge topic deleted successfully!');
    }
}
