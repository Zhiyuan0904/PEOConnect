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
            'enroll_date' => $request->user()->enroll_date,
            'expected_graduate_date' => $request->user()->expected_graduate_date,
            'actual_graduate_date' => $request->user()->actual_graduate_date,
        ]);
    }

    // PUT /profile - Update current user info
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

    // GET /me - Retrieve authenticated user's information
    public function me(Request $request)
    {
        return response()->json([
            'id' => $request->user()->id,
            'name' => $request->user()->name,
            'email' => $request->user()->email,
            'role' => $request->user()->role,
            'enroll_date' => $request->user()->enroll_date,
            'expected_graduate_date' => $request->user()->expected_graduate_date,
            'actual_graduate_date' => $request->user()->actual_graduate_date,
        ]);
    }
}
