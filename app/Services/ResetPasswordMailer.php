<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\User;

class ResetPasswordMailer
{
    protected $email;
    protected $resetUrl;

    public function __construct($email, $resetUrl)
    {
        $this->email = $email;
        $this->resetUrl = $resetUrl;
    }

    public function send(User $user)
    {
        $apiKey = config('services.brevo.api_key');
        $sender = [
            'email' => 'peoconnect0@gmail.com', // your verified Brevo sender
            'name'  => 'PEOConnect',
        ];

        $response = Http::withHeaders([
            'api-key' => $apiKey,
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender' => $sender,
            'to' => [[ 'email' => $this->email, 'name' => $user->name ]],
            'subject' => '🔒 Reset Your PEOConnect Password',
            'htmlContent' => $this->buildHtmlContent(),
        ]);

        if (!$response->successful()) {
            logger()->error('Brevo Password Reset Email Error:', ['response' => $response->body()]);
        }
    }

    private function buildHtmlContent()
    {
        return '
        <html>
        <body style="font-family: Arial, sans-serif; background-color: #f5f7fa; padding: 40px;">
            <div style="max-width: 600px; margin: auto; background: white; border-radius: 10px; padding: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                <h2 style="text-align: center; color: #1e7c99;">Reset Your Password</h2>
                <p style="font-size: 16px; color: #555; text-align: center;">
                    We received a request to reset your password. Click the button below to proceed.
                </p>
                <div style="text-align: center; margin: 30px 0;">
                    <a href="' . $this->resetUrl . '" style="background: #f07ba3; color: white; padding: 14px 28px; border-radius: 50px; text-decoration: none; font-weight: bold;">
                        Reset Password
                    </a>
                </div>
                <p style="font-size: 14px; color: #777; text-align: center;">
                    If you didn\'t request this, you can safely ignore this email.
                </p>
                <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">
                <p style="font-size: 13px; color: #aaa; text-align: center;">
                    Thank you,<br>The PEOConnect Team
                </p>
            </div>
        </body>
        </html>';
    }
}
