<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MusicalApplication;
use App\Models\User;
use App\Notifications\NewMusicalApplicationCreated;
use App\Enums\Roles\UserRoles;
use Illuminate\Http\Request;
use App\Helpers\VideoHelper;

class MusicalApplicationController extends Controller
{
    public function index()
    {
        $uploads = MusicalApplication::orderByRaw('position IS NULL, position ASC')->orderBy('id', 'desc')->get();
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
            'audio_resource' => 'nullable|file|mimes:mp3,wav,ogg,m4a|max:20480',
            'pdf_resource' => 'nullable|file|mimes:pdf|max:20480',
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

        if ($request->hasFile('audio_resource')) {
            $audio = $request->file('audio_resource');
            $filename = time() . '_' . $audio->getClientOriginalName();
            $destination = base_path('../public_html/uploads/resources/audio');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            $audio->move($destination, $filename);
            $validated['audio_resource'] = 'uploads/resources/audio/' . $filename;
        }

        if ($request->hasFile('pdf_resource')) {
            $pdf = $request->file('pdf_resource');
            $filename = time() . '_' . $pdf->getClientOriginalName();
            $destination = base_path('../public_html/uploads/resources/pdf');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            $pdf->move($destination, $filename);
            $validated['pdf_resource'] = 'uploads/resources/pdf/' . $filename;
        }

        $upload = MusicalApplication::create($validated);

        $members = User::where('role', UserRoles::MEMBER->value)->get();
        foreach ($members as $member) {
            $member->notify(new NewMusicalApplicationCreated($upload));
        }

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
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'required|string',
            'video_type' => 'required|string',
            'skill_level' => 'required|in:Beginner,Intermediate,Advanced',
            'series' => 'nullable|string',
            'status' => 'required|in:active,inactive,draft',
            'tags' => 'nullable|array',
        ];

        if ($request->hasFile('thumbnail')) {
            $rules['thumbnail'] = 'image|max:2048';
        }
        if ($request->hasFile('audio_resource')) {
            $rules['audio_resource'] = 'file|mimes:mp3,wav,ogg,m4a|max:20480';
        }
        if ($request->hasFile('pdf_resource')) {
            $rules['pdf_resource'] = 'file|mimes:pdf|max:20480';
        }

        $validated = $request->validate($rules);

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

        if ($request->hasFile('audio_resource')) {
            $audio = $request->file('audio_resource');
            $filename = time() . '_' . $audio->getClientOriginalName();
            $destination = base_path('../public_html/uploads/resources/audio');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            if ($musicalApplication->audio_resource && file_exists(public_path($musicalApplication->audio_resource))) {
                @unlink(public_path($musicalApplication->audio_resource));
            }
            $audio->move($destination, $filename);
            $validated['audio_resource'] = 'uploads/resources/audio/' . $filename;
        }

        if ($request->hasFile('pdf_resource')) {
            $pdf = $request->file('pdf_resource');
            $filename = time() . '_' . $pdf->getClientOriginalName();
            $destination = base_path('../public_html/uploads/resources/pdf');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            if ($musicalApplication->pdf_resource && file_exists(public_path($musicalApplication->pdf_resource))) {
                @unlink(public_path($musicalApplication->pdf_resource));
            }
            $pdf->move($destination, $filename);
            $validated['pdf_resource'] = 'uploads/resources/pdf/' . $filename;
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
        $uploads = MusicalApplication::orderByRaw('position IS NULL, position ASC')->orderBy('id', 'desc')->paginate($perPage);

        return response()->json($uploads, 200);
    }

    public function getAllCourses()
    {
        $uploads = MusicalApplication::select('id', 'title')->get();
        return response()->json($uploads);
    }

    public function updatePositions(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*' => 'integer',
        ]);

        foreach ($validated['items'] as $index => $id) {
            MusicalApplication::where('id', $id)->update(['position' => $index + 1]);
        }

        return response()->json([
            'message' => 'Musical Application positions updated successfully',
        ]);
    }
}
