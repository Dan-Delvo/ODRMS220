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
            background-color: #28a745;
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
        .email-body a {
            color: #28a745;
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
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Request Approved</h1>
        </div>
        <div class="email-body">
            <p>Your request has been approved!</p>
            <p>Hello <strong>{{ $name }}</strong>,</p>
            <p>We’re pleased to inform you that your request has been successfully approved. Please take the necessary steps to complete the process.</p>
            <p>If you need further assistance, feel free to reach out to us.</p>
            <p style="text-align: center; margin-top: 20px;">
                <a href="{{ url('http://127.0.0.1:8000/') }}" style="
                    display: inline-block;
                    padding: 10px 20px;
                    background-color: #28a745;
                    color: #ffffff;
                    border-radius: 4px;
                    text-decoration: none;
                    font-size: 16px;
                ">Notification Action</a>
            </p>
        </div>
        <div class="email-footer">
            <p>&copy; {{ date('Y') }} ODRMS. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
