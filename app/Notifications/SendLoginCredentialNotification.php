<?php

namespace App\Services;

use App\Services\BrevoMailService;

class SendLoginCredentialMailer
{
    protected $email;
    protected $password;

    public function __construct($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function send($user)
    {
        $html = "
            <p>Hello {$user->name},</p>
            <p>Your login credentials for <strong>PEOConnect</strong> are as follows:</p>
            <ul>
                <li><strong>Email:</strong> {$this->email}</li>
                <li><strong>Password:</strong> {$this->password}</li>
            </ul>
            <p>Please log in and change your password after your first login.</p>
            <p>
                <a href=\"" . url('/') . "\" style=\"
                    background:#4CAF50;
                    color:white;
                    padding:10px 20px;
                    text-decoration:none;
                    border-radius:5px;
                \">Login to PEOConnect</a>
            </p>
            <p>Thank you,<br>PEOConnect Team</p>
        ";

        return BrevoMailService::send(
            $user->email,
            $user->name,
            '🔐 Your Login Credentials for PEOConnect',
            $html
        );
    }
}
