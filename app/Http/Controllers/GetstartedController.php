<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enums\GetStarted\GetStartedStatus;
use  App\Models\WebsiteVideo;
use App\Models\Course;

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

        $beginnerCourses = Course::where('category', 'Beginer')->latest()->take(3)->get();
        // dd($beginnerCourses);
        $intermediateCourses = Course::where('category', 'Intermidiate')->latest()->take(3)->get();
        $advancedCourses = Course::where('category', 'Advanced')->latest()->take(3)->get();

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
