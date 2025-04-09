<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    // GET /profile - Retrieve current user info
    public function getProfile(Request $request)
    {
        return response()->json([
            'name' => $request->user()->name,
            'email' => $request->user()->email,
        ]);
    }

    // PUT /profile - Update current user info
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:6',
        ]);

        $user = $request->user();
        $user->name = $request->name;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return response()->json(['message' => 'Profile updated successfully']);
    }

    // GET /me - Retrieve authenticated user's information
    public function me(Request $request)
    {
        return response()->json(['user' => $request->user()]);
    }
}
