<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SurveyDistributionMailer
{
    protected $email;
    protected $name;
    protected $surveyLinks;

    public function __construct($email, $name, $surveyLinks)
    {
        $this->email = $email;
        $this->name = $name;
        $this->surveyLinks = $surveyLinks;
    }

    public function send()
    {
        $apiKey = config('services.brevo.api_key');

        $response = Http::withHeaders([
            'api-key' => $apiKey,
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender' => [
                'email' => 'peoconnect0@gmail.com',
                'name' => 'PEOConnect',
            ],
            'to' => [
                ['email' => $this->email, 'name' => $this->name]
            ],
            'subject' => '📊 You Have New PEO Surveys to Complete',
            'htmlContent' => $this->buildHtmlContent(),
        ]);

        if (!$response->successful()) {
            logger()->error('Brevo Survey Mail Error', [
                'email' => $this->email,
                'response' => $response->body(),
            ]);
        }
    }

    private function buildHtmlContent()
    {
        $linksHtml = '';
        foreach ($this->surveyLinks as $title => $url) {
            $linksHtml .= '<li style="margin: 8px 0;"><a href="' . $url . '" style="color: #1e7c99; font-weight: bold;">' . $title . '</a></li>';
        }

        return '
        <html>
        <body style="font-family: Arial, sans-serif; background-color: #f5f7fa; padding: 40px;">
            <div style="max-width: 600px; margin: auto; background: white; border-radius: 10px; padding: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                <h2 style="text-align: center; color: #1e7c99;">New PEO Surveys Available</h2>
                <p style="font-size: 16px; color: #555; text-align: center;">
                    You have surveys pending for you in PEOConnect. Please fill them out at your convenience:
                </p>
                <ul style="font-size: 16px; color: #444; padding-left: 20px;">
                    ' . $linksHtml . '
                </ul>
                <p style="font-size: 14px; color: #777; text-align: center;">
                    Thank you for your valuable feedback!
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
