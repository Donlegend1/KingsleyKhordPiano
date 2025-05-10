<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LiveSessionController extends Controller
{
    function liveSession() {
        return view('memberpages.livesession');
    }
}
