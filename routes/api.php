<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\DeanController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\QualityController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\SurveyResponseController;
use App\Http\Controllers\SurveyDistributionController;
use App\Http\Controllers\CurriculumContentController;
use App\Http\Controllers\PEOController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\ReportController;

// ====================
// Public Authentication Routes
// ====================
Route::post('/sign-up', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');

// ====================
// Protected Routes
// ====================
Route::middleware(['auth:sanctum'])->group(function () {

    // Auth-related
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [UserController::class, 'me']);

    // Profile
    Route::get('/profile', [UserController::class, 'getProfile']);
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::post('/profile/update', [UserController::class, 'updateProfile']);

    // Surveys
    Route::apiResource('surveys', SurveyController::class);
    Route::get('/available-surveys', [SurveyController::class, 'availableSurveys']);

    // Survey Responses
    Route::post('/survey-responses', [SurveyResponseController::class, 'store']); // Students
    Route::get('/survey-responses', [SurveyResponseController::class, 'index']);  // Admin
    Route::post('/surveys/import', [SurveyController::class, 'import']);


    // Survey Distribution
    Route::apiResource('survey-distributions', SurveyDistributionController::class);

    // Curriculum Content & PEO Management (Admin + Lecturer)
    Route::middleware(['role:admin|lecturer'])->group(function () {
        Route::apiResource('curriculum-content', CurriculumContentController::class);
        Route::apiResource('peos', PEOController::class);
        Route::post('/peos/bulk-upload', [PEOController::class, 'bulkUpload']);
    });

    Route::middleware(['role:admin|quality team'])->group(function () {
        Route::get('/track/progress', [ProgressController::class, 'index']);
        Route::get('/track/progress/{title}', [ProgressController::class, 'detail']);
        Route::get('/reports', [ReportController::class, 'exportFullReport']);
    });

    // ====================
    // Role-Based Dashboards
    // ====================
    Route::middleware('role:admin')->get('/admin/dashboard', [AdminController::class, 'index']);
    Route::middleware('role:quality team')->get('/quality/reports', [QualityController::class, 'index']);
    Route::middleware('role:dean')->get('/dean/overview', [DeanController::class, 'index']);
    Route::middleware('role:lecturer')->get('/lecturer/courses', [LecturerController::class, 'index']);
    Route::middleware('role:alumni')->get('/alumni/events', [AlumniController::class, 'index']);
    Route::middleware('role:student')->get('/student/dashboard', [StudentController::class, 'index']);
});
