<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Login Credentials</title>
</head>
<body style="font-family: 'Helvetica Neue', Arial, sans-serif; background-color: #f5f7fa; margin: 0; padding: 40px;">
    <div style="max-width: 600px; margin: auto; background: white; border-radius: 10px; padding: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
        
        <h2 style="text-align: center; color: #1e7c99;">Welcome to PEOConnect!</h2>

        <p style="font-size: 16px; color: #555; text-align: center;">
            You've been registered into the PEOConnect system. <br>Below are your login credentials:
        </p>

        <div style="margin: 30px 0; text-align: center; font-size: 16px; color: #444;">
            <p><strong>Email:</strong> {{ $email }}</p>
            <p><strong>Temporary Password:</strong> {{ $password }}</p>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/') }}" style="background: linear-gradient(to right, #f07ba3, #c4a8e3); color: white; padding: 14px 28px; border-radius: 50px; text-decoration: none; font-size: 16px; font-weight: bold;">
                Login to PEOConnect
            </a>
        </div>

        <p style="font-size: 14px; color: #777; text-align: center;">
            Please change your password after logging in for security reasons.
        </p>

        <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">

        <p style="font-size: 13px; color: #aaa; text-align: center;">
            Thank you and welcome aboard! 🚀<br>
            — The PEOConnect Team
        </p>
    </div>
</body>
</html>
