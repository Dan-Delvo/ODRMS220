<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
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
            background-color: #6777ef;
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
            color: #6777ef;
            text-decoration: none;
            font-weight: bold;
        }
        .email-footer {
            background-color: #f4f4f9;
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
            <h1>Document Ready Notification</h1>
        </div>
        <div class="email-body">
            <p>Your document is ready to be claimed!</p>
            <p>Hello <strong>{{ $name }}</strong>,</p>
            <p>Thank you for using our application! Your document is now ready for pickup. Please visit our office or use the link below to proceed.</p>
            <p>If you need further assistance, feel free to reach out to us.</p>
            <p style="text-align: center; margin-top: 20px;">
                <a href="{{ url('http://192.168.86.106:8000') }}" style="
                    display: inline-block;
                    padding: 10px 20px;
                    background-color: #6777ef;
                    color: #ffffff;
                    border-radius: 4px;
                    text-decoration: none;
                    font-size: 16px;
                ">Claim Your Document</a>
            </p>
        </div>
        <div class="email-footer">
            <p>&copy; {{ date('Y') }} ODRMS. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
