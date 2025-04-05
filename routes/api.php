<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\QualityController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\DeanController;
use App\Http\Controllers\StudentController;

    
// Authentication Module Routes
Route::post('/sign-up', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');


// Authenticated User Routes (Requires Login)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Test route to check authenticated user & roles
    Route::get('/me', function (Request $request) {
        return response()->json([
            'user' => $request->user(),
            'roles' => $request->user()?->getRoleNames(),
        ]);
    });

    // Role-based Routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'index']);
    });

    Route::middleware('role:quality team')->group(function () {
        Route::get('/quality/reports', [QualityController::class, 'index']);
    });

    Route::middleware('role:dean')->group(function () {
        Route::get('/dean/overview', [DeanController::class, 'index']);
    });

    Route::middleware('role:lecturer')->group(function () {
        Route::get('/lecturer/courses', [LecturerController::class, 'index']);
    });

    Route::middleware('role:alumni')->group(function () {
        Route::get('/alumni/events', [AlumniController::class, 'index']);
    });

    Route::middleware('role:student')->group(function () {
        Route::get('/student/dashboard', [StudentController::class, 'index']);
    });
});
