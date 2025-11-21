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
        ]);

        $user = auth()->user();

        // Path to public_html/passports
        $publicHtmlPath = base_path('../public_html/passports');

        // Ensure folder exists
        if (!file_exists($publicHtmlPath)) {
            mkdir($publicHtmlPath, 0777, true);
        }

        // Delete old passport if exists
        if ($user->passport) {
            // Remove leading slash if stored as "/passports/file.jpg"
            $oldFile = ltrim($user->passport, '/');
            $oldPath = base_path("../public_html/{$oldFile}");

            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        // Generate new file name
        $filename = time() . '_' . uniqid() . '.' . $request->passport->getClientOriginalExtension();

        // Move file to public_html/passports
        $request->passport->move($publicHtmlPath, $filename);

        // Save path in DB
        $user->passport = "passports/" . $filename;
        $user->save();

        return response()->json([
            'message' => 'Passport updated successfully',
            'passport_url' => asset("passports/" . $filename),
        ]);
    }

    public function getProfile(Request $request)
    {
        $user = auth()->user();

        return response()->json([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'passport' => $user->passport,
            'biography' => $user->biography,
            'skill_level' => $user->skill_level,
            'phone_number' => $user->phone_number,
            'country' => $user->country,
            'instagram' => $user->instagram,
            'youtube' => $user->youtube,
            'facebook' => $user->facebook,
            'tiktok' => $user->tiktok,
        ]);
    }

}
