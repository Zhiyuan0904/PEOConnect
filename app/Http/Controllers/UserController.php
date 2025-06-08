<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    // GET /profile - Retrieve current user info
    public function getProfile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'enroll_date' => $user->enroll_date,
            'expected_graduate_date' => $user->expected_graduate_date,
            'actual_graduate_date' => $user->actual_graduate_date,
        ]);
    }

    // POST /profile/update - Handle profile update without profile_picture
    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'enroll_date' => 'nullable|date',
            'expected_graduate_date' => 'nullable|date',
            'actual_graduate_date' => 'nullable|date',
            'password' => 'nullable|string|min:6',
        ]);

        $user = $request->user();

        $user->name = $data['name'];
        $user->enroll_date = $data['enroll_date'] ?? $user->enroll_date;
        $user->expected_graduate_date = $data['expected_graduate_date'] ?? $user->expected_graduate_date;
        $user->actual_graduate_date = $data['actual_graduate_date'] ?? $user->actual_graduate_date;

        if (!empty($data['password'])) {
            $user->password = bcrypt($data['password']);
        }

        $user->save();

        return response()->json(['message' => 'Profile updated successfully']);
    }

    // GET /me - Retrieve authenticated user's info (used by authStore.fetchUser())
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'enroll_date' => $user->enroll_date,
            'expected_graduate_date' => $user->expected_graduate_date,
            'actual_graduate_date' => $user->actual_graduate_date,
        ]);
    }
}
