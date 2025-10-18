<!-- Password Changed Confirmation Email Template -->
<!-- Create file: resources/views/emails/password-changed.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Changed Successfully</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 40px 30px;
        }
        .success-icon {
            text-align: center;
            font-size: 64px;
            margin: 20px 0;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #10b981;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .warning {
            background-color: #fee;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #10b981;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✓ Password Changed Successfully</h1>
        </div>
        
        <div class="content">
            <div class="success-icon">✓</div>
            
            <p>Hello <strong>{{ $studentName }}</strong>,</p>
            
            <p>This is to confirm that your password was successfully changed.</p>
            
            <div class="info-box">
                <strong>📅 Change Details:</strong>
                <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                    <li><strong>Date & Time:</strong> {{ $changedAt }}</li>
                    <li><strong>Account:</strong> {{ $studentName }}</li>
                </ul>
            </div>
            
            <div class="warning">
                <strong>🚨 Didn't make this change?</strong>
                <p style="margin: 10px 0 0 0;">If you did not authorize this password change, your account may be compromised. Please contact our support team immediately and consider the following:</p>
                <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                    <li>Contact support immediately</li>
                    <li>Review your recent account activity</li>
                    <li>Enable two-factor authentication if available</li>
                </ul>
            </div>
            
            <p>For your security, we recommend:</p>
            <ul>
                <li>Using a strong, unique password</li>
                <li>Not sharing your password with anyone</li>
                <li>Changing your password regularly</li>
                <li>Using different passwords for different accounts</li>
            </ul>
            
            <p>Thank you for helping us keep your account secure!</p>
            
            <p>Best regards,<br>
            <strong>Student Portal Team</strong></p>
        </div>
        
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>If you have any questions, please contact our support team.</p>
            <p>&copy; {{ date('Y') }} Student Portal. All rights reserved.</p>
        </div>
    </div>
</body>
</html>