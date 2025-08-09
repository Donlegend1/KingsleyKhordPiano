<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommunityIndexController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        return view('community.index');
    }

    public function space()
    {
       return view('community.space');
    }

    public function members()
    {
       return view('community.members');
    }

    public function single()
    {
       return view('community.single');
    }
}
