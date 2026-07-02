<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oxford Center | Standalone Registration</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS for grid -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        }

        body {
            background: var(--bg-gradient);
            background-attachment: fixed;
            font-family: 'Tajawal', 'Inter', sans-serif;
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
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
            overflow: hidden;
            transition: transform 0.3s ease;
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
            border-bottom: 2px solid var(--accent);
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
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.15);
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
            background: var(--accent);
            border-radius: 4px 0 0 4px;
        }

        .invoice-box:hover {
            border-color: var(--accent);
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
            border-color: var(--accent) !important;
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--accent) 0%, #b8962b 100%);
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
            box-shadow: 0 8px 16px rgba(212, 175, 55, 0.25);
            margin-top: 2rem;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, var(--accent-hover) 0%, var(--accent) 100%);
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(212, 175, 55, 0.35);
            color: #fff;
        }
        
        .btn-submit:active {
            transform: translateY(1px);
        }

        .whatsapp-note {
            background: rgba(16, 185, 129, 0.1);
            border-left: 4px solid var(--success);
            color: #065f46;
            padding: 1rem 1.5rem;
            border-radius: 0 12px 12px 0;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .whatsapp-note svg {
            color: var(--success);
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

        /* Success Modal Styles */
        #successModal {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(10, 37, 64, 0.8);
            backdrop-filter: blur(5px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-content-custom {
            background: white;
            padding: 3rem 2rem;
            border-radius: 20px;
            text-align: center;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: scaleIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .modal-icon {
            width: 80px;
            height: 80px;
            background: var(--success);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }

        .modal-icon svg {
            width: 40px;
            height: 40px;
            color: white;
        }

        @keyframes scaleIn {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
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
            background: transparent;
            pointer-events: none;
            overflow: hidden;
        }
        .splash-door {
            position: absolute;
            top: 0;
            width: 50%;
            height: 100%;
            background: var(--primary);
            transition: transform 1.2s cubic-bezier(0.77, 0, 0.175, 1);
            z-index: 1;
        }
        .splash-door.left {
            left: 0;
            transform-origin: left;
        }
        .splash-door.right {
            right: 0;
            transform-origin: right;
        }
        .splash-screen.loaded .splash-door.left {
            transform: translateX(-100%);
        }
        .splash-screen.loaded .splash-door.right {
            transform: translateX(100%);
        }
        .splash-logo-container {
            position: relative;
            z-index: 2;
            transition: opacity 0.6s ease 0.3s;
        }
        .splash-screen.loaded .splash-logo-container {
            opacity: 0;
        }
        .splash-logo {
            width: 180px;
            animation: pulse-splash 1.5s infinite;
            filter: drop-shadow(0 4px 10px rgba(0,0,0,0.3));
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
            border-radius: 50%;
            background: var(--surface);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.06);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .social-icon:hover {
            background: var(--accent);
            color: #fff;
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(212, 175, 55, 0.3);
            border-color: var(--accent);
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
    </style>
</head>
<body>

    <!-- Splash Screen -->
    <div class="splash-screen" id="splashScreen">
        <div class="splash-door left"></div>
        <div class="splash-door right"></div>
        <div class="splash-logo-container">
            <img src="{{ url('assets/oxford/img/logo.png') }}" alt="Oxford Center Logo" class="splash-logo" onerror="this.src='https://via.placeholder.com/120x120.png?text=Logo'">
        </div>
    </div>

    <div class="registration-container">
        <div class="header">
            <img src="{{ url('assets/oxford/img/logo.png') }}" alt="Oxford Center Logo" onerror="this.src='https://via.placeholder.com/120x120.png?text=Logo'">
            <h1>Student Registration Form</h1>
            <p style="font-family: 'Cairo', sans-serif;" dir="rtl">نموذج تسجيل الطلاب - مركز أوكسفورد</p>
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
                                $feesJson = htmlspecialchars(json_encode($progFees->map(function($f) {
                                    return ['name' => $f->type_name, 'amount' => $f->amount];
                                })->toArray()));
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
                            <p class="mb-2 text-muted">Fee Details (تفاصيل الرسوم):</p>
                            <ul class="list-group mb-2" id="invFeeDetailsList">
                                <!-- Dynamically populated list -->
                            </ul>
                            <p class="fs-6 text-muted mb-0"><small>* Includes program registration and base tuition. (تشمل جميع المتطلبات الأساسية للبرنامج)</small></p>
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
                            <strong>Crucial Note (ملاحظة هامة جداً):</strong><br>
                            Please send the payment receipt/proof via WhatsApp to the following number:<br>
                            <span dir="ltr">{{ $settings->mobile ?? '+970XXXXXXXXX' }}</span>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">
                    <span class="btn-text">Complete Registration (إتمام التسجيل)</span>
                    <div class="loader"></div>
                </button>
            </form>
        </div>
        
        <!-- Social Media & OTE Logo Footer -->
        <div class="registration-footer">
            @if(isset($socials) && count($socials) > 0)
                <h5 style="color:var(--primary); font-family: 'Cairo', sans-serif; margin-bottom: 1.5rem; font-size: 1.15rem; font-weight: 700;">تابعنا على منصات التواصل (Follow Us)</h5>
                <div class="social-icons">
                    @foreach($socials as $social)
                        <a href="{{ $social->link }}" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="{{ $social->name ?? 'Social Link' }}">
                            <i class="{{ str_starts_with($social->icon, 'fa') ? '' : 'fa ' }}{{ $social->icon }}"></i>
                        </a>
                    @endforeach
                </div>
            @endif
            <div class="ote-logo-container">
                <img src="{{ url('assets/oxford/img/OTE-Approved-Test-Centre-Logo.png') }}" alt="OTE Approved Test Centre">
            </div>
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
        <i class="fa-brands fa-whatsapp"></i>
    </a>

    <!-- Success Modal -->
    <div id="successModal">
        <div class="modal-content-custom">
            <div class="modal-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </div>
            <h2 style="color:var(--primary-blue); font-weight:700; margin-bottom:1rem;">Registration Successful!</h2>
            <h3 style="font-family:'Cairo'; color:var(--text-muted); font-size:1.5rem; margin-bottom:1.5rem;">تم التسجيل بنجاح!</h3>
            <p style="color:var(--text-dark); margin-bottom:2rem;">Your registration has been submitted successfully. Please do not forget to send the payment receipt via WhatsApp.</p>
            <button onclick="window.location.reload()" class="btn-submit" style="margin-top:0;">Register Another (تسجيل طالب آخر)</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Splash Screen Logic
            setTimeout(() => {
                document.getElementById('splashScreen').classList.add('loaded');
                setTimeout(() => {
                    document.getElementById('splashScreen').style.display = 'none';
                }, 1200);
            }, 800);

            // Age Logic
            const dobInput = document.getElementById('dobInput');
            const parentSection = document.getElementById('parentSection');
            const parentName = document.getElementById('parent_name');
            const parentPhone = document.getElementById('parent_phone');

            dobInput.addEventListener('change', function() {
                if(!this.value) return;
                const dob = new Date(this.value);
                const today = new Date();
                let age = today.getFullYear() - dob.getFullYear();
                const m = today.getMonth() - dob.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
                    age--;
                }

                if(age <= 15) {
                    parentSection.style.display = 'block';
                    parentName.setAttribute('required', 'required');
                    parentPhone.setAttribute('required', 'required');
                } else {
                    parentSection.style.display = 'none';
                    parentName.removeAttribute('required');
                    parentPhone.removeAttribute('required');
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
                                    const li = document.createElement('li');
                                    li.className = 'list-group-item d-flex justify-content-between align-items-center py-2';
                                    li.innerHTML = `<span>${fee.name}</span> <span class="fw-bold">₪ ${parseFloat(fee.amount).toFixed(2)}</span>`;
                                    detailsList.appendChild(li);
                                });
                            } else {
                                detailsList.innerHTML = '<li class="list-group-item text-muted py-2">No fees specified</li>';
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
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                        document.getElementById('successModal').style.display = 'flex';
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
