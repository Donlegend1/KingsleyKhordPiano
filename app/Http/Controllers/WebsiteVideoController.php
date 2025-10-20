<?php

namespace App\Http\Controllers;
use  App\Models\WebsiteVideo;
use  App\Http\Requests\WebsiteVideoRequest;

class WebsiteVideoController extends Controller
{
    public function index()
    {
        return view('admin.website.videos', [
            'websiteVideos' => WebsiteVideo::all()
        ]);
    }

    public function saveVideo(WebsiteVideoRequest $request, WebsiteVideo $video = null)
    {
        if ($video && $video->exists) {
            // if ($video->path && file_exists(public_path($video->path))) {
            //     unlink(public_path($video->path));
            // }
            $video->delete();
        }

        WebsiteVideo::create($request->validated());

        return redirect()->back()->with('success', 'Video saved successfully.');
    }

    public function destroy(WebsiteVideo $video)
    {
       $video->delete();

       return redirect()->back()->with('success', 'Video deleted successfully.');
    }
}
