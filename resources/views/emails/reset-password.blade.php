<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Your Password | PEOConnect</title>
</head>
<body style="font-family: 'Segoe UI', sans-serif; background-color: #f4f4f4; margin: 0; padding: 40px;">

  <table width="100%" cellspacing="0" cellpadding="0" style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
    <tr>
      <td style="padding: 30px; text-align: center; background: linear-gradient(to right, #8475d2, #a7c8f8); color: white; border-top-left-radius: 10px; border-top-right-radius: 10px;">
        <h1 style="margin: 0;">PEOConnect</h1>
        <p style="margin: 5px 0 0;">Reset Your Password</p>
      </td>
    </tr>

    <tr>
      <td style="padding: 30px;">
        <p>Hello {{ $user->name ?? 'there' }},</p>

        <p>We received a request to reset your password for your PEOConnect account. Click the button below to reset it:</p>

        <div style="text-align: center; margin: 30px 0;">
          <a href="{{ $url }}" style="background: linear-gradient(to right, #f07ba3, #c4a8e3); color: white; padding: 15px 30px; text-decoration: none; border-radius: 50px; font-weight: bold;">
            Reset Password
          </a>
        </div>

        <p>This link will expire in 60 minutes.</p>
        <p>If you didn’t request a password reset, please ignore this email.</p>

        <p style="margin-top: 40px;">– The PEOConnect Team</p>
      </td>
    </tr>

    <tr>
      <td style="padding: 20px; text-align: center; font-size: 12px; color: #999;">
        © {{ date('Y') }} PEOConnect. All rights reserved.
      </td>
    </tr>
  </table>

</body>
</html>
