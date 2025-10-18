<!-- Password OTP Email Template -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Change Verification</title>
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
            background: linear-gradient(135deg, #1dd3b0 0%, #16a085 100%);
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
        .otp-box {
            background-color: #f8f9fa;
            border: 2px dashed #1dd3b0;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }
        .otp-code {
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 8px;
            color: #1dd3b0;
            font-family: 'Courier New', monospace;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
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
            background-color: #1dd3b0;
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
            <h1>🔐 Password Change Verification</h1>
        </div>
        
        <div class="content">
            <p>Hello <strong>{{ $studentName }}</strong>,</p>
            
            <p>We received a request to change your password. To proceed, please use the verification code below:</p>
            
            <div class="otp-box">
                <p style="margin: 0 0 10px 0; color: #6c757d; font-size: 14px;">Your Verification Code</p>
                <div class="otp-code">{{ $otp }}</div>
                <p style="margin: 10px 0 0 0; color: #6c757d; font-size: 12px;">This code expires in {{ $expiresIn }}</p>
            </div>
            
            <div class="warning">
                <strong>⚠️ Security Notice:</strong>
                <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                    <li>Never share this code with anyone</li>
                    <li>Our team will never ask for this code</li>
                    <li>If you didn't request this change, please ignore this email</li>
                </ul>
            </div>
            
            <p>If you have any concerns about your account security, please contact our support team immediately.</p>
            
            <p>Best regards,<br>
            <strong>Popberry Team</strong></p>
        </div>
        
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} ODRMS Portal. All rights reserved.</p>
        </div>
    </div>
</body>
</html>


