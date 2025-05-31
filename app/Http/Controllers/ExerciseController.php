<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;

class ExerciseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    

    function pianoExercise() {
        $all = Upload::where('category', 'piano exercise')->paginate(5);
        $independence = Upload::where('category', 'piano exercise')->where('level', 'Independence')->paginate(9);
        $coordination = Upload::where('category', 'piano exercise')->where('level', 'Coordination')->paginate(9);
        $flexibility = Upload::where('category', 'piano exercise')->where('level', 'Flexibility')->paginate(9);
        $strength = Upload::where('category', 'piano exercise')->where('level', 'Strength')->paginate(9);
        $dexterity = Upload::where('category', 'piano exercise')->where('level', 'Dextrity')->paginate(9);

        // dd($all);
        return view('memberpages.pianoexercise', compact('all', 'independence', 'coordination', 'flexibility', 'strength', 'dexterity'));
    }
}
