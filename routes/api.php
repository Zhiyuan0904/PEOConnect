<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Events\Verified;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\SurveyResponseController;
use App\Http\Controllers\SurveyDistributionController;
use App\Http\Controllers\CurriculumContentController;
use App\Http\Controllers\PEOController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ManageUserController;
use Illuminate\Http\Request;
use App\Models\User;

// ====================
// Public Authentication Routes
// ====================
Route::post('/sign-up', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');

// ====================
// ✅ Email Verification Routes
// ====================

// Handle email verification from link
// Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
//     $user = User::findOrFail($id);

//     if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
//         return response()->json(['message' => 'Invalid or expired verification link.'], 403);
//     }

//     if ($user->hasVerifiedEmail()) {
//         return redirect('https://peoconnect.onrender.com/email/verified-success?status=already-verified');
//     }

//     $user->markEmailAsVerified();
//     event(new Verified($user));

//     return redirect('https://peoconnect.onrender.com/email/verified-success?status=success');
// })->middleware(['signed'])->name('verification.verify');


// Resend verification link (requires Sanctum auth)
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return response()->json(['message' => 'Verification link sent.']);
})->middleware(['auth:sanctum'])->name('verification.send');

// Reminder endpoint for frontend
Route::get('/email/verify', function () {
    return response()->json(['message' => 'Please verify your email.']);
})->middleware('auth:sanctum')->name('verification.notice');

// ====================
// ✅ Protected Routes (Authenticated Only)
// ====================
Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/auth/check', function (Request $request) {
        return response()->noContent();
    });
    
    // Logout / Auth Info
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [UserController::class, 'me']);

    // Profile (All roles can manage profile)
    Route::get('/profile', [UserController::class, 'getProfile']);
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::post('/profile/update', [UserController::class, 'updateProfile']);

    Route::post('/reset-password-first-time', [AuthController::class, 'resetPasswordFirstTime']);

    // 🚀 Survey Management Module
    Route::middleware(['role:admin|quality team|dean|lecturer'])->group(function () {
        Route::get('/surveys/check-title', [SurveyController::class, 'checkTitle']);
        Route::apiResource('surveys', SurveyController::class);
        Route::post('/surveys/import', [SurveyController::class, 'import']);
        Route::get('/track/available-years', [ReportController::class, 'getAvailableYears']);
        Route::get('/track/yearly-comparison', [ReportController::class, 'getYearlyComparison']);
    });

    Route::get('/available-surveys', [SurveyController::class, 'availableSurveys']);

    Route::middleware(['role:student|alumni'])->group(function () {
        Route::post('/survey-responses', [SurveyResponseController::class, 'store']);
    });

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/survey-responses', [SurveyResponseController::class, 'index']);
    });

    // 🚀 Survey Distribution
    Route::middleware(['role:admin|quality team'])->group(function () {
        Route::apiResource('survey-distributions', SurveyDistributionController::class);
    });

    // 🚀 Curriculum + PEO Management
    Route::middleware(['role:admin|lecturer|quality team'])->group(function () {
        Route::apiResource('curriculum-content', CurriculumContentController::class);
        Route::apiResource('peos', PEOController::class);
        Route::post('/peos/bulk-upload', [PEOController::class, 'bulkUpload']);
    });

    // 🚀 Progress Tracking
    Route::middleware(['role:admin|quality team|dean'])->group(function () {
        Route::get('/track/progress', [ProgressController::class, 'index']);
        Route::get('/track/progress/{title}', [ProgressController::class, 'detail']);
        Route::get('/track/top-peos', [ProgressController::class, 'topPEOs']);
    });

    // 🚀 Reporting Module
    Route::middleware(['role:admin|quality team|dean'])->group(function () {
        Route::get('/reports', [ReportController::class, 'exportFullReport']);
    });

    // 🚀 Role Management (Admin only)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/manage/users', [ManageUserController::class, 'index']);
        Route::put('/manage/users/{id}/role', [ManageUserController::class, 'updateRole']);
        Route::post('/manage/users/create', [ManageUserController::class, 'create']);
        Route::post('/manage/users/send-login-email', [ManageUserController::class, 'sendLoginEmail']);
        Route::delete('/manage/users/{id}', [ManageUserController::class, 'destroy']); // ← DELETE endpoint
    });

    Route::middleware(['role:student'])->get('/student/progress', [SurveyController::class, 'studentProgress']);

});
