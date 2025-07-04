<?php

namespace App\Services;

use App\Models\User;
use App\Services\BrevoMailService;
use Illuminate\Support\Facades\URL;

class RegistrationMailer
{
    protected $user;
    protected $mailService;

    public function __construct(User $user, BrevoMailService $mailService)
    {
        $this->user = $user;
        $this->mailService = $mailService;
    }

    public function send()
    {
        // Generate the email verification URL
        $verificationUrl = null;

        app('router')->middleware('web')->group(function () use (&$verificationUrl) {
            // Force correct HTTPS scheme and root domain
            URL::forceScheme('https');
            URL::forceRootUrl(config('app.url'));

            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                [
                    'id' => $this->user->getKey(),
                    'hash' => sha1($this->user->getEmailForVerification()),
                ]
            );
        });

        // ✅ Send using 4 arguments to match your BrevoMailService::send()
        $this->mailService->send(
            $this->user->email,
            'Verify Your Email Address',
            $this->buildHtml($verificationUrl),
            null // or any optional parameter your service expects (like attachments or plainText version)
        );
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
