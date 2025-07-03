<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Services\SendLoginCredentialMailer;


class ManageUserController extends Controller
{
    /**
     * Get all users (Admin only)
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // ✅ More efficient: get only needed columns and eager load roles with constraints
        $users = User::select('id', 'name', 'email', 'role')
            ->with(['roles:id,name']) // only fetch role id + name
            ->get()
            ->map(function ($user) {
                $user->spatie_role = optional($user->roles->first())->name;
                unset($user->roles); // remove redundant relationship if not needed in frontend
                return $user;
            });

        return response()->json($users, 200);
    }


    /**
     * Update user role (Admin only)
     */
    public function updateRole(Request $request, $id)
    {
        $user = request()->user();

        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'role' => 'required|in:admin,lecturer,quality team,dean,student,alumni'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userToUpdate = User::findOrFail($id);
        $userToUpdate->role = $request->role;
        $userToUpdate->save();
        $userToUpdate->syncRoles($request->role);

        return response()->json(['message' => 'Role updated successfully'], 200);
    }

    /**
     * Create new user (Admin only)
     */
    public function create(Request $request)
    {
        $authUser = $request->user();
        if (!$authUser || !$authUser->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role'  => 'required|in:lecturer,quality team,dean'
        ]);

        $generatedPassword = Str::random(10);

        $user = User::create([
            'name'                => $validated['name'],
            'email'               => $validated['email'],
            'password'            => Hash::make($generatedPassword),
            'role'                => $validated['role'],
            'must_reset_password' => true,
            'email_verified_at'   => now(), // skip email verification
        ]);

        $user->assignRole($validated['role']);

        // ✅ Removed: $user->notify(...) — handled separately

        return response()->json([
            'message'           => 'User created successfully.',
            'email'             => $user->email,
            'generatedPassword' => $generatedPassword
        ]);
    }


    /**
     * Send login credentials to new user
     */
    public function sendLoginEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // ✅ Send via Brevo API
        (new SendLoginCredentialMailer($user->email, $request->password))->send($user);

        return response()->json(['message' => 'Login email sent successfully.']);
    }

        
    /**
     * Delete a user (Admin only)
     */
    public function destroy(Request $request, $id)
    {
        $authUser = $request->user();

        // Only admins can delete users
        if (!$authUser || !$authUser->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $userToDelete = User::find($id);
        if (!$userToDelete) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Optionally prevent deleting own account:
        if ($userToDelete->id === $authUser->id) {
            return response()->json(['message' => 'You cannot delete your own account.'], 400);
        }

        $userToDelete->delete();

        return response()->json(['message' => 'User deleted successfully.'], 200);
    }
}
