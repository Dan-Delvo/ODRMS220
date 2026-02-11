<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Temporary Password Notification</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 20px; color: #333;">
    <div style="max-width: 600px; margin: auto; background: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 20px;">

        <h2 style="color: #007bff; text-align: center;">🔐 Temporary Login Details</h2>

        <p>Hello <strong>{{ $name }}</strong>,</p>

        <p>We’ve created a temporary password for your account. Please use the details below to log in and make sure to change your password right away for security purposes:</p>

        <div style="background: #f1f3f5; padding: 15px; border-radius: 6px; font-size: 15px;">
            <p><strong>Email Address:</strong> {{ $email }}</p>
            <p><strong>Temporary Password:</strong> {{ $tempPassword }}</p>
        </div>

        <p style="margin-top: 20px;">For your security:</p>
        <ul>
            <li>Log in as soon as possible.</li>
            <li>Change your password immediately after logging in.</li>
            <li>Do not share your login details with anyone.</li>
        </ul>

        <!-- Login Button -->
        <div style="text-align: center; margin-top: 25px;">
            <a href="https://popberry.site/"
               style="background-color: #007bff; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-size: 16px;">
                🔑 Login to Your Account
            </a>
        </div>

        <p style="margin-top: 20px; text-align: center; font-size: 14px; color: #777;">
            If the button doesn’t work, copy and paste this link into your browser:<br>
            <a href="https://popberry.site/" style="color: #007bff;">https://popberry.site/</a>
        </p>

        <p>Thank you,<br>
        <strong>Your System Administrator</strong></p>
    </div>
</body>
</html>
