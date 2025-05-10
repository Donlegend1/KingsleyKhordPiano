<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    function profile() {
        return view('memberpages.profile');
    }

    function update() {
        
        
    }

    function handleGetStarted() : Returntype {
        $user = User::find(Auth::user()->id);
        $user->metadata = ['hide_get_started' => true];
        $user->save();
        return redirect()->back();
    }
}
