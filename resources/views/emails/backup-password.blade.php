<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
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
        .password-box {
            background: #f8f9fa;
            border: 2px solid #1dd3b0;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .password {
            font-size: 28px;
            font-weight: bold;
            color: #1f2937;
            letter-spacing: 2px;
            font-family: 'Courier New', monospace;
            margin: 10px 0;
            word-break: break-all;
        }
        .info-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
        .warning-box {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
        }
        .detail-item {
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-label {
            font-weight: bold;
            color: #1f2937;
        }
        .detail-value {
            color: #6c757d;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
        .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">🔐</div>
            <h1>Database Backup Password</h1>
            <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;">ODRMS - UBNHS</p>
        </div>

        <div class="content">
            <p>Hello Administrator,</p>

            <p>A database backup has been created. Below is the secure password needed to restore this backup:</p>

            <div class="password-box">
                <p style="margin: 0; font-size: 14px; color: #6c757d;">Backup Password:</p>
                <div class="password">{{ $password }}</div>
                <p style="margin: 10px 0 0 0; font-size: 12px; color: #6c757d;">Copy this password and store it securely</p>
            </div>

            <div class="info-box">
                <strong>ℹ️ Important Information:</strong>
                <div class="detail-item">
                    <span class="detail-label">File Name:</span><br>
                    <span class="detail-value">{{ $fileName }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Performed By:</span><br>
                    <span class="detail-value">{{ $performedBy }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Timestamp:</span><br>
                    <span class="detail-value">{{ $timestamp }}</span>
                </div>
            </div>

            <div class="warning-box">
                <strong>⚠️ Security Notice:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>This password is unique to this backup file</li>
                    <li>Store this password in a secure location</li>
                    <li>You will need this password to restore the backup</li>
                    <li>Do not share this password with unauthorized persons</li>
                    <li>Keep the backup file and password separately</li>
                </ul>
            </div>

            <p style="margin-top: 30px; color: #6c757d; font-size: 14px;">
                This is an automated email. If you did not initiate this backup, please contact your system administrator immediately.
            </p>
        </div>

        <div class="footer">
            <p style="margin: 5px 0;">
                <strong>Online Document Request Management System</strong><br>
            </p>
            <p style="margin: 5px 0; font-size: 11px;">
                This email contains sensitive information. Please handle with care.
            </p>
        </div>
    </div>
</body>
</html>
