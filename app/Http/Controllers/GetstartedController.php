<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GetstartedController extends Controller
{
    function index()  {
        return view('memberpages.getstarted');
        
    }
    function roadMap() {
        return view('memberpages.roadmap');
    }
}
