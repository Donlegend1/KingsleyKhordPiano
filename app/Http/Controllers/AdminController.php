<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Enums\Roles\UserRoles;

class AdminController extends Controller
{
    //   public function __construct()
    // {
    //     $this->middleware('auth');
    // }
    /**
     * Show the courses page.
     *
     * @return \Illuminate\View\View
     */
    function users() {
        $users = User::paginate(10);
        return view('admin.users', compact('users'));
    }

    function usersList() {
        $users = User:: with('plan')->where('role', UserRoles::MEMBER->value)->paginate(10);
        return response()->json($users);
}
}
