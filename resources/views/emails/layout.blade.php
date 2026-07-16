<!DOCTYPE html>
<html lang="en" dir="ltr">
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
        body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; background-color: #f8fafc; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; }

        /* Professional Typography */
        h1 { color: #002147; font-size: 27.2px; margin-top: 15px; font-weight: 700; letter-spacing: -0.5px; }
        p { font-size: 19.2px; line-height: 1.6; color: #475569; }

        /* Mobile Styles */
        @media screen and (max-width: 600px) {
            .content { width: 100% !important; border-radius: 0 !important; }
            .mobile-padding { padding-left: 20px !important; padding-right: 20px !important; }
            img { width: 100% !important; height: auto !important; }
        }
    </style>
</head>
<body style="margin: 0 !important; padding: 0 !important; background-color: #f8fafc;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <!-- Header -->
        <tr>
            <td align="center" style="padding: 40px 0 25px 0;">
                <table border="0" cellpadding="0" cellspacing="0" width="600" class="content text-center">
                    <tr>
                        <td align="center">
                            <img src="{{ $message->embed(public_path('OXFORD-LOGO.jpg')) }}" alt="Oxford English Centre" width="200" style="display: block; width: 200px; max-width: 250px;">
                            <h1 style="margin-top: 15px; font-size: 27.2px; color: #002147;">{{ $mysettings->name ?? 'Oxford English Centre' }}</h1>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <!-- Main Content Body -->
        <tr>
            <td align="center" style="padding: 0 0 50px 0;">
                <table border="0" cellpadding="0" cellspacing="0" width="600" class="content" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0;">
                    <!-- Top Accent Border -->
                    <tr>
                        <td style="background: linear-gradient(to right, #002147, #003d7a); height: 6px;"></td>
                    </tr>
                    
                    <tr>
                        <td class="mobile-padding" style="padding: 45px 50px;">
                            @yield('content')
                        </td>
                    </tr>
                    
                    <!-- Professional Sign-off -->
                    <tr>
                        <td class="mobile-padding" style="padding: 0 50px 45px 50px;">
                            <p style="font-size: 18.2px; color: #64748b; margin: 0; border-top: 1px solid #f1f5f9; padding-top: 30px;">
                                Best regards,<br>
                                <strong style="color: #002147; font-size: 19.2px;">{{ $mysettings->name ?? 'Oxford English Centre Team' }}</strong>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <!-- Footer Links & Ownership -->
        <tr>
            <td align="center" style="padding: 0 0 50px 0;">
                <table border="0" cellpadding="0" cellspacing="0" width="600" class="content" style="text-align: center;">
                    <tr>
                        <td style="padding-bottom: 25px;">
                            @if(isset($social) && count($social) > 0)
                                @foreach($social as $item)
                                    @php
                                        $icon = 'https://cdn-icons-png.flaticon.com/512/733/733547.png';
                                        if(stripos($item->name, 'twitter') !== false) $icon = 'https://cdn-icons-png.flaticon.com/512/733/733579.png';
                                        if(stripos($item->name, 'instagram') !== false) $icon = 'https://cdn-icons-png.flaticon.com/512/2111/2111463.png';
                                        if(stripos($item->name, 'youtube') !== false) $icon = 'https://cdn-icons-png.flaticon.com/512/1384/1384060.png';
                                        if(stripos($item->name, 'whatsapp') !== false) $icon = 'https://cdn-icons-png.flaticon.com/512/733/733585.png';
                                    @endphp
                                    <a href="{{ $item->url }}" style="display: inline-block; margin: 0 10px; text-decoration: none; opacity: 0.8; transition: opacity 0.2s;">
                                        <img src="{{ $icon }}" alt="{{ $item->name }}" width="22" style="width: 22px; height: 22px; filter: grayscale(100%);">
                                    </a>
                                @endforeach
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 16.2px; color: #94a3b8; line-height: 1.8;">
                            &copy; {{ date('Y') }} {{ $mysettings->name ?? 'Oxford English Centre' }}. All rights reserved.<br>
                            {{ $mysettings->address ?? '' }} @if(isset($mysettings->phone)) | {{ $mysettings->phone }} @endif
                            <br>
                            <span style="font-size: 15.2px;">You received this email because you are a registered student at our center.</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
