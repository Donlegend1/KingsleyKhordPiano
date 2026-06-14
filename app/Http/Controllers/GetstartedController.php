<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enums\GetStarted\GetStartedStatus;
use  App\Models\WebsiteVideo;
use App\Models\Course;
use App\Models\Upload;

class GetstartedController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index() 
    {
        $tour = WebsiteVideo::where('video_category', 'tour')->first()?->video_url;
        $session =WebsiteVideo::where('video_category', 'session')->first()?->video_url;
        $setUp = WebsiteVideo::where('video_category', 'setUp')->first()?->video_url;
        $exper = WebsiteVideo::where('video_category', 'exper')->first()?->video_url;

        $beginnerCourses = \App\Models\LearnSong::where('level', 'beginner')->where('status', 'active')->latest()->take(3)->get();
        $intermediateCourses = \App\Models\LearnSong::where('level', 'intermediate')->where('status', 'active')->latest()->take(3)->get();
        $advancedCourses = \App\Models\LearnSong::where('level', 'advanced')->where('status', 'active')->latest()->take(3)->get();

        return view('memberpages.getstarted', [
            'tour' => $tour,
            'session' => $session,
            'setUp' => $setUp,
            'exper' => $exper,
            'beginnerCourses' => $beginnerCourses,
            'intermediateCourses' => $intermediateCourses,
            'advancedCourses' => $advancedCourses,
        ]);
        
    }
    public function roadMap() 
    {

        return view('memberpages.roadmap');
    }

    public function updateGetStarted() 
    {
        $user = auth()->user();
        $user->get_started = GetStartedStatus::Old->value;
        $user->save();
        return redirect()->back();
    }
}
