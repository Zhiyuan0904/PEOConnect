<?php

use Illuminate\Support\Facades\Route;
use App\Mail\SurveyDistributionMail;
use Illuminate\Support\Facades\Mail;
use App\Services\BrevoMailService;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Artisan;

Route::get('/clear-cache', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return '✅ Cache cleared';
});

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $user = $request->user();

    if ($user->hasVerifiedEmail()) {
        return redirect('https://peoconnect.onrender.com/email/verified-success?status=already-verified');
    }

    $request->fulfill(); // Automatically calls markEmailAsVerified and dispatches Verified event

    return redirect('https://peoconnect.onrender.com/email/verified-success?status=success');
})->middleware(['signed'])->name('verification.verify');


// // 🛠 TEST email first (put above!)
// Route::get('/test-survey-email', function () {
//     $sampleLink = 'http://127.0.0.1:8000/respond/surveys';
//     Mail::to('zhiyuann0904@gmail.com')->send(new SurveyDistributionMail($sampleLink));
//     return 'Test survey email sent successfully!';
// });

Route::get('/test-brevo', function () {
    \App\Services\BrevoMailService::send(
        'your-email@gmail.com',
        'Test User',
        'Test Subject',
        '<p>This is a test email.</p>'
    );
    return 'Test email sent.';
});

// 🔥 Important: put the "catch all" route LAST!
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '^(?!api).*$');
