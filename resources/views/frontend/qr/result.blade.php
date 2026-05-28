<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $title }} | Oxford English Center</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=Plus+Jakarta+Sans:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<style>
    body {
        background: linear-gradient(135deg, #003366 0%, #002a55 100%);
        min-height: 100vh;
        display: flex; align-items: center; justify-content: center;
        font-family: 'Plus Jakarta Sans', sans-serif;
        padding: 24px;
        margin: 0;
    }
    .qr-card {
        background: #fff;
        border-radius: 22px;
        padding: 40px 32px;
        max-width: 460px;
        text-align: center;
        box-shadow: 0 24px 60px rgba(0,0,0,0.3);
        position: relative;
        overflow: hidden;
    }
    .qr-card::before, .qr-card::after {
        content: '';
        position: absolute;
        width: 26px; height: 26px;
        border: 2px solid #ffcc00;
        opacity: 0.6;
    }
    .qr-card::before { top: 14px; right: 14px; border-left: none; border-bottom: none; }
    .qr-card::after  { bottom: 14px; left: 14px; border-right: none; border-top: none; }
    .qr-icon {
        width: 90px; height: 90px;
        border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 3rem; margin-bottom: 18px;
    }
    .qr-icon.success { background: #d1fae5; color: #065f46; box-shadow: 0 8px 22px rgba(6,95,70,0.18); }
    .qr-icon.error   { background: #fee2e2; color: #dc2626; box-shadow: 0 8px 22px rgba(220,38,38,0.18); }
    .qr-icon.info    { background: #fff9e0; color: #92400e; box-shadow: 0 8px 22px rgba(146,64,14,0.18); }
    .qr-title {
        font-family: 'Fraunces', Georgia, serif;
        font-style: italic;
        font-weight: 600;
        font-size: 1.7rem;
        color: #003366;
        letter-spacing: -0.02em;
        margin: 0 0 12px;
    }
    .qr-message {
        color: #4b5563;
        font-size: 1rem;
        line-height: 1.7;
        margin-bottom: 24px;
    }
    .qr-btn {
        background: #003366;
        color: #ffcc00;
        text-decoration: none;
        padding: 12px 28px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 0.95rem;
        display: inline-block;
        transition: all 0.25s;
        letter-spacing: 0.04em;
    }
    .qr-btn:hover { background: #002a55; transform: translateY(-2px); box-shadow: 0 10px 22px rgba(0,51,102,0.32); }
    .qr-rule {
        width: 60px; height: 3px; background: #ffcc00;
        margin: 0 auto 18px; border-radius: 2px;
    }
</style>
</head>
<body>
<div class="qr-card">
    <div class="qr-icon {{ $status }}">
        @if($status === 'success')<i class="bi bi-check-circle-fill"></i>
        @elseif($status === 'info')<i class="bi bi-info-circle-fill"></i>
        @else<i class="bi bi-x-circle-fill"></i>@endif
    </div>
    <div class="qr-rule"></div>
    <h1 class="qr-title">{{ $title }}</h1>
    <p class="qr-message">{{ $message }}</p>
    @if(!empty($login_url))
        <a href="{{ $login_url }}" class="qr-btn"><i class="bi bi-box-arrow-in-right me-1"></i> تسجيل الدخول</a>
    @else
        <a href="{{ url('/') }}" class="qr-btn"><i class="bi bi-house-fill me-1"></i> العودة للرئيسية</a>
    @endif
</div>
</body>
</html>
