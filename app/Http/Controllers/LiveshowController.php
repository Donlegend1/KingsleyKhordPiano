<?php

namespace App\Http\Controllers;

use App\Models\Liveshow;
use App\Models\LiveShowNotification;
use App\Http\Requests\StoreLiveshowRequest;
use App\Http\Requests\UpdateLiveshowRequest;
 use Carbon\Carbon;
 use Illuminate\Http\Request;
 use App\Notifications\NewLiveShowNotification;
use Illuminate\Support\Facades\DB;

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
        try {
            // return $request->all();
            $liveshow = LiveShow::create($request->validated());

            $subscribers = LiveShowNotification::with('user')->get();

            if ($subscribers->isNotEmpty()) {
                foreach ($subscribers as $subscriber) {
                    if ($subscriber->user) {
                         new NewLiveShowNotification($liveshow, $subscriber->user);
                    }
                }
            }

            return redirect()->back()->with('success', 'Live show created.');
        } catch (\Exception $e) {
            logger()->error('LiveShowNotification failed: ' . $e->getMessage());

            return redirect()->back()->with(
                'error',
                'Failed to create live show.'
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        return view('memberpages.premium.booking');
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
