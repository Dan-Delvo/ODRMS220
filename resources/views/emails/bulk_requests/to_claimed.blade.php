<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Documents Successfully Claimed' }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #1dd3b0 0%, #1f2937 100%);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 30px 25px;
        }
        .school-info {
            background-color: #f8f9fa;
            border-left: 4px solid #1dd3b0;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 4px;
        }
        .school-info h2 {
            margin: 0 0 10px;
            color: #1f2937;
            font-size: 18px;
        }
        .school-info p {
            margin: 5px 0;
            font-size: 14px;
            color: #555;
        }
        .message-box {
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .message-box p {
            margin: 10px 0;
            font-size: 15px;
            line-height: 1.8;
        }
        .status-badge {
            display: inline-block;
            background-color: #6f42c1;
            color: #ffffff;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin: 10px 0;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin: 20px 0;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            padding: 10px 15px 10px 0;
            font-weight: 600;
            color: #1f2937;
            width: 40%;
        }
        .info-value {
            display: table-cell;
            padding: 10px 0;
            color: #555;
        }
        .cta-button {
            display: inline-block;
            background-color: #1dd3b0;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 5px;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
        }
        .cta-button:hover {
            background-color: #1abc9c;
        }
        .footer {
            background-color: #1f2937;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            font-size: 13px;
        }
        .footer p {
            margin: 5px 0;
        }
        .footer a {
            color: #1dd3b0;
            text-decoration: none;
        }
        .divider {
            height: 1px;
            background-color: #dee2e6;
            margin: 20px 0;
        }
        .note {
            background-color: #e2e3e5;
            border-left: 4px solid #6c757d;
            padding: 12px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 14px;
            color: #383d41;
        }
        .success-box {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border: 2px solid #28a745;
            border-radius: 8px;
            padding: 25px;
            margin: 20px 0;
            text-align: center;
        }
        .success-box .icon {
            font-size: 60px;
            margin-bottom: 10px;
        }
        .success-box h3 {
            color: #155724;
            margin: 0 0 10px;
            font-size: 22px;
        }
        .success-box p {
            color: #155724;
            margin: 5px 0;
            font-size: 16px;
        }
        .feedback-section {
            background-color: #fff3cd;
            border: 2px dashed #ffc107;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .feedback-section h4 {
            color: #856404;
            margin: 0 0 10px;
        }
        .feedback-section p {
            color: #856404;
            font-size: 14px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>{{ $subject ?? '🎊 Documents Successfully Claimed' }}</h1>
            <p>Upper Bicutan National High School - Registrar Office</p>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Greeting -->
            <p style="font-size: 16px; color: #333; margin-bottom: 20px;">
                Dear <strong>{{ $name }}</strong>,
            </p>

            <!-- Success Box -->
            <div class="success-box">
                <div class="icon">✅</div>
                <h3>Successfully Claimed!</h3>
                <p>Your bulk document request has been successfully claimed and released.</p>
            </div>

            <!-- Status Badge -->
            <div style="text-align: center; margin: 20px 0;">
                <span class="status-badge">🎉 CLAIMED</span>
            </div>

            <!-- Message Box -->
            <div class="message-box">
                <p>
                    This is to confirm that your bulk document request has been successfully claimed from our Registrar Office.
                </p>
                <p>
                    Thank you for using our Document Request Management System. We appreciate your patience throughout the process.
                </p>
            </div>

            <!-- Request Details -->
            <div class="school-info">
                <h2>📍 Transaction Details</h2>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">School/Institution:</div>
                        <div class="info-value">{{ $name }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Status:</div>
                        <div class="info-value"><strong style="color: #6f42c1;">Claimed</strong></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Claimed Date:</div>
                        <div class="info-value">{{ now()->format('F d, Y') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Time:</div>
                        <div class="info-value">{{ now()->format('h:i A') }}</div>
                    </div>
                </div>
            </div>

            <div class="divider"></div>

            <!-- Important Reminders -->
            <h3 style="color: #1f2937; margin-bottom: 15px;">📝 Important Reminders</h3>
            <ul style="color: #555; line-height: 2; padding-left: 20px;">
                <li>Please verify all documents received for accuracy and completeness</li>
                <li>Keep your documents in a safe and secure place</li>
                <li>If you notice any discrepancies, please report immediately</li>
                <li>This email serves as your official transaction receipt</li>
                <li>Additional requests may be processed through the same system</li>
            </ul>

            <!-- Important Note -->
            <div class="note">
                <strong>📢 Note:</strong> Please keep this email for your records. This serves as proof of successful document release from our Registrar Office. If you have any concerns regarding the documents received, please contact us within 3 working days.
            </div>

            <!-- Feedback Section -->
            <div class="feedback-section">
                <h4>💬 We Value Your Feedback</h4>
                <p>
                    How was your experience with our Document Request Management System?<br>
                    Your feedback helps us improve our services.
                </p>
                <p style="font-size: 13px; margin-top: 15px;">
                    Thank you for choosing Upper Bicutan National High School!
                </p>
            </div>

            <div class="divider"></div>

            <!-- Contact Section -->
            <h3 style="color: #1f2937; margin-bottom: 10px;">📞 Need Assistance?</h3>
            <p style="color: #555; font-size: 14px;">
                If you have any questions or concerns regarding your claimed documents:
            </p>
            <p style="color: #555; font-size: 14px; margin: 5px 0;">
                🕐 Office Hours: Monday - Friday, 8:00 AM - 5:00 PM
            </p>

            <!-- Thank You Message -->
            <div style="text-align: center; margin-top: 30px; padding: 20px; background-color: #f8f9fa; border-radius: 8px;">
                <h3 style="color: #1f2937; margin: 0 0 10px;">🙏 Thank You!</h3>
                <p style="color: #555; margin: 0;">
                    We appreciate your trust in our services and look forward to serving you again in the future.
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
                <p style="color: white;"><strong>Upper Bicutan National High School</strong></p>
                <p style="color: white;">General Santos Avenue, Central Bicutan, Taguig City</p>
                <p style="margin-top: 15px; color: white;">
                    This is an automated email. Please do not reply to this message.
                </p>
            <p style="margin-top: 10px; font-size: 12px; opacity: 0.8;">
                © {{ date('Y') }} UBNHS Registrar Office in collaboration with PopBerry. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
