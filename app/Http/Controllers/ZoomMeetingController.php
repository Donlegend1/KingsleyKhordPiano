<?php

namespace App\Http\Controllers;

use App\Models\ZoomMeeting;
use App\Http\Requests\StoreZoomMeetingRequest;
use App\Http\Requests\UpdateZoomMeetingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Session;
use App\Services\ZoomService;

class ZoomMeetingController extends Controller
{
    public function redirectToZoom()
    {
        $authorizeUrl = "https://zoom.us/oauth/authorize";
        $clientId = 'zzgTBR6hT2qxXI5OfLAxHQ';
        $clientSecret = 'PLXKN43fN4uVjqwIYlPxSFzbNnbSCSrH';
        $redirectUri ='http://localhost:8000/zoom/callback';

       $data = Http::asForm(
           
        )->post('https://zoom.us/oauth/token?grant_type=account_credentials&account_id=zzgTBR6hT2qxXI5OfLAxHQ:PLXKN43fN4uVjqwIYlPxSFzbNnbSCSrH');

        return $data->json();
        // return redirect("https://zoom.us/oauth/token?grant_type=account_credentials&account_id=zzgTBR6hT2qxXI5OfLAxHQ");
    }

    public function handleZoomCallback(Request $request)
    {
        $code = $request->query('code');

        $response = Http::asForm()->withBasicAuth(
            env('ZOOM_CLIENT_ID'),
            env('ZOOM_CLIENT_SECRET')
        )->post('https://zoom.us/oauth/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => env('ZOOM_REDIRECT_URI'),
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to get access token', 'details' => $response->json()], 400);
        }

        $accessToken = $response->json()['access_token'];

        // Store in session or database
        Session::put('zoom_access_token', $accessToken);

        // Redirect back to frontend with token or use session-based flow
        return redirect('/zoom-meeting-booking?zoom_auth=success');
    }

   public function createZoomMeeting(Request $request, ZoomService $service)
    {

        $request->validate([
            'topic' => 'required|string',
            'date' => 'required|date',
            'time' => 'required',
            'duration' => 'required|integer',
        ]);

        $start_time = date('Y-m-d\TH:i:s\Z', strtotime($request->date . ' ' . $request->time));

        $meeting = $service->createMeeting([
            'topic' => $request->topic,
            'start_time' => $start_time,
            'duration' => $request->duration,
        ]);

        return response()->json($meeting);



        // $accessToken = 'hhudB71cS2-aYziCSvZWwg';
        // if (!$accessToken) {
        //     return response()->json(['error' => 'Not authorized with Zoom'], 401);
        // }

        // $request->validate([
        //     'topic' => 'required|string',
        //     'date' => 'required|date',
        //     'time' => 'required|string',
        //     'duration' => 'required|integer',
        // ]);

        // $startTime = date('Y-m-d\TH:i:s', strtotime($request->date . ' ' . $request->time));

        // $response = Http::withToken($accessToken)->post('https://api.zoom.us/v2/users/me/meetings', [
        //     'topic' => $request->topic,
        //     'type' => 2,
        //     'start_time' => $startTime,
        //     'duration' => $request->duration,
        //     'timezone' => 'UTC',
        //     'settings' => [
        //         'host_video' => true,
        //         'participant_video' => true,
        //         'waiting_room' => true,
        //     ],
        // ]);

        // if ($response->failed()) {
        //     return response()->json(['error' => 'Zoom API error', 'details' => $response->json()], 500);
        // }

        // return response()->json($response->json());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreZoomMeetingRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ZoomMeeting $zoomMeeting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ZoomMeeting $zoomMeeting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateZoomMeetingRequest $request, ZoomMeeting $zoomMeeting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ZoomMeeting $zoomMeeting)
    {
        //
    }
}
