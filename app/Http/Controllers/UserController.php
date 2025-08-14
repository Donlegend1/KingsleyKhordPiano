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

    public function updateUserCommunity(Request $request, Community $community)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'web_url'    => 'nullable|url',
            'short_bio'        => 'nullable|string',
            'instagram'  => 'nullable|url',
            'facebook'   => 'nullable|url',
            'youtube'    => 'nullable|url',
            'x'          => 'nullable|url',
            'status'     => 'nullable|string|in:active,inactive',
            'nickname'   => 'nullable|string|max:255',
        ]);

        $community->user->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
        ]);

        $community->update([
            'bio'     => $request->short_bio,
            'user_name'   => $request->user_name,
            'social'  => [
                'instagram' => $request->instagram,
                'facebook'  => $request->facebook,
                'youtube'   => $request->youtube,
                'x'         => $request->x,
                'website' => $request->web_url,
            ]
        ]);

        $community->load('user');

        return response()->json([
            'message' => 'User and community updated successfully.',
            'data'    => $community
        ], 200);
    }

    public function updateUserPassport(Request $request)
    {
        $request->validate([
            'passport' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // 'user_id' =>  'required|integer|exist,users:id'
        ]);

        $user = auth()->user();

        if ($user->passport && file_exists(public_path('passports/' . $user->passport))) {
            unlink(public_path('passports/' . $user->passport));
        }

        $filename = time() . '_' . uniqid() . '.' . $request->passport->getClientOriginalExtension();

        $request->passport->move(public_path('passports'), $filename);

        $user->passport = "/passports/".$filename;
        $user->save();

        return response()->json([
            'message' => 'Passport updated successfully',
            'passport_url' => asset('passports/' . $filename),
        ]);
    }

}
