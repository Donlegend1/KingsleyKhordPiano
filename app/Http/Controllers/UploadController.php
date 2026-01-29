<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use App\Http\Requests\StoreUploadRequest;
use App\Http\Requests\UpdateUploadRequest;
use App\Models\User;
use App\Notifications\NewUploadCreated;
use App\Enums\Roles\UserRoles;
use Illuminate\Http\Request;
use App\Helpers\VideoHelper;

class UploadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function pianoExercise()
    {
        // dd(Upload::where('category', 'piano exercise')->get());

        return view('admin.uploads.piano-exercise', [
            'uploads' => Upload::where('category', 'piano exercise')->get(),
        ]);
    }
    public function extraCourses()
    {
        return view('admin.uploads.extra-courses', [
            'uploads' => Upload::where('category', 'extra courses')->get(),
        ]);
    }
    public function quickLessons()
    {
        return view('admin.uploads.quick-lessons', [
            'uploads' => Upload::where('category', 'quick lessons')->get(),
        ]);
    }
    public function learnSongs()
    {
        return view('admin.uploads.learn-songs', [
            'uploads' => Upload::where('category', 'learn songs')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.uploads.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUploadRequest $request)
    {
        $validated = $request->validated();

        $videoType = $validated['video_type'] ?? 'iframe';
        $videoPath = $validated['video_url'] ?? null;

        switch ($videoType) {
            case 'youtube':
                $validated['video_url'] = VideoHelper::extractYoutubeId($videoPath);
                break;

            case 'google':
                $validated['video_url'] = VideoHelper::extractGoogleDriveId($videoPath);
                break;

            case 'local':
                // assume file already uploaded elsewhere
                $validated['video_url'] = $videoPath;
                break;

            case 'iframe':
            default:
                $validated['video_url'] = $videoPath;
                break;
        }

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

        $upload = Upload::create([
            'title'        => $validated['title'],
            'category'     => $validated['category'],
            'description'  => $validated['description'],
            'video_type'   => $videoType,
            'video_url'    => $validated['video_url'],
            'level'        => $validated['level'],
            'skill_level'  => $validated['skill_level'] ?? null,
            'status'       => $validated['status'],
            'tags'         => $validated['tags'] ?? null,
            'thumbnail'    => $validated['thumbnail'] ?? null,
        ]);

        $members = User::where('role', UserRoles::MEMBER->value)->get();

        foreach ($members as $member) {
            $member->notify(new NewUploadCreated($upload));
        }

        return response()->json($upload, 200);
    }


    /**
     * Display the specified resource.
     */
    public function show(Upload $upload)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Upload $upload)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(UpdateUploadRequest $request, Upload $upload)
    {
        $validated = $request->validated();
         logger()->info(['vedoe' => $validated]);

        if (isset($validated['video_type'])) {
            $videoType = $validated['video_type'];
            $videoPath = $validated['video_url'] ?? null;

            switch ($videoType) {
                case 'youtube':
                    $validated['video_url'] = VideoHelper::extractYoutubeId($videoPath);
                    break;

                case 'google':
                    $validated['video_url'] = VideoHelper::extractGoogleDriveId($videoPath);
                    break;

                case 'local':
                    $validated['video_url'] = $videoPath;
                    break;

                case 'iframe':
                default:
                    $validated['video_url'] = $videoPath;
                    break;
            }
        }

        /* ---------------- THUMBNAIL UPLOAD ---------------- */

        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $filename = time() . '_' . $thumbnail->getClientOriginalName();

            $destination = base_path('../public_html/uploads/thumbnails');

            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            // Delete old thumbnail if it exists
            if ($upload->thumbnail && file_exists(public_path($upload->thumbnail))) {
                unlink(public_path($upload->thumbnail));
            }

            $thumbnail->move($destination, $filename);
            $validated['thumbnail'] = 'uploads/thumbnails/' . $filename;
        }

        /* ---------------- UPDATE MODEL ---------------- */

        $upload->update([
            'title'        => $validated['title'] ?? $upload->title,
            'category'     => $validated['category'] ?? $upload->category,
            'description'  => $validated['description'] ?? $upload->description,
            'video_type'   => $validated['video_type'] ?? $upload->video_type,
            'video_url'    => $validated['video_url'] ?? $upload->video_url,
            'level'        => $validated['level'] ?? $upload->level,
            'skill_level'  => $validated['skill_level'] ?? $upload->skill_level,
            'status'       => $validated['status'] ?? $upload->status,
            'tags'         => $validated['tags'] ?? $upload->tags,
            'thumbnail'    => $validated['thumbnail'] ?? $upload->thumbnail,
        ]);
        logger()->info(['video' => $validated['video_type']]);
        
        return response()->json([
            'message' => 'Upload updated successfully',
            'upload' => $upload->fresh(),
            'thumbnail_url' => $upload->thumbnail
                ? asset($upload->thumbnail)
                : null,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Upload $upload)
    {
        $upload->delete();
        return response()->json(['message' => 'Course deleted successfully']);
    }

    public function uploadList(Request $request)
    {
        if ($request->has('page')) {
            $uploads = Upload::where('category', $request->input('category'))->latest()->paginate(10);
        } else {
            $uploads = Upload::where('category', $request->input('category'))->latest()->get();
        }

        return response()->json($uploads, 200);
    }
}
