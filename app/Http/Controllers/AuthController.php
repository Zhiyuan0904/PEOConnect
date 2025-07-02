<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use App\Notifications\CustomRegistrationMail;
use App\Notifications\CustomResetPasswordNotification;

class AuthController extends Controller
{
    // ✅ User Registration
    public function register(Request $request)
    {
        // Check if email already exists
        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                'message' => 'Email already exists. Please use another email.'
            ], 409);
        }

        // Validate fields with custom messages
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:student,alumni',
        ], [
            'name.required'     => 'Please enter your full name.',
            'email.required'    => 'Email is required to register.',
            'email.email'       => 'Please enter a valid email address.',
            'password.required' => 'Password is required.',
            'password.min'      => 'Password must have at least 6 characters.',
            'role.required'     => 'Please select a role.',
            'role.in'           => 'Role must be student or alumni only.',
        ]);

        // If validation fails, return first error message in 'message', and all in 'errors'
        if ($validator->fails()) {
            $fieldErrors = [];
            foreach ($validator->errors()->messages() as $field => $messages) {
                $fieldErrors[$field] = $messages[0]; // only first message per field
            }

            return response()->json([
                'message' => reset($fieldErrors), // show first error message overall
                'errors'  => $fieldErrors
            ], 422);
        }

        $email = $request->email;
        $role  = $request->role;

        // Role-based email domain check
        $valid = match ($role) {
            'student' => str_ends_with($email, '@graduate.utm.my'),
            'alumni'  => !str_ends_with($email, '@graduate.utm.my'),
            default   => false,
        };

        if (!$valid) {
            return response()->json([
                'message' => 'Invalid email domain for selected role.'
            ], 422);
        }

        // Create and notify user
        $user = User::create([
            'name'     => $request->name,
            'email'    => $email,
            'password' => Hash::make($request->password),
            'role'     => $role,
            'must_reset_password' => false
        ]);

        $user->assignRole($role);
        $user->notify(new CustomRegistrationMail());

        return response()->json([
            'message' => 'User registered successfully. Please check your email to verify your account.'
        ], 201);
    }

    // ✅ User Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        $user = Auth::user();

        // ---- 1. First-time login (admin-created)
        if ($user->must_reset_password) {
            $user->must_reset_password = false;
            $user->email_verified_at = now();
            $user->save();

            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'message' => 'First-time login — reset password required.',
                'token'   => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'must_reset_password' => true, // front-end uses this
                ],
            ], 200);
        }

        // ---- 2. Self-registered users must have verified email
        if (!$user->hasVerifiedEmail()) {
            Auth::logout();
            return response()->json(['message' => 'Please verify your email.'], 403);
        }

        // ---- 3. Normal login flow
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message' => 'Login successful.',
            'token'   => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'enroll_date' => $user->enroll_date,
                'expected_graduate_date' => $user->expected_graduate_date,
                'actual_graduate_date' => $user->actual_graduate_date,
                'must_reset_password' => false,
            ],
        ], 200);
    }

    // ✅ Forgot Password 
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $token = Str::random(60);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        $resetUrl = url("/reset-password?token=$token&email=" . urlencode($user->email));
        $user->notify(new CustomResetPasswordNotification($resetUrl));

        return response()->json(['message' => 'Reset link sent to email'], 200);
    }

    // ✅ Reset Password (normal flow)
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $resetRecord = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord) {
            return response()->json(['message' => 'Invalid reset request'], 400);
        }

        if (now()->subMinutes(60)->gt($resetRecord->created_at)) {
            DB::table('password_resets')->where('email', $request->email)->delete();
            return response()->json(['message' => 'Token has expired'], 400);
        }

        if (!Hash::check($request->token, $resetRecord->token)) {
            return response()->json(['message' => 'Invalid token'], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'New password cannot be the same as the previous password'], 400);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'must_reset_password' => false
        ]);

        DB::table('password_resets')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Password reset successfully']);
    }

    // ✅ First-time Password Reset (admin-created users)
    public function resetPasswordFirstTime(Request $request)
    {
        $request->validate(['password' => 'required|string|min:6']);

        $user = $request->user();

        $user->update([
            'password' => Hash::make($request->password),
            'must_reset_password' => false,
        ]);

        return response()->json(['message' => 'Password updated successfully.']);
    }

}
