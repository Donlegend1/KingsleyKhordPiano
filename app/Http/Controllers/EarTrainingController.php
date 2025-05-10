<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EarTrainingController extends Controller
{
    function earTraining() {
        return view('memberpages.eartraining');
    }
}
