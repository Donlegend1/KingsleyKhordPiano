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
        $users = User::with('plan')->paginate(10);
        return response()->json($users);
    }


    public function editUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'payment_status' => 'nullable|in:successful,pending',
            'premium' => 'nullable|boolean',
        ]);

        $user->update($validated);

        return response()->json($user);
    }

    public function destroy(Request $request, User $user)
    {
        $user->delete();

        return response()->json(['message' => "Record deleted"]);
    }

}
