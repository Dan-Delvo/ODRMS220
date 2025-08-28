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
            background-color: #007bff;
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
            color: #007bff;
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
            <h1>Document Ready for Release</h1>
        </div>
        <div class="email-body">
            <p>Good news!</p>
            <p>Hello <strong>{{ $name }}</strong>,</p>
            <p>Your requested document is now ready for release. You may now proceed to the releasing area or follow the next instructions provided by the office.</p>
            <p>Be sure to bring any required identification or proof of request when claiming your document.</p>
            <p style="text-align: center; margin-top: 20px;">
                <a href="{{ url('https://odrms-ubnhs.bagsik-eis.site/') }}" style="
                    display: inline-block;
                    padding: 10px 20px;
                    background-color: #007bff;
                    color: #ffffff;
                    border-radius: 4px;
                    text-decoration: none;
                    font-size: 16px;
                ">View Status</a>
            </p>
            <p>If you have any questions or need assistance, please contact us anytime.</p>
        </div>
        <div class="email-footer">
            <p>&copy; {{ date('Y') }} ODRMS. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
