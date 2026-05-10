<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MusicalApplication;
use Illuminate\Http\Request;
use App\Helpers\VideoHelper;

class MusicalApplicationController extends Controller
{
    public function index()
    {
        $uploads = MusicalApplication::latest()->get();
        return view('admin.musical-application.index', compact('uploads'));
    }

    public function create()
    {
        return view('admin.musical-application.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'required|string',
            'video_type' => 'required|string',
            'skill_level' => 'required|in:Beginner,Intermediate,Advanced',
            'series' => 'nullable|string',
            'status' => 'required|in:active,inactive,draft',
            'thumbnail' => 'nullable|image|max:2048',
            'tags' => 'nullable|array',
        ]);

        $videoPath = $validated['video_url'];

        $validated['video_url'] = VideoHelper::linkToEmbed($videoPath);

        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $filename = time() . '_' . $thumbnail->getClientOriginalName();
            $destination = base_path('../public_html/uploads/thumbnails');

            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            $thumbnail->move($destination, $filename);
            $validated['thumbnail'] = 'uploads/thumbnails/' . $filename;
        }

        $upload = MusicalApplication::create($validated);

        if ($request->ajax()) {
            return response()->json($upload, 201);
        }

        return redirect()->route('admin.musical-application.index')->with('success', 'Musical Application exercise created successfully.');
    }

    public function edit(MusicalApplication $musicalApplication)
    {
        return view('admin.musical-application.edit', compact('musicalApplication'));
    }

    public function update(Request $request, MusicalApplication $musicalApplication)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'required|string',
            'video_type' => 'required|string',
            'skill_level' => 'required|in:Beginner,Intermediate,Advanced',
            'series' => 'nullable|string',
            'status' => 'required|in:active,inactive,draft',
            'thumbnail' => 'nullable|image|max:2048',
            'tags' => 'nullable|array',
        ]);

        if (isset($validated['video_type'])) {
            $videoPath = $validated['video_url'];

            $validated['video_url'] = VideoHelper::linkToEmbed($videoPath);
        }

        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $filename = time() . '_' . $thumbnail->getClientOriginalName();
            $destination = base_path('../public_html/uploads/thumbnails');

            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            if ($musicalApplication->thumbnail && file_exists(public_path($musicalApplication->thumbnail))) {
                unlink(public_path($musicalApplication->thumbnail));
            }

            $thumbnail->move($destination, $filename);
            $validated['thumbnail'] = 'uploads/thumbnails/' . $filename;
        }

        $musicalApplication->update($validated);

        if ($request->ajax()) {
            return response()->json($musicalApplication, 200);
        }

        return redirect()->route('admin.musical-application.index')->with('success', 'Musical Application exercise updated successfully.');
    }

    public function destroy(MusicalApplication $musicalApplication)
    {
        if ($musicalApplication->thumbnail && file_exists(public_path($musicalApplication->thumbnail))) {
            unlink(public_path($musicalApplication->thumbnail));
        }
        $musicalApplication->delete();
        return redirect()->route('admin.musical-application.index')->with('success', 'Musical Application exercise deleted successfully.');
    }

    public function musicalApplicationList(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $uploads = MusicalApplication::latest()->paginate($perPage);

        return response()->json($uploads, 200);
    }

    public function getAllCourses()
    {
        $uploads = \App\Models\Upload::select('id', 'title')->get();
        return response()->json($uploads);
    }
}
