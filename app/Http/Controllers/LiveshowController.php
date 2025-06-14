<?php

namespace App\Http\Controllers;

use App\Models\Liveshow;
use App\Http\Requests\StoreLiveshowRequest;
use App\Http\Requests\UpdateLiveshowRequest;

class LiveshowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         return view('admin.live_shows.index', [
            'liveShows' => LiveShow::latest()->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.live_shows.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLiveshowRequest $request)
    {
         LiveShow::create($request->all());

        return redirect()->back()->with('success', 'Live show created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Liveshow $liveshow)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Liveshow $liveshow)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(UpdateLiveshowRequest $request, Liveshow $liveshow)
    {
        $liveshow->update($request->only([
            'title',
            'zoom_link',
            'recording_url',
            'start_time',
            'access_type'
        ]));

        return response()->json([
            'message' => 'Live show updated successfully.',
            'data' => $liveshow
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
   public function destroy(Liveshow $liveshow)
{
    $liveshow->delete();

    return response()->json([
        'message' => 'Live show deleted successfully.'
    ]);
}

    public function list()
    {
        return response()->json(LiveShow::orderBy('start_time')->get());
    }
}
