<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background-color: #1dd3b0;
            color: #ffffff;
            text-align: center;
            padding: 20px;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .email-body {
            padding: 20px;
            color: #333333;
            line-height: 1.6;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #1dd3b0;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-box strong {
            color: #1f2937;
        }
        .notice-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .email-body a {
            color: #1dd3b0;
            text-decoration: none;
            font-weight: bold;
        }
        .email-footer {
            background-color: #f9f9f9;
            text-align: center;
            padding: 10px;
            font-size: 12px;
            color: #777777;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #1dd3b0;
            color: #ffffff;
            border-radius: 4px;
            text-decoration: none;
            font-size: 16px;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #17b897;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>📄 Document Request Submitted</h1>
        </div>
        <div class="email-body">
            <p>Hello <strong>{{ $requestorName }}</strong>,</p>
            
            <p>Thank you for submitting a document request on behalf of <strong>{{ $studentName }}</strong>. This email confirms that we have received your request.</p>
            
            <div class="info-box">
                <strong>📋 Request Details:</strong><br>
                <strong>Student Name:</strong> {{ $studentName }}<br>
                <strong>Requested By:</strong> {{ $requestorName }}<br>
                <strong>Request Date:</strong> {{ now()->format('F d, Y') }}<br>
                <strong>Request Time:</strong> {{ now()->format('h:i A') }}
            </div>

            <div class="notice-box">
                <strong>⚠️ Important Notice:</strong><br>
                This is a <strong>guest request</strong> submitted by you on behalf of the student. 
                <strong>No online account has been created</strong> for this request.
                <br><br>
                This email is for <strong>notification purposes only</strong>. 
            </div>

            <p><strong>What happens next?</strong></p>
            <ul>
                <li>Your request is now being processed by our team</li>
                <li>The student/requestor will be notified when the document is ready</li>
                <li>Please bring a valid ID when claiming the document</li>
                <li>For status updates, please contact the school office directly</li>
            </ul>

            <p>If you have any questions or concerns, please contact our office.</p>
        </div>
        <div class="email-footer">
            <p>This is an automated notification. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} ODRMS. All rights reserved.</p>
        </div>
    </div>
</body>
</html>