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
                border: 1px solid #f5c2c7;
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            }
            .email-header {
                background-color: #dc3545; /* red */
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
                color: #dc3545;
                text-decoration: none;
                font-weight: bold;
            }
            .reason-box {
                color: #842029;
                font-weight: bold;
                font-size: 16px;
                border: 1px solid #f5c2c7;
                background-color: #f8d7da;
                padding: 10px;
                border-radius: 4px;
                margin: 10px 0;
            }
            .view-status-btn {
                display: inline-block;
                padding: 10px 20px;
                background-color: #dc3545;
                color: #ffffff;
                border-radius: 4px;
                text-decoration: none;
                font-size: 16px;
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
            <h1>Document Declined</h1>
        </div>

        <div class="email-body">
            <p>Greetings,</p>

            <p>Hello <strong>{{ $name }}</strong>,</p>

            <p>Your requested document has been <strong style="color: #dc3545;">denied</strong>. Please see the reason below:</p>

            <p class="reason-box">
                Reason: {{ $reason }}
            </p>

            <p>If you believe this decision was made in error or you would like to discuss the reason further, please contact our office for clarification or assistance.  
            We’re here to help you with any questions regarding your request.</p>

            <p style="text-align: center; margin-top: 20px;">
                <a href="https://popberry.site/" class="view-status-btn">View Status</a>
            </p>

            <p>If you have any questions or need assistance, please contact us anytime.</p>
        </div>

        <div class="email-footer">
            <p>&copy; {{ date('Y') }} ODRMS. All rights reserved.</p>
        </div>
    </div>

    </body>
</html>
