<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Documents Ready for Release' }}</title>
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
            background-color: #28a745;
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
            background-color: #d1ecf1;
            border-left: 4px solid #0c5460;
            padding: 12px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 14px;
            color: #0c5460;
        }
        .highlight-box {
            background-color: #d4edda;
            border: 2px solid #28a745;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .highlight-box h3 {
            color: #155724;
            margin: 0 0 10px;
            font-size: 20px;
        }
        .highlight-box p {
            color: #155724;
            margin: 5px 0;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>{{ $subject ?? '✅ Documents Ready for Release' }}</h1>
            <p>Upper Bicutan National High School - Registrar Office</p>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Greeting -->
            <p style="font-size: 16px; color: #333; margin-bottom: 20px;">
                Dear <strong>{{ $name }}</strong>,
            </p>

            <!-- Status Badge -->
            <div style="text-align: center; margin: 20px 0;">
                <span class="status-badge">✅ READY FOR RELEASE</span>
            </div>

            <!-- Highlight Box -->
            <div class="highlight-box">
                <h3>🎉 Great News!</h3>
                <p>Your bulk document request is now complete and ready for claiming.</p>
            </div>

            <!-- Message Box -->
            <div class="message-box">
                <p>
                    We are pleased to inform you that all requested documents have been processed and are now available for release.
                </p>
                <p>
                    You may now proceed to our Registrar Office to claim your documents during office hours.
                </p>
            </div>

            <!-- Request Details -->
            <div class="school-info">
                <h2>📍 Request Details</h2>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">School/Institution:</div>
                        <div class="info-value">{{ $name }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Status:</div>
                        <div class="info-value"><strong style="color: #28a745;">For Release</strong></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Ready Date:</div>
                        <div class="info-value">{{ now()->format('F d, Y') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Time:</div>
                        <div class="info-value">{{ now()->format('h:i A') }}</div>
                    </div>
                </div>
            </div>

            <div class="divider"></div>

            <!-- Claiming Instructions -->
            <h3 style="color: #1f2937; margin-bottom: 15px;">📋 How to Claim Your Documents</h3>
            <ul style="color: #555; line-height: 2; padding-left: 20px;">
                <li>Visit our Registrar Office during office hours</li>
                <li>Present a valid authorization letter from your institution</li>
                <li>Bring a valid ID of the authorized representative</li>
                <li>Sign the release form upon claiming</li>
                <li>Please claim within 30 days from this notification</li>
            </ul>

            <!-- Important Note -->
            <div class="note">
                <strong>📢 Important:</strong> Please bring this email or your request reference number when claiming the documents. An authorized representative with a proper authorization letter is required for bulk document claims.
            </div>

            <!-- Office Location -->
            <div class="divider"></div>
            <h3 style="color: #1f2937; margin-bottom: 10px;">📍 Registrar Office Location</h3>
            <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <p style="margin: 5px 0; color: #555;"><strong>Address:</strong> General Santos Avenue, Central Bicutan, Taguig City</p>
                <p style="margin: 5px 0; color: #555;"><strong>Office Hours:</strong> Monday - Friday, 8:00 AM - 5:00 PM</p>
                <p style="margin: 5px 0; color: #555;"><strong>Lunch Break:</strong> 12:00 NN - 1:00 PM</p>
            </div>

            <!-- Contact Section -->
            <h3 style="color: #1f2937; margin-bottom: 10px;">📞 Need Assistance?</h3>
            <p style="color: #555; font-size: 14px;">
                For any questions or to schedule your claiming, please contact us:
            </p>
            <p style="color: #555; font-size: 14px; margin: 5px 0;">
                🕐 Office Hours: Monday - Friday, 8:00 AM - 5:00 PM
            </p>
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
