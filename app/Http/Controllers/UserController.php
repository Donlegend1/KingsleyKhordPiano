<?php

namespace App\Http\Controllers;
use  App\Models\User;
use App\Models\Community;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getSingleUser(Request $request, Community $community)
    {
        $userDetails = $community->with('user')->first();

        return  response()->json($userDetails, 200);
    }
}
