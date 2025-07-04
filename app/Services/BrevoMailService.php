<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrevoMailService
{
    public static function send($toEmail, $toName, $subject, $htmlContent)
    {
        $response = Http::withHeaders([
            'api-key' => env('BREVO_API_KEY'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender' => [
                'name' => 'PEOConnect',
                'email' => '911215e01@smtp-brevo.com',
            ],
            'to' => [[
                'email' => $toEmail,
                'name' => $toName,
            ]],
            'subject' => $subject,
            'htmlContent' => $htmlContent,
        ]);

        if ($response->successful()) {
            Log::info("✅ Email sent successfully to: $toEmail");
            return true;
        } else {
            Log::error('❌ Email failed', [
                'to' => $toEmail,
                'response' => $response->body(),
            ]);
            return false;
        }
    }
}
