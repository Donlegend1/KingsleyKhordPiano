<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enums\GetStarted\GetStartedStatus;

class GetstartedController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    function index()  {
        return view('memberpages.getstarted');
        
    }
    function roadMap() {
        return view('memberpages.roadmap');
    }

    function updateGetStarted() {
        $user = auth()->user();
        $user->get_started = GetStartedStatus::Old->value;
        $user->save();
        return redirect()->back();
    }
}
