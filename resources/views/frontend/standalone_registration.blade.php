<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oxford Centre | Standalone Registration</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS for grid -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&family=Inter:wght@400;500;600;700&display=swap');

        :root {
            --primary: #0a2540; /* Deep Oxford Blue */
            --primary-light: #1a4570;
            --accent: #d4af37; /* Gold */
            --accent-hover: #f2cb4e;
            --success: #10b981;
            --surface: #ffffff;
            --surface-glass: rgba(255, 255, 255, 0.95);
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --bg-gradient: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            --oxford-blue: #36336D;
            --date-red: #8A1538;
        }

        body {
            background: var(--bg-gradient);
            background-attachment: fixed;
            font-family: 'Tajawal', 'Inter', sans-serif;
            color: var(--text-main);
            min-height: 100vh;
            padding: 2rem 0;
            position: relative;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 350px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            z-index: -1;
            clip-path: polygon(0 0, 100% 0, 100% 100%, 0 85%);
        }

        .registration-container {
            background: var(--surface-glass);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08), 0 1px 3px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 900px;
            margin: auto;
            border: 1px solid rgba(255,255,255,0.6);
            overflow: visible;
            transition: transform 0.3s ease;
            position: relative;
        }

        .header {
            text-align: center;
            padding: 3rem 2rem 2rem;
            position: relative;
        }

        .header img {
            max-width: 130px;
            margin-bottom: 1rem;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
            transition: transform 0.3s ease;
        }
        
        .header img:hover {
            transform: scale(1.05);
        }

        .header h1 {
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 0.5rem;
            font-size: 2.2rem;
            letter-spacing: -0.5px;
        }
        
        .header p {
            color: var(--text-muted);
            font-size: 1.1rem;
        }

        .form-section {
            padding: 2.5rem;
        }

        .section-title {
            color: var(--primary);
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--date-red);
            display: inline-block;
        }

        .form-label {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 0.6rem;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-label span.ar {
            font-family: 'Tajawal', sans-serif;
            color: var(--text-muted);
            font-weight: 400;
            font-size: 0.9rem;
        }

        .form-control, .form-select {
            border: 2px solid var(--border);
            border-radius: 12px;
            padding: 0.8rem 1.2rem;
            font-size: 1rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: rgba(255,255,255,0.8);
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--date-red);
            box-shadow: 0 0 0 4px rgba(138, 21, 56, 0.15);
            background-color: #fff;
            transform: translateY(-2px);
        }

        .form-control::placeholder {
            color: #94a3b8;
        }

        .invoice-box {
            background: linear-gradient(to bottom right, #ffffff, #f8fafc);
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            padding: 2rem;
            margin-top: 2rem;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .invoice-box::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 4px; height: 100%;
            background: var(--date-red);
            border-radius: 4px 0 0 4px;
        }

        .invoice-box:hover {
            border-color: var(--date-red);
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            transform: translateY(-3px);
        }

        .invoice-box h4 {
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .payment-method-card {
            transition: all 0.2s ease;
            cursor: default;
        }
        
        .payment-method-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08) !important;
            border-color: var(--date-red) !important;
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--oxford-blue) 0%, #292654 100%);
            color: #fff;
            border: none;
            padding: 1.2rem;
            width: 100%;
            border-radius: 14px;
            font-size: 1.25rem;
            font-weight: 700;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 8px 16px rgba(54, 51, 109, 0.25);
            margin-top: 2rem;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #444085 0%, var(--oxford-blue) 100%);
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(54, 51, 109, 0.35);
            color: #fff;
        }
        
        .btn-submit:active {
            transform: translateY(1px);
        }

        .whatsapp-note {
            background: rgba(138, 21, 56, 0.1);
            border-left: 4px solid var(--date-red);
            color: var(--date-red);
            padding: 1rem 1.5rem;
            border-radius: 0 12px 12px 0;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .whatsapp-note svg {
            color: var(--date-red);
            flex-shrink: 0;
            margin-top: 2px;
        }

        .loader {
            display: none;
            border: 3px solid rgba(255,255,255,0.3);
            border-top: 3px solid #fff;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        .ar {
            font-family: 'Tajawal', sans-serif;
            direction: rtl;
        }

        .invalid-feedback {
            font-size: 0.85rem;
            color: #e53e3e;
            display: none;
        }

        .is-invalid ~ .invalid-feedback {
            display: block;
        }



        .dynamic-section {
            display: none;
            animation: fadeIn 0.5s ease;
            background: #f8fafc;
            padding: 1.5rem;
            border-radius: 12px;
            border: 1px dashed var(--accent);
            margin-top: 1rem;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .registration-container { padding: 1.5rem; margin: 1rem; }
            body::before { height: 250px; }
        }

        /* --- SPLASH SCREEN --- */
        .splash-screen {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0a2540, #1a4570, #2b6cb0, #d4af37, #0a2540, #1a4570);
            background-size: 400% 400%;
            animation: gradientMoveNWSE 5s ease infinite;
            transition: opacity 1.2s cubic-bezier(0.4, 0, 0.2, 1), visibility 1.2s, transform 1.2s;
        }
        @keyframes gradientMoveNWSE {
            0% { background-position: 0% 0%; }
            50% { background-position: 100% 100%; }
            100% { background-position: 0% 0%; }
        }
        .splash-screen.loaded {
            opacity: 0;
            visibility: hidden;
            transform: scale(1.05); /* Elegant subtle zoom out */
        }
        .splash-logo-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 180px;
            height: 180px;
        }
        .splash-logo-wrapper::before, .splash-logo-wrapper::after {
            content: '';
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.4);
            animation: splash-ripple 2.5s infinite cubic-bezier(0.4, 0, 0.2, 1);
        }
        .splash-logo-wrapper::after {
            animation-delay: 1.25s;
        }
        @keyframes splash-ripple {
            0% { width: 180px; height: 180px; opacity: 1; border-width: 3px; }
            100% { width: 500px; height: 500px; opacity: 0; border-width: 1px; }
        }
        .splash-logo-container {
            position: relative;
            z-index: 2;
        }
        .splash-logo {
            width: 180px;
            animation: pulse-splash 2s cubic-bezier(0.4, 0, 0.2, 1) infinite;
            filter: drop-shadow(0 8px 20px rgba(0,0,0,0.5));
        }
        @keyframes pulse-splash {
            0% { transform: scale(0.95); opacity: 0.9; }
            50% { transform: scale(1.05); opacity: 1; }
            100% { transform: scale(0.95); opacity: 0.9; }
        }

        /* --- FLOATING WHATSAPP --- */
        .floating-wa {
            position: fixed;
            left: 30px;
            bottom: 30px;
            width: 65px;
            height: 65px;
            background-color: #25d366;
            color: #fff;
            border-radius: 50px;
            text-align: center;
            font-size: 38px;
            box-shadow: 2px 2px 15px rgba(0,0,0,0.2);
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .floating-wa:hover {
            transform: scale(1.15) rotate(5deg);
            color: #fff;
            box-shadow: 2px 5px 20px rgba(37, 211, 102, 0.4);
        }
        .floating-wa::after {
            content: '';
            position: absolute;
            top: -4px; left: -4px; right: -4px; bottom: -4px;
            background: transparent;
            border: 2px solid #25d366;
            border-radius: 50%;
            animation: wa-pulse 2s infinite;
            pointer-events: none;
        }
        @keyframes wa-pulse {
            0% { transform: scale(1); opacity: 0.8; }
            100% { transform: scale(1.3); opacity: 0; }
        }

        /* --- SOCIAL MEDIA FOOTER --- */
        .registration-footer {
            text-align: center;
            padding: 2.5rem 1.5rem;
            background: rgba(255, 255, 255, 0.6);
            border-top: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 0 0 24px 24px;
        }
        .social-icons {
            display: flex;
            justify-content: center;
            gap: 18px;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        .social-icon {
            width: 50px;
            height: 50px;
            border-radius: 14px; /* Square with rounded corners */
            background: var(--surface);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.06);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            border: 1px solid rgba(0,0,0,0.05);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        .social-icon::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: -1;
            transition: all 0.4s ease;
            opacity: 0;
        }

        /* Specific Brand Colors */
        .social-icon i[class*="facebook"] { color: #1877F2; }
        .social-icon:hover i[class*="facebook"] { color: #fff; }
        .social-icon:has(i[class*="facebook"])::before { background: #1877F2; }

        .social-icon i[class*="instagram"] { 
            background: -webkit-linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .social-icon:hover i[class*="instagram"] { background: transparent; -webkit-text-fill-color: #fff; }
        .social-icon:has(i[class*="instagram"])::before { background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); }

        .social-icon i[class*="twitter"], .social-icon i[class*="x-twitter"] { color: #000; }
        .social-icon:hover i[class*="twitter"], .social-icon:hover i[class*="x-twitter"] { color: #fff; }
        .social-icon:has(i[class*="twitter"])::before, .social-icon:has(i[class*="x-twitter"])::before { background: #000; }

        .social-icon i[class*="youtube"] { color: #FF0000; }
        .social-icon:hover i[class*="youtube"] { color: #fff; }
        .social-icon:has(i[class*="youtube"])::before { background: #FF0000; }

        .social-icon i[class*="linkedin"] { color: #0A66C2; }
        .social-icon:hover i[class*="linkedin"] { color: #fff; }
        .social-icon:has(i[class*="linkedin"])::before { background: #0A66C2; }

        .social-icon i[class*="tiktok"] { color: #000; }
        .social-icon:hover i[class*="tiktok"] { color: #fff; }
        .social-icon:has(i[class*="tiktok"])::before { background: #000; }
        
        .social-icon i[class*="snapchat"] { color: #FFFC00; text-shadow: 0px 0px 1px #000; }
        .social-icon:hover i[class*="snapchat"] { color: #000; text-shadow: none; }
        .social-icon:has(i[class*="snapchat"])::before { background: #FFFC00; }

        .social-icon i[class*="whatsapp"] { color: #25D366; }
        .social-icon:hover i[class*="whatsapp"] { color: #fff; }
        .social-icon:has(i[class*="whatsapp"])::before { background: #25D366; }

        .social-icon:hover {
            transform: translateY(-6px) scale(1.08);
            box-shadow: 0 12px 25px rgba(0,0,0,0.15);
            border-color: transparent;
        }
        .social-icon:hover::before {
            opacity: 1;
        }

        /* Fallback */
        @supports not selector(:has(*)) {
            .social-icon:hover {
                background: var(--primary);
                color: #fff !important;
            }
            .social-icon:hover i {
                color: #fff !important;
                -webkit-text-fill-color: #fff !important;
            }
        }
        .ote-logo-container {
            margin-top: 0.5rem;
        }
        .ote-logo-container img {
            max-width: 220px;
            height: auto;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.05));
            transition: transform 0.3s ease;
        }
        .ote-logo-container img:hover {
            transform: scale(1.03);
        }

        /* --- HANGING 3D MEDALS (INSIDE FORM) --- */
        .hanging-medals-wrapper {
            position: absolute;
            top: 0;
            left: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
            transform-origin: top center;
            animation: swing-medal 4s cubic-bezier(0.4, 0, 0.2, 1) infinite alternate;
            z-index: 10;
        }
        .hanging-string {
            width: 14px;
            height: 45px;
            background-image: url("data:image/svg+xml,%3Csvg width='14' height='20' xmlns='http://www.w3.org/2000/svg'%3E%3Crect x='4' y='0' width='6' height='14' rx='3' fill='none' stroke='%23d4af37' stroke-width='2'/%3E%3Crect x='2' y='12' width='10' height='6' rx='3' fill='none' stroke='%23f2cb4e' stroke-width='2'/%3E%3Cpath d='M4 12v2a3 3 0 006 0v-2' fill='none' stroke='%23d4af37' stroke-width='2'/%3E%3C/svg%3E");
            background-repeat: repeat-y;
            filter: drop-shadow(1px 2px 2px rgba(0,0,0,0.4));
        }
        .medal-scene {
            width: 75px;
            height: 75px;
            perspective: 600px;
        }
        .medal-3d {
            width: 100%;
            height: 100%;
            position: relative;
            transform-style: preserve-3d;
            animation: flip-medal 8s infinite cubic-bezier(0.4, 0, 0.2, 1);
        }
        .medal-face {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
            background: #fff;
            padding: 6px;
            border-radius: 50%;
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
            border: 3px solid #d4af37;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .medal-face img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .medal-back {
            transform: rotateY(180deg);
        }
        @keyframes swing-medal {
            0% { transform: rotate(8deg); }
            100% { transform: rotate(-8deg); }
        }
        @keyframes flip-medal {
            0%, 40% { transform: rotateY(0deg); }
            50%, 90% { transform: rotateY(180deg); }
            100% { transform: rotateY(360deg); }
        }
        
        /* --- PROMOTIONAL RIBBON (Top Right) --- */
        .promo-ribbon {
            position: absolute;
            top: 30px;
            right: -10px; /* Stick out slightly */
            background: linear-gradient(135deg, #f6d365 0%, #ffb347 100%); /* Golden gradient */
            padding: 8px 24px;
            border-radius: 8px 0 0 8px;
            box-shadow: -3px 5px 15px rgba(255, 179, 71, 0.4), inset 0 1px 2px rgba(255,255,255,0.6);
            z-index: 15;
            animation: float-ribbon 3s ease-in-out infinite alternate;
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-right: none;
            direction: rtl;
        }
        .promo-ribbon::after {
            content: '';
            position: absolute;
            top: 100%;
            right: 0;
            width: 0;
            height: 0;
            border-style: solid;
            border-width: 10px 10px 0 0;
            border-color: #cc8e39 transparent transparent transparent; /* Darker fold color */
        }
        .promo-text {
            color: #fff;
            font-family: 'Cairo', sans-serif;
            font-weight: 800;
            font-size: 1.15rem;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        @keyframes float-ribbon {
            0% { transform: translateY(0); }
            100% { transform: translateY(-5px); }
        }
    </style>
</head>
<body>

    <!-- Splash Screen -->
    <div class="splash-screen" id="splashScreen">
        <div class="splash-logo-wrapper">
            <div class="splash-logo-container">
                <img src="{{ url('assets/oxford/img/logo.png') }}" alt="Oxford Center Logo" class="splash-logo" onerror="this.src='https://via.placeholder.com/120x120.png?text=Logo'">
            </div>
        </div>
    </div>

    <div class="registration-container">
        <!-- Hanging 3D Medals 
        <div class="hanging-medals-wrapper">
            <div class="hanging-string"></div>
            <div class="medal-scene">
                <div class="medal-3d">
                    <div class="medal-face">
                        <img src="{{ url('assets/oxford/img/logo.png') }}" alt="Oxford Logo">
                    </div>
                    <div class="medal-face medal-back">
                        <img src="{{ asset('assets/images/LOGO_2026_bg_remove.png') }}" alt="2026 Logo">
                    </div>
                </div>
            </div>
        </div>
        -->

        <!-- Promotional Ribbon (Top Right) -->
        <style>
            .promo-ribbon-red::after { border-color: #610B24 transparent transparent transparent !important; }
        </style>
        <div class="promo-ribbon promo-ribbon-red" style="background: var(--date-red); box-shadow: -3px 5px 15px rgba(138, 21, 56, 0.4); border-color: var(--date-red); direction: ltr;">
            <div class="promo-text" style="font-size: 1.05rem;">
                Welcome To Oxford Family
            </div>
        </div>

        <div class="header">
            <img src="{{ url('assets/oxford/img/logo.png') }}" alt="Oxford Centre Logo" onerror="this.src='https://via.placeholder.com/120x120.png?text=Logo'">
            <h1>Student Registration Form</h1>
            <p style="font-family: 'Cairo', sans-serif;" dir="rtl">نموذج تسجيل الطلاب - مركز أكسفورد</p>
        </div>

        <div class="form-section">
            <form id="registrationForm">
                <!-- Section A: Student Details -->
                <h3 class="section-title">Section A: Student Details <span style="font-family: 'Cairo', sans-serif; font-size:1rem; color:var(--text-muted)">(بيانات الطالب)</span></h3>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name (English) <span class="ar">(الاسم الرباعي بالإنجليزية)</span> *</label>
                        <input type="text" class="form-control" name="full_name_en" required placeholder="e.g. John Doe">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" dir="rtl" style="display:block; text-align:right;">الاسم الرباعي بالعربية <span class="ar" style="font-family: 'Inter', sans-serif;">(Full Name Arabic)</span> *</label>
                        <input type="text" class="form-control text-end" name="full_name_ar" required placeholder="مثال: أحمد محمد" dir="rtl">
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Phone Number <span class="ar">(رقم الجوال)</span> *</label>
                        <input type="tel" class="form-control" name="phone" required placeholder="05XXXXXXXX">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Address <span class="ar">(البريد الإلكتروني)</span> *</label>
                        <input type="email" class="form-control" name="email" required placeholder="email@example.com">
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Date of Birth <span class="ar">(تاريخ الميلاد)</span> *</label>
                        <input type="date" class="form-control" name="dob" id="dobInput" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Gender <span class="ar">(الجنس)</span> *</label>
                        <select class="form-select" name="gender" required>
                            <option value="">-- Select Gender --</option>
                            <option value="Male">Male (ذكر)</option>
                            <option value="Female">Female (أنثى)</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Address <span class="ar">(العنوان)</span> *</label>
                        <input type="text" class="form-control" name="address" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Branch <span class="ar">(الفرع)</span> *</label>
                        <select class="form-select" name="branch" required>
                            <option value="">-- Select Branch --</option>
                            <option value="Headquarters - Deir al-Balah">المقر الرئيسي - دير البلح (Headquarters - Deir al-Balah)</option>
                            <option value="Gaza branch - Aljundiu">فرع غزة - الجندي (Gaza branch - Aljundiu)</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Major/Profession <span class="ar">(التخصص/المهنة)</span> *</label>
                        <input type="text" class="form-control" name="major_profession" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-12 mt-4">
                        <label class="form-label">Does the applicant have any health problems? <span class="ar">(هل يعاني المتقدم من مشاكل صحية؟)</span> *</label>
                        <div class="d-flex gap-4 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="health_issues" id="health_no" value="0" checked>
                                <label class="form-check-label" for="health_no">
                                    No (لا يوجد)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="health_issues" id="health_yes" value="1">
                                <label class="form-check-label" for="health_yes">
                                    Yes (نعم يوجد)
                                </label>
                            </div>
                        </div>

                    <div class="col-md-12 mt-3" id="healthIssuesDetailsContainer" style="display: none;">
                        <label class="form-label text-danger">Please describe the health problem <span class="ar">(يرجى وصف المشكلة الصحية بالتفصيل)</span> *</label>
                        <textarea class="form-control" name="health_issues_details" rows="3" placeholder="تفاصيل المشكلة الصحية"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <!-- Dynamic Parent Section -->
                <div id="parentSection" class="dynamic-section">
                    <h4 class="mb-3" style="color:var(--primary-blue); font-size:1.1rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        Parent Information <span class="ar" style="font-weight:normal">(بيانات ولي الأمر - العمر 15 أو أقل)</span>
                    </h4>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Parent Name <span class="ar">(اسم ولي الأمر)</span> *</label>
                            <input type="text" class="form-control" name="parent_name" id="parent_name">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Parent Phone <span class="ar">(رقم جوال ولي الأمر)</span> *</label>
                            <input type="tel" class="form-control" name="parent_phone" id="parent_phone">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Parent Email <span class="ar">(بريد ولي الأمر)</span></label>
                            <input type="email" class="form-control" name="parent_email">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>

                <hr class="my-5" style="border-color: var(--border-color)">

                <!-- Section B: Placement Test -->
                <h3 class="section-title">Section B: Placement Test <span class="ar" style="font-size:1rem; color:var(--text-muted)">(اختبار تحديد المستوى)</span></h3>
                <div class="mb-4">
                    <label class="form-label">Do you want to take a placement test? <span class="ar">(هل ترغب بالتقدم لاختبار تحديد المستوى؟)</span></label>
                    <div class="form-check form-switch mt-2" style="transform: scale(1.2); transform-origin: left center;">
                        <input class="form-check-input" type="checkbox" id="placementTestToggle" name="placement_test" value="1">
                        <label class="form-check-label ms-2" for="placementTestToggle">Yes, I want to take the test (نعم، أرغب)</label>
                    </div>
                </div>

                <div id="placementDateSection" class="dynamic-section">
                    <div class="col-md-6">
                        <label class="form-label">Preferred Date & Time <span class="ar">(الموعد المفضل للاختبار)</span> *</label>
                        <input type="datetime-local" class="form-control" name="placement_test_date" id="placement_test_date">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <hr class="my-5" style="border-color: var(--border-color)">

                <!-- Section C: Program Selection & Invoicing -->
                <h3 class="section-title">Section C: Program Selection <span class="ar" style="font-size:1rem; color:var(--text-muted)">(اختيار البرنامج والفاتورة)</span></h3>
                
                <div class="mb-4">
                    <label class="form-label">Program Type <span class="ar">(نوع البرنامج)</span> *</label>
                    <div class="d-flex gap-4 mt-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="program_type" id="type_kids" value="kids" required>
                            <label class="form-check-label" for="type_kids">
                                Kids (برنامج الصغار)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="program_type" id="type_adults" value="adults" required>
                            <label class="form-check-label" for="type_adults">
                                Adults (برنامج الكبار)
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Select Program <span class="ar">(اختر البرنامج)</span> *</label>
                    <select class="form-select" name="program_id" id="programSelect" required>
                        <option value="">-- Choose a Program --</option>
                        @foreach($programs as $program)
                            @php
                                $progFees = isset($feesByProgram[$program->id]) ? $feesByProgram[$program->id] : collect([]);
                                $baseFee = $progFees->sum('amount');
                                $minDue = 0;
                                if (!empty($program->min_payment_fixed)) {
                                    $minDue = $program->min_payment_fixed;
                                } elseif (!empty($program->min_payment_percent) && $baseFee > 0) {
                                    $minDue = $baseFee * ($program->min_payment_percent / 100);
                                } else {
                                    $minDue = $baseFee;
                                }
                                $feesJson = json_encode($progFees->map(function($f) {
                                    return ['name' => $f->type_name, 'amount' => $f->amount];
                                })->toArray());
                            @endphp
                            <option value="{{ $program->id }}" 
                                data-title="{{ $program->title }}" 
                                data-total-fee="{{ $baseFee }}"
                                data-min-due="{{ $minDue }}"
                                data-fees-details="{{ $feesJson }}">
                                {{ $program->title }}
                            </option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback"></div>
                </div>

                <div id="invoiceBox" class="invoice-box" style="display:none;">
                    <h4>Invoice Summary <span class="ar" style="font-weight:normal; font-size:1.1rem">(ملخص الفاتورة)</span></h4>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <p class="mb-1 text-muted">Selected Program (البرنامج المختار):</p>
                            <p class="fw-bold fs-5" id="invProgramName">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="mb-1 text-muted">Total Fee (إجمالي الرسوم):</p>
                            <p class="fw-bold fs-5 text-dark">₪ <span id="invTotalFee">0.00</span></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="mb-1 text-muted">Minimum Amount Due (الحد الأدنى للدفع):</p>
                            <p class="fw-bold fs-5 text-primary">₪ <span id="invMinAmount">0.00</span></p>
                        </div>
                        <div class="col-md-12">
                            <p class="mb-2 fw-bold" style="color: var(--date-red) !important;">Fee Details (تفاصيل الرسوم):</p>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm mb-2 text-center align-middle" dir="ltr">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="color: var(--primary);">Fee Type (نوع الرسم)</th>
                                            <th style="color: var(--primary);">Amount (المبلغ ₪)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="invFeeDetailsList">
                                        <!-- Dynamically populated list -->
                                    </tbody>
                                </table>
                            </div>
                            <p class="fs-6 mb-0 fw-bold" style="color: var(--date-red) !important;"><small>* Includes program registration and base tuition. (تشمل جميع المتطلبات الأساسية للبرنامج)</small></p>
                        </div>
                    </div>
                    
                    <hr class="my-4" style="border-color: rgba(0,0,0,0.1);">
                    
                    <h5 class="mb-3">Available Payment Methods <span class="ar" style="font-weight:normal; font-size:1rem">(طرق الدفع المتاحة)</span></h5>
                    <div class="row payment-methods-container">
                        @foreach($paymentMethods as $pm)
                            <div class="col-md-6 mb-3">
                                <div class="payment-method-card p-3 border rounded shadow-sm d-flex align-items-center" style="background: rgba(255,255,255,0.7); backdrop-filter: blur(5px); border-color: rgba(255,255,255,0.4) !important;">
                                    @if($pm->image)
                                        <img src="{{ asset('uploads/' . $pm->image) }}" alt="{{ $pm->name }}" class="me-3 rounded" style="width: 60px; height: 60px; object-fit: contain; background: #fff; padding: 5px; border: 1px solid #eee;">
                                    @else
                                        <div class="me-3 rounded d-flex align-items-center justify-content-center bg-light text-secondary" style="width: 60px; height: 60px; border: 1px solid #eee;">
                                            <i class="bi bi-wallet2 fs-3"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h6 class="mb-1 fw-bold text-dark">{{ $pm->name }}</h6>
                                        @php
                                            $credentials = json_decode($pm->credentials, true);
                                        @endphp
                                        @if($credentials && is_array($credentials))
                                            <div class="text-muted small" style="line-height: 1.4;">
                                                @foreach($credentials as $key => $val)
                                                    <strong>{{ ucfirst($key) }}:</strong> <span dir="ltr">{{ $val }}</span><br>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="whatsapp-note mt-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        <div>
                            <strong>Crucial Note (ملاحظة هامة ):</strong><br>
                            <span dir="rtl" style="font-family: 'Cairo', sans-serif;">الرجاء إرسال إيصال/إثبات الدفع عبر الواتساب إلى الرقم التالي:</span><br>
                            Please send the payment receipt/proof via WhatsApp to the following number:<br>
                            <strong class="fs-5 d-inline-block mt-1" dir="ltr" style="letter-spacing: 1px;">{{ $settings->mobile ?? '+970XXXXXXXXX' }}</strong>
                        </div>
                    </div>

                    <div class="mt-3 p-3 rounded d-flex align-items-start" style="background: rgba(138, 21, 56, 0.05); border: 1px dashed var(--date-red); gap: 1rem;" dir="ltr">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--date-red)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 4px;">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <div style="flex-grow: 1;">
                            <strong class="mb-2 d-block" style="color: var(--date-red); font-family: 'Inter', sans-serif; font-size: 1.05rem;" dir="ltr">
                                Note: Payment Schedule over 3 months:
                                <span dir="rtl" style="font-family: 'Cairo', sans-serif; font-size: 0.95rem; font-weight: normal; margin-left: 5px;">(ملاحظة: يتم تسديد الرسوم على ثلاثة شهور كالتالي)</span>
                            </strong>
                            
                            <ul style="color: var(--date-red); font-family: 'Inter', sans-serif; list-style-type: disc; padding-left: 1.5rem; margin-bottom: 0;" dir="ltr">
                                <li class="mb-2">
                                    <strong>First month upon registration:</strong> 500 NIS + Book fees 150 NIS.<br>
                                    <span dir="rtl" style="font-family: 'Cairo', sans-serif; font-size: 0.9rem; opacity: 0.9; display: inline-block; margin-top: 2px;">(الشهر الأول عند التسجيل: 500 شيكل بالإضافة لرسوم الكتب 150 شيكل)</span>
                                </li>
                                <li class="mb-2">
                                    <strong>Second month:</strong> 400 NIS.<br>
                                    <span dir="rtl" style="font-family: 'Cairo', sans-serif; font-size: 0.9rem; opacity: 0.9; display: inline-block; margin-top: 2px;">(الشهر الثاني: 400 شيكل)</span>
                                </li>
                                <li>
                                    <strong>Third month:</strong> 300 NIS.<br>
                                    <span dir="rtl" style="font-family: 'Cairo', sans-serif; font-size: 0.9rem; opacity: 0.9; display: inline-block; margin-top: 2px;">(الشهر الثالث: 300 شيكل)</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">
                    <span class="btn-text">Submit (تأكيد الطلب)</span>
                    <div class="loader"></div>
                </button>
            </form>
        </div>
        
        <!-- Social Media & OTE Logo Footer -->
        <div class="registration-footer">
            <div class="ote-logo-container d-flex justify-content-center align-items-center flex-wrap mb-4" style="gap: 2rem;">
                <img src="{{ url('assets/oxford/img/OTE-Approved-Test-Centre-Logo.png') }}" alt="OTE Approved Test Centre">
                <img src="{{ asset('assets/images/oxford-ielts.png') }}" alt="Oxford ELLT Global" style="mix-blend-mode: multiply;">
            </div>
            @if(isset($socials) && count($socials) > 0)
                <h5 style="color:var(--primary); font-family: 'Cairo', sans-serif; margin-bottom: 1.5rem; font-size: 1.15rem; font-weight: 700;">تابعنا على منصات التواصل (Follow Us)</h5>
                <div class="social-icons">
                    @foreach($socials as $social)
                        <a href="{{ $social->link }}" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="{{ $social->name ?? 'Social Link' }}">
                            @php
                                $iconName = trim(str_replace(['fa-brands fa-', 'fa-solid fa-', 'fab fa-', 'fas fa-', 'fa-', 'fa '], '', $social->icon));
                                if($iconName == 'x-twitter' || $iconName == 'twitter') $iconName = 'twitter-x';
                            @endphp
                            <i class="bi bi-{{ $iconName }}"></i>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Floating WhatsApp -->
    @php
        $waNumber = $settings->mobile ?? '';
        if(str_starts_with($waNumber, '05')) {
            $waNumber = '970' . ltrim($waNumber, '0');
        } else {
            $waNumber = preg_replace('/[^0-9]/', '', $waNumber);
        }
    @endphp
    <a href="https://wa.me/{{ $waNumber }}" target="_blank" class="floating-wa" aria-label="WhatsApp Support">
        <i class="bi bi-whatsapp"></i>
    </a>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Splash Screen Logic
            setTimeout(() => {
                document.getElementById('splashScreen').classList.add('loaded');
                setTimeout(() => {
                    document.getElementById('splashScreen').style.display = 'none';
                }, 1200); // Wait for fade transition
            }, 2500); // Longer duration according to standards

            // Age Logic
            const dobInput = document.getElementById('dobInput');
            const parentSection = document.getElementById('parentSection');
            const parentName = document.getElementById('parent_name');
            const parentPhone = document.getElementById('parent_phone');
            const typeKids = document.getElementById('type_kids');
            const typeAdults = document.getElementById('type_adults');

            dobInput.addEventListener('change', function() {
                if(!this.value) return;
                const dob = new Date(this.value);
                const today = new Date();
                let age = today.getFullYear() - dob.getFullYear();
                const m = today.getMonth() - dob.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
                    age--;
                }

                if(age < 15) {
                    parentSection.style.display = 'block';
                    parentName.setAttribute('required', 'required');
                    parentPhone.setAttribute('required', 'required');
                    if(typeKids) {
                        typeKids.checked = true;
                        typeKids.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                } else {
                    parentSection.style.display = 'none';
                    parentName.removeAttribute('required');
                    parentPhone.removeAttribute('required');
                    if(typeAdults) {
                        typeAdults.checked = true;
                        typeAdults.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }
            });

            // Placement Test Logic
            const placementToggle = document.getElementById('placementTestToggle');
            const placementDateSection = document.getElementById('placementDateSection');
            const placementDateInput = document.getElementById('placement_test_date');

            placementToggle.addEventListener('change', function() {
                if(this.checked) {
                    placementDateSection.style.display = 'block';
                    placementDateInput.setAttribute('required', 'required');
                } else {
                    placementDateSection.style.display = 'none';
                    placementDateInput.removeAttribute('required');
                }
            });

            // Invoice Logic
            const programSelect = document.getElementById('programSelect');
            const invoiceBox = document.getElementById('invoiceBox');
            const invProgramName = document.getElementById('invProgramName');
            const invMinAmount = document.getElementById('invMinAmount');

            programSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if(selectedOption.value) {
                    const title = selectedOption.getAttribute('data-title');
                    const totalFee = parseFloat(selectedOption.getAttribute('data-total-fee')) || 0;
                    const minDue = parseFloat(selectedOption.getAttribute('data-min-due')) || 0;
                    const feesDetailsStr = selectedOption.getAttribute('data-fees-details');
                    
                    invProgramName.textContent = title;
                    document.getElementById('invTotalFee').textContent = totalFee.toFixed(2);
                    invMinAmount.textContent = minDue.toFixed(2);
                    
                    // Render fee details
                    const detailsList = document.getElementById('invFeeDetailsList');
                    detailsList.innerHTML = '';
                    if (feesDetailsStr) {
                        try {
                            const details = JSON.parse(feesDetailsStr);
                            if (details.length > 0) {
                                details.forEach(fee => {
                                    const tr = document.createElement('tr');
                                    tr.innerHTML = `<td>${fee.name}</td><td class="fw-bold" dir="ltr">₪ ${parseFloat(fee.amount).toFixed(2)}</td>`;
                                    detailsList.appendChild(tr);
                                });
                            } else {
                                detailsList.innerHTML = '<tr><td colspan="2" class="text-muted py-2">No fees specified</td></tr>';
                            }
                        } catch(e) {
                            console.error('Error parsing fees details', e);
                        }
                    }

                    invoiceBox.style.display = 'block';
                } else {
                    invoiceBox.style.display = 'none';
                }
            });

            // Health Issues Logic
            const healthYes = document.getElementById('health_yes');
            const healthNo = document.getElementById('health_no');
            const healthIssuesDetailsContainer = document.getElementById('healthIssuesDetailsContainer');
            const healthIssuesDetailsInput = document.querySelector('textarea[name="health_issues_details"]');

            function toggleHealthDetails() {
                if(healthYes.checked) {
                    healthIssuesDetailsContainer.style.display = 'block';
                    healthIssuesDetailsInput.setAttribute('required', 'required');
                } else {
                    healthIssuesDetailsContainer.style.display = 'none';
                    healthIssuesDetailsInput.removeAttribute('required');
                    healthIssuesDetailsInput.value = '';
                }
            }

            healthYes.addEventListener('change', toggleHealthDetails);
            healthNo.addEventListener('change', toggleHealthDetails);

            // Form Submission
            const form = document.getElementById('registrationForm');
            const submitBtn = document.getElementById('submitBtn');
            const btnText = submitBtn.querySelector('.btn-text');
            const loader = submitBtn.querySelector('.loader');

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Frontend JS validation for Arabic Name and Phone
                let hasError = false;
                const arNameInput = form.querySelector('[name="full_name_ar"]');
                const phoneInput = form.querySelector('[name="phone"]');
                
                const arRegex = /^[\u0600-\u06FF\s]+$/;
                const phoneRegex = /^05[0-9]{8}$/;

                // Clear previous errors
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                if(arNameInput.value && !arRegex.test(arNameInput.value)) {
                    arNameInput.classList.add('is-invalid');
                    let feedback = arNameInput.parentNode.querySelector('.invalid-feedback');
                    if(feedback) feedback.textContent = 'الرجاء إدخال حروف عربية فقط.';
                    hasError = true;
                }

                if(phoneInput.value && !phoneRegex.test(phoneInput.value)) {
                    phoneInput.classList.add('is-invalid');
                    let feedback = phoneInput.parentNode.querySelector('.invalid-feedback');
                    if(feedback) feedback.textContent = 'رقم الجوال يجب أن يبدأ بـ 05 ويتكون من 10 أرقام.';
                    hasError = true;
                }

                if(hasError) {
                    const firstError = document.querySelector('.is-invalid');
                    if(firstError) firstError.scrollIntoView({behavior: 'smooth', block: 'center'});
                    return;
                }

                const formData = new FormData(this);
                // Handle un-checked checkbox
                if(!placementToggle.checked) {
                    formData.append('placement_test', '0');
                }

                submitBtn.disabled = true;
                btnText.style.display = 'none';
                loader.style.display = 'block';

                fetch('{{ route("registration.standalone.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    submitBtn.disabled = false;
                    btnText.style.display = 'block';
                    loader.style.display = 'none';

                    if(data.success) {
                        Swal.fire({
                            title: '<span style="font-family: \'Tajawal\', sans-serif; font-weight: 700;">تم التسجيل بنجاح!</span>',
                            html: '<div style="direction: ltr; font-size: 1.1rem; line-height: 1.6; color: var(--text-muted);">Your registration has been submitted successfully.<br>Please do not forget to send the payment receipt via WhatsApp.</div>',
                            icon: 'success',
                            color: 'var(--date-red)',
                            iconColor: 'var(--date-red)',
                            confirmButtonText: '<div style="display:flex; flex-direction:column; padding: 0.25rem; line-height: 1.4;"><span style="font-size: 1.2rem; font-weight: 700; letter-spacing: 1px;">REGISTER ANOTHER</span><span style="font-family: \'Tajawal\', sans-serif; font-size: 1.1rem; font-weight: 500; opacity: 0.9;">تسجيل طالب آخر</span></div>',
                            confirmButtonColor: 'var(--oxford-blue)',
                            allowOutsideClick: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.reload();
                            }
                        });
                    } else if(data.errors) {
                        for(let field in data.errors) {
                            let input = form.querySelector(`[name="${field}"]`);
                            if(input) {
                                input.classList.add('is-invalid');
                                let feedback = input.parentNode.querySelector('.invalid-feedback');
                                if(feedback) {
                                    feedback.textContent = data.errors[field][0];
                                }
                            }
                        }
                        // Scroll to first error
                        const firstError = document.querySelector('.is-invalid');
                        if(firstError) {
                            firstError.scrollIntoView({behavior: 'smooth', block: 'center'});
                        }
                    } else {
                        alert(data.message || 'An error occurred.');
                    }
                })
                .catch(error => {
                    submitBtn.disabled = false;
                    btnText.style.display = 'block';
                    loader.style.display = 'none';
                    alert('Network error. Please try again.');
                    console.error('Error:', error);
                });
            });
        });
    </script>
</body>
</html>
