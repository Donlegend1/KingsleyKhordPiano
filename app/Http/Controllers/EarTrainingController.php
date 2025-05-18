<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EarTrainingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Show the ear training page.
     *
     * @return \Illuminate\View\View
     */
    function earTraining() {
        return view('memberpages.eartraining');
    }
}
