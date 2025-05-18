<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\Roles\UserRoles;
use App\Models\User;


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

        if (Auth::user()->role == UserRoles::ADMIN->value) {
            return view('admin.home');
        }
        if (Auth::user()->role == UserRoles::MEMBER->value) {
            return view('home');
        }
        
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
