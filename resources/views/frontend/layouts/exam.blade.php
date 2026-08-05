<!doctype html>
<html lang="ar" dir="rtl">
<head>
    @include('frontend.general.head')
    <style>
        html, body {
            background: #f2f4f8 !important;
            min-height: 100vh;
        }
        /* Hide anything the shared head/footer partials might still inject (nav remnants, etc). */
        body.exam-focus-mode > *:not(.exam-shell) { display: none !important; }

        .exam-shell {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .exam-shell__header {
            position: sticky;
            top: 0;
            z-index: 50;
            background: #14213d;
            color: #fff;
            padding: 14px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,.15);
        }
        .exam-shell__title { font-weight: 700; font-size: 18px; margin: 0; }
        .exam-shell__body {
            flex: 1;
            max-width: 820px;
            width: 100%;
            margin: 0 auto;
            padding: 28px 16px 60px;
        }
        .exam-timer {
            font-variant-numeric: tabular-nums;
            font-weight: 800;
            font-size: 18px;
            background: rgba(255,255,255,.12);
            padding: 6px 16px;
            border-radius: 8px;
        }
        .exam-timer.exam-timer--danger { background: #b3261e; }
    </style>
    @yield('css')
</head>
<body class="exam-focus-mode">
    <div class="exam-shell">
        @yield('content')
    </div>

    <script src="{{ url('assets/oxford/js/jquery-2.2.4.min.js') }}"></script>
    @yield('js')
</body>
</html>
