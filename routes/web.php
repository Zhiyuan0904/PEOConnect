<?php

use Illuminate\Support\Facades\Route;
use App\Mail\SurveyDistributionMail;
use Illuminate\Support\Facades\Mail;
use App\Services\BrevoMailService;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use App\Models\User;

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::findOrFail($id);

    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return response()->json(['message' => 'Invalid or expired verification link.'], 403);
    }

    if ($user->hasVerifiedEmail()) {
        return redirect('https://peoconnect.onrender.com/email/verified-success?status=already-verified');
    }

    $user->markEmailAsVerified();
    event(new Verified($user));

    return redirect('https://peoconnect.onrender.com/email/verified-success?status=success');
})->middleware(['signed'])->name('verification.verify');

// // 🛠 TEST email first (put above!)
// Route::get('/test-survey-email', function () {
//     $sampleLink = 'http://127.0.0.1:8000/respond/surveys';
//     Mail::to('zhiyuann0904@gmail.com')->send(new SurveyDistributionMail($sampleLink));
//     return 'Test survey email sent successfully!';
// });


// 🔥 Important: put the "catch all" route LAST!
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '^(?!api).*$');
