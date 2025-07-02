<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Your Password | PEOConnect</title>
</head>
<body style="font-family: 'Helvetica Neue', Arial, sans-serif; background-color: #f5f7fa; margin: 0; padding: 40px;">
  <div style="max-width: 600px; margin: auto; background: white; border-radius: 10px; padding: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
    
    <h2 style="text-align: center; color: #1e7c99;">🔒 Reset Your Password</h2>

    <p style="font-size: 16px; color: #555; text-align: center;">
      Hello {{ $user->name ?? 'there' }}, <br>
      We received a request to reset your PEOConnect account password. Click below to continue:
    </p>

    <div style="margin: 30px 0; text-align: center;">
      <a href="{{ $url }}" style="display: inline-block; background: linear-gradient(to right, #f07ba3, #c4a8e3); color: white; padding: 14px 28px; border-radius: 50px; text-decoration: none; font-size: 16px; font-weight: bold;">
        🔐 Reset Password
      </a>
    </div>

    <p style="font-size: 14px; color: #777; text-align: center;">
      This link will expire in 60 minutes. <br>
      If you did not request a reset, no action is needed.
    </p>

    <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">

    <p style="font-size: 13px; color: #aaa; text-align: center;">
      Thank you for using PEOConnect!<br>
      — The PEOConnect Team
    </p>
  </div>
</body>
</html>
