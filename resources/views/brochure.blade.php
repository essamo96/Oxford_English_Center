<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $program->title }} — بروشور | أكاديمية أوكسفورد</title>
    <meta name="description" content="بروشور برنامج {{ $program->title }} من أكاديمية أوكسفورد للغة الإنجليزية">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap&subset=arabic" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, #0d1b2a 0%, #1b2838 50%, #0d1b2a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .brochure-card {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 100%;
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .card-header-area {
            background: linear-gradient(135deg, #1e3a5f 0%, #2d5a8e 100%);
            padding: 40px 30px 30px;
            text-align: center;
            position: relative;
        }
        .card-header-area::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 40px;
            background: #fff;
            border-radius: 24px 24px 0 0;
        }
        .logo-circle {
            width: 80px;
            height: 80px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .logo-circle i {
            font-size: 36px;
            color: #1e3a5f;
        }
        .academy-name {
            color: #fff;
            font-size: 14px;
            font-weight: 400;
            opacity: 0.9;
            margin-bottom: 8px;
        }
        .program-title {
            color: #fff;
            font-size: 24px;
            font-weight: 700;
            line-height: 1.4;
        }
        .card-body-area {
            padding: 30px 30px 20px;
            text-align: center;
        }
        .pdf-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 8px 25px rgba(204,0,0,0.3);
        }
        .pdf-icon i {
            font-size: 40px;
            color: #fff;
        }
        .description {
            color: #666;
            font-size: 15px;
            margin-bottom: 25px;
            line-height: 1.6;
        }
        .download-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 40px;
            background: linear-gradient(135deg, #28a745, #1e8e3e);
            color: #fff;
            border: none;
            border-radius: 14px;
            font-size: 18px;
            font-weight: 700;
            font-family: 'Cairo', sans-serif;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(40,167,69,0.4);
        }
        .download-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(40,167,69,0.5);
            color: #fff;
        }
        .download-btn i {
            font-size: 22px;
        }
        .view-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 30px;
            background: #f8f9fa;
            color: #1e3a5f;
            border: 2px solid #e9ecef;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            font-family: 'Cairo', sans-serif;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            margin-top: 12px;
        }
        .view-btn:hover {
            background: #e9ecef;
            color: #1e3a5f;
        }
        .card-footer-area {
            padding: 20px 30px 25px;
            text-align: center;
            border-top: 1px solid #f0f0f0;
        }
        .footer-text {
            color: #aaa;
            font-size: 12px;
        }
        .footer-text a {
            color: #1e3a5f;
            text-decoration: none;
            font-weight: 600;
        }

        /* PDF Embed for desktop */
        .pdf-embed {
            width: 100%;
            height: 500px;
            border: none;
            border-radius: 12px;
            margin-bottom: 20px;
            display: none;
        }
        @media (min-width: 768px) {
            .pdf-embed { display: block; }
            .brochure-card { max-width: 700px; }
        }
    </style>
</head>
<body>
    <div class="brochure-card">
        <div class="card-header-area">
            <div class="logo-circle">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <p class="academy-name">أكاديمية أوكسفورد للغة الإنجليزية</p>
            <h1 class="program-title">{{ $program->title }}</h1>
        </div>

        <div class="card-body-area">
            <div class="pdf-icon">
                <i class="bi bi-file-earmark-pdf-fill"></i>
            </div>
            <p class="description">
                اطّلع على تفاصيل البرنامج الكاملة، محتوى الدورة، والمزايا من خلال البروشور التالي
            </p>

            {{-- Embedded PDF viewer for desktop --}}
            <iframe src="{{ $serveUrl }}" class="pdf-embed" title="بروشور {{ $program->title }}"></iframe>

            <div class="d-flex flex-column align-items-center gap-2">
                <a href="{{ $downloadUrl }}" download class="download-btn">
                    <i class="bi bi-download"></i>
                    تحميل البروشور
                </a>
                <a href="{{ $serveUrl }}" target="_blank" class="view-btn">
                    <i class="bi bi-eye"></i>
                    عرض في نافذة جديدة
                </a>
            </div>
        </div>

        <div class="card-footer-area">
            <p class="footer-text">
                &copy; {{ date('Y') }}
                <a href="{{ url('/') }}">أكاديمية أوكسفورد</a>
                — جميع الحقوق محفوظة
            </p>
        </div>
    </div>
</body>
</html>
