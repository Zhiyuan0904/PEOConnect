<?php

use Illuminate\Support\Facades\Route;
use App\Mail\SurveyDistributionMail;
use Illuminate\Support\Facades\Mail;
use App\Services\BrevoMailService;
use Illuminate\Support\Facades\Http;

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
