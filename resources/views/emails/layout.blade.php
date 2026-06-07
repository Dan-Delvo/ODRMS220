<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $emailTitle ?? config('app.name', 'ODRMS') }}</title>
</head>
<body style="margin:0; padding:0; background:#f1f5f9; color:#172033; font-family:Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f1f5f9;">
        <tr>
            <td align="center" style="padding:32px 16px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                    style="max-width:620px; background:#ffffff; border:1px solid #dbe4ee; border-radius:16px; overflow:hidden;">
                    <tr>
                        <td style="height:6px; background:{{ $accent ?? '#0f766e' }};"></td>
                    </tr>
                    <tr>
                        <td style="padding:32px 36px 20px;">
                            <p style="margin:0 0 10px; color:{{ $accent ?? '#0f766e' }}; font-size:12px; font-weight:700; letter-spacing:1.4px; text-transform:uppercase;">
                                {{ $eyebrow ?? 'ODRMS Notification' }}
                            </p>
                            <h1 style="margin:0; color:#0f172a; font-size:28px; line-height:1.2; font-weight:700;">
                                {{ $emailTitle ?? 'Notification' }}
                            </h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 36px 32px; color:#334155; font-size:15px; line-height:1.7;">
                            @yield('content')
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 36px; background:#f8fafc; border-top:1px solid #e2e8f0; color:#64748b; font-size:12px; line-height:1.6;">
                            This is an automated notification from the Online Document Request Management System.
                            Please contact the registrar's office if you need assistance.
                            <br>&copy; {{ date('Y') }} ODRMS. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
