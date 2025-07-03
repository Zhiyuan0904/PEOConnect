<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use App\Models\User;

class RegistrationMailer
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function send()
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $this->user->getKey(),
                'hash' => sha1($this->user->getEmailForVerification()),
            ]
        );

        $apiKey = config('services.brevo.api_key');

        $response = Http::withHeaders([
            'api-key' => $apiKey,
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender' => [
                'email' => 'peoconnect0@gmail.com', // ✅ Must be a verified sender
                'name' => 'PEOConnect'
            ],
            'to' => [
                ['email' => $this->user->email, 'name' => $this->user->name]
            ],
            'subject' => '✨ Confirm Your Email for PEOConnect',
            'htmlContent' => $this->buildHtml($verificationUrl),
        ]);

        if (!$response->successful()) {
            logger()->error('Brevo Registration Mail Error', [
                'email' => $this->user->email,
                'response' => $response->body(),
            ]);
        }
    }

    private function buildHtml($url)
    {
        return '
        <html>
        <body style="font-family: Arial, sans-serif; background-color: #f5f7fa; padding: 40px;">
            <div style="max-width: 600px; margin: auto; background: white; border-radius: 10px; padding: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                <h2 style="text-align: center; color: #1e7c99;">Welcome to PEOConnect!</h2>
                <p style="font-size: 16px; color: #555; text-align: center;">
                    Thank you for registering. Please confirm your email by clicking the button below:
                </p>
                <div style="text-align: center; margin: 30px 0;">
                    <a href="' . $url . '" style="background: linear-gradient(to right, #f07ba3, #c4a8e3); color: white; padding: 12px 25px; border-radius: 30px; text-decoration: none; font-weight: bold;">
                        Confirm Email
                    </a>
                </div>
                <p style="font-size: 14px; color: #777; text-align: center;">
                    This link will expire in 60 minutes. If you didn\'t register, please ignore this email.🌱
                </p>
                <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">
                <p style="font-size: 13px; color: #aaa; text-align: center;">
                    — The PEOConnect Team
                </p>
            </div>
        </body>
        </html>';
    }
}
