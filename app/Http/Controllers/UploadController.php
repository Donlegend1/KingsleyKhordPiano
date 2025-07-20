<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use App\Http\Requests\StoreUploadRequest;
use App\Http\Requests\UpdateUploadRequest;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.uploads.index', [
            'uploads' => Upload::all(),
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
    $data = $request->only([
        'title',
        'category',
        'description',
        'video_url',
        'level',
        'skill_level',
        'status',
        'tags',
    ]);

    // Handle file upload manually using move()
   if ($request->hasFile('thumbnail') && $request->file('thumbnail') !== null) {
        $thumbnail = $request->file('thumbnail');
        $filename = time() . '_' . $thumbnail->getClientOriginalName();
        $destination = public_path('uploads/thumbnails');

        // Ensure destination exists
        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        $thumbnail->move($destination, $filename);
        $data['thumbnail'] = 'uploads/thumbnails/' . $filename;
    }


    $upload = Upload::create($data);

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
        //
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
            $uploads = Upload::paginate(10);
        } else {
            $uploads = Upload::all();
        }

        return response()->json($uploads, 200);
    }
}
