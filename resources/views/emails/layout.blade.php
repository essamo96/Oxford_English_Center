<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <style>
        /* Email Reset & Base Styles */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; max-width: 100%; }
        table { border-collapse: collapse !important; }
        body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; background-color: #f1f5f9; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; }

        /* Typography */
        h1 { color: #002147; font-size: 24px; margin-top: 15px; font-weight: 800; letter-spacing: -0.5px; line-height: 1.2; }
        h2 { color: #002147; font-size: 20px; font-weight: 700; margin-bottom: 15px; }
        p { font-size: 16px; line-height: 1.6; color: #475569; margin-bottom: 20px; }

        /* Components */
        .content-card { background-color: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0; }
        .accent-bar { background: linear-gradient(90deg, #002147 0%, #003d7a 100%); height: 8px; }
        .btn { display: inline-block; background-color: #002147; color: #ffffff !important; padding: 14px 30px; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 16px; transition: all 0.3s ease; }
        
        /* Mobile Styles */
        @media screen and (max-width: 600px) {
            .container { width: 100% !important; padding: 10px !important; }
            .content-body { padding: 30px 20px !important; }
            h1 { font-size: 22px !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <!-- Header Section -->
        <tr>
            <td align="center" style="padding: 40px 0 30px 0;">
                <table border="0" cellpadding="0" cellspacing="0" width="600" class="container">
                    <tr>
                        <td align="center">
                            <img src="{{ url('assets/oxford/img/logo.png') }}" alt="Oxford English Centre" width="160" style="display: block; width: 160px; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));">
                            <div style="margin-top: 10px; font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: 2px; font-weight: 700;">
                                Oxford English Centre
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <!-- Main Content -->
        <tr>
            <td align="center" style="padding: 0 0 50px 0;">
                <table border="0" cellpadding="0" cellspacing="0" width="600" class="container content-card">
                    <tr>
                        <td class="accent-bar"></td>
                    </tr>
                    <tr>
                        <td class="content-body" style="padding: 50px;">
                            @yield('content')
                        </td>
                    </tr>
                    
                    <!-- Sign-off -->
                    <tr>
                        <td style="padding: 0 50px 50px 50px;">
                            <div style="border-top: 1px solid #f1f5f9; padding-top: 30px;">
                                <p style="font-size: 14px; color: #94a3b8; margin: 0;">Sincerely,</p>
                                <strong style="color: #002147; font-size: 16px;">The Oxford Administration Team</strong>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <!-- Footer Section -->
        <tr>
            <td align="center" style="padding-bottom: 60px;">
                <table border="0" cellpadding="0" cellspacing="0" width="600" class="container" style="text-align: center;">
                    <tr>
                        <td style="padding-bottom: 25px;">
                            <div style="display: inline-block;">
                                <a href="https://facebook.com" style="margin: 0 8px;"><img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" width="20" style="opacity: 0.5;"></a>
                                <a href="https://instagram.com" style="margin: 0 8px;"><img src="https://cdn-icons-png.flaticon.com/512/2111/2111463.png" width="20" style="opacity: 0.5;"></a>
                                <a href="https://twitter.com" style="margin: 0 8px;"><img src="https://cdn-icons-png.flaticon.com/512/733/733579.png" width="20" style="opacity: 0.5;"></a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 13px; color: #94a3b8; line-height: 1.8;">
                            &copy; {{ date('Y') }} <strong>Oxford English Centre</strong>. All rights reserved.<br>
                            {{ $mysettings->address ?? 'Palestine, Gaza' }}<br>
                            <a href="{{ url('/') }}" style="color: #002147; text-decoration: none; font-weight: 600;">Visit our Website</a> | <a href="mailto:{{ $mysettings->contact_email }}" style="color: #002147; text-decoration: none; font-weight: 600;">Support Center</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
