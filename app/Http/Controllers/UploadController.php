<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use App\Http\Requests\StoreUploadRequest;
use App\Http\Requests\UpdateUploadRequest;

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
    ]);

    // Handle file upload
    if ($request->hasFile('thumbnail')) {
        $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
    }
    $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');

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
        //
    }

    public function uploadList(){
        $uploads = Upload::paginate(10);
        return  response()->json($uploads, 200);
    }
}
