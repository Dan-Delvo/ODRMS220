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
            background-color: #f8fafc;
            border-left: 4px solid #1dd3b0;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-box p {
            margin: 8px 0;
        }
        .info-box strong {
            color: #1dd3b0;
        }
        .student-count {
            background-color: #1dd3b0;
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: bold;
            display: inline-block;
        }
        .email-footer {
            background-color: #f9f9f9;
            text-align: center;
            padding: 10px;
            font-size: 12px;
            color: #777777;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Request Submitted Successfully</h1>
        </div>
        <div class="email-body">
            <p>Hello <strong>{{ $name }}</strong>,</p>
            <p>Your bulk document request has been successfully submitted to our system.</p>

            <p>We have received your request and it has been added to our records. The registrar will review and process it accordingly.</p>
            
            <p>You will receive further notifications regarding the status of your request.</p>
            
            
            <p>If you have any questions, feel free to contact our support team.</p>
        </div>
        <div class="email-footer">
            <p>&copy; {{ date('Y') }} ODRMS. All rights reserved.</p>
        </div>
    </div>
</body>
</html>