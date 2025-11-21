<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;
use App\Models\Liveshow;
use Carbon\Carbon;

class IndexController extends Controller
{
    public function index()
    {
        $extracourses = Upload::where('category', 'extra courses')->latest()->take(3)->get();
        $liveshow = Liveshow::where('start_time', '>=', Carbon::now())
        ->orderBy('start_time', 'asc')
        ->first();
        return view('welcome', compact('extracourses', 'liveshow'));
    }
}
