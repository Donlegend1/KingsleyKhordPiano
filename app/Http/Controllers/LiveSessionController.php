<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LiveSessionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Show the live session page.
     *
     * @return \Illuminate\View\View
     */
    function liveSession() {
        return view('memberpages.livesession');
    }
}
