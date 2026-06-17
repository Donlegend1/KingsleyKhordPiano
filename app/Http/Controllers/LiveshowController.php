<?php

namespace App\Http\Controllers;

use App\Models\Liveshow;
use App\Http\Requests\StoreLiveshowRequest;
use App\Http\Requests\UpdateLiveshowRequest;
 use Carbon\Carbon;
 use Illuminate\Http\Request;

class LiveShowController extends Controller
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
    public function show()
    {
        if(auth()->user()->premium === 1) {
            return view('memberpages.premium.booking');
        }

        return redirect()->route('home')->with('error', 'You are not authorized to access this page');
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
            'access_type',
            'category',
            'max_slots'
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

    public function list(Request $request)
    {
        $filter = $request->query('filter');
        $query = LiveShow::with('bookedUsers');

        if ($filter === 'future') {
            $query->where('start_time', '>', Carbon::now())
                ->orderBy('start_time', 'asc');
        } elseif ($filter === 'previous') {
            $query->where('start_time', '<=', Carbon::now())
                ->orderBy('start_time', 'desc');
        } else {
            $query->orderBy('start_time', 'desc');
        }

        $userId = auth()->id();
        
        $shows = $query->get()->map(function ($show) use ($userId) {
            $show->bookings_count = $show->bookedUsers->count();
            $show->booked_by_user = $userId ? $show->bookedUsers->contains('id', $userId) : false;
            $show->bookings = $show->bookedUsers;
            return $show;
        });

        return response()->json($shows);
    }

}
