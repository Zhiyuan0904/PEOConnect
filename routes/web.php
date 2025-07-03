<?php

use Illuminate\Support\Facades\Route;
use App\Mail\SurveyDistributionMail;
use Illuminate\Support\Facades\Mail;
use App\Services\BrevoMailService;

// // 🛠 TEST email first (put above!)
// Route::get('/test-survey-email', function () {
//     $sampleLink = 'http://127.0.0.1:8000/respond/surveys';
//     Mail::to('zhiyuann0904@gmail.com')->send(new SurveyDistributionMail($sampleLink));
//     return 'Test survey email sent successfully!';
// });

Route::get('/test-mail', function () {
    $sent = BrevoMailService::send(
        'zhiyuann0904@gmail.com', // ✅ change to your real email
        'Your Name',
        'Test Email from PEOConnect via Brevo API',
        '<p>This is a test email sent using <strong>Brevo Email API</strong>. 🎉</p>'
    );

    return $sent ? '✅ Email sent successfully!' : '❌ Failed to send email.';
});

// 🔥 Important: put the "catch all" route LAST!
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '^(?!api).*$');
