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

Route::get('/test-mail', function () {
    $response = Http::withHeaders([
        'api-key' => env('BREVO_API_KEY'),
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ])->post('https://api.brevo.com/v3/smtp/email', [
        'sender' => [
            'name' => 'PEOConnect',
            'email' => '911215001@smtp-brevo.com',
        ],
        'to' => [[
            'email' => 'your_email@gmail.com',
            'name' => 'Your Name',
        ]],
        'subject' => 'PEOConnect API Email Test',
        'htmlContent' => '<p>This is a test email via <strong>Brevo API</strong>.</p>',
    ]);

    return response()->json([
        'success' => $response->successful(),
        'status' => $response->status(),
        'body' => $response->json(),
    ]);
});


// 🔥 Important: put the "catch all" route LAST!
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '^(?!api).*$');
