<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <!--begin::Head-->

    <head>
        <title>تسجيل الدخول :: لوحة التحكم</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="shortcut icon" href="{{ asset('assets/oxford/img/favicon.png') }}" />
        <!--begin::Fonts(mandatory for all pages)-->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Cairo:300,400,500,600,700" />
        <!--end::Fonts-->
        <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
        <link href="{{ asset('assets/css/plugins.bundle.rtl.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/css/style.bundle.rtl.css') }}" rel="stylesheet" type="text/css" />
        <!--end::Global Stylesheets Bundle-->
        <style>
            html, body { font-family: Cairo, Helvetica, "sans-serif"; }

            .btn-primary { background-color: #003366 !important; border-color: #003366 !important; }
            .btn-primary:hover { background-color: #002244 !important; border-color: #002244 !important; }

            /* ── Aside animated background ── */
            .login-aside {
                background: linear-gradient(145deg, #03122b 0%, #0a2556 40%, #0f4c81 70%, #1a6aad 100%);
                position: relative;
                overflow: hidden;
            }

            /* ── Floating particles canvas ── */
            #login-canvas {
                position: absolute;
                inset: 0;
                width: 100%;
                height: 100%;
                pointer-events: none;
            }

            /* ── Rotating images ── */
            .image-rotation-container {
                display: grid;
                grid-template-columns: 1fr;
                grid-template-rows: 1fr;
                place-items: center;
                width: 100%;
                position: relative;
                z-index: 2;
            }
            .rotating-image {
                grid-area: 1 / 1;
                opacity: 0;
                transition: opacity 1.5s ease-in-out;
                z-index: 1;
                filter: drop-shadow(0 8px 32px rgba(0,0,0,.4));
            }
            .rotating-image.active { opacity: 1; z-index: 2; }

            .login-aside h1,
            .login-aside div { position: relative; z-index: 2; }
        </style>
    </head>
    <!--end::Head-->
    <!--begin::Body-->

    <body id="kt_body" class="app-blank">
        <!--begin::Root-->
        <div class="d-flex flex-column flex-root" id="kt_app_root">
            <!--begin::Authentication - Sign-in -->
            <div class="d-flex flex-column flex-lg-row flex-column-fluid">
                <!--begin::Body-->
                <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
                    <!--begin::Form-->
                    <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                        <!--begin::Wrapper-->
                        <div class="w-lg-500px p-10">
                            <!--begin::Form-->

                            <form class="form w-100" novalidate="novalidate" id="kt_sign_in_form" action=""
                                  method="post">
                                <div class="mb-14 text-center">
                                    <a href="/" class="">
                                        <img alt="Logo" src="{{ asset('assets/oxford/img/logo.png') }}" class="h-100px">
                                    </a>
                                </div>
                                
                                @if(Session::has('danger'))
                                <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                                    <i class="ki-duotone ki-shield-tick fs-2hx text-danger me-4"><span class="path1"></span><span class="path2"></span></i>
                                    <div class="d-flex flex-column">
                                        <h4 class="mb-1 text-danger">خطأ</h4>
                                        <span>{{ Session::get('danger') }}</span>
                                    </div>
                                </div>
                                @endif

                                @if(Session::has('success'))
                                <div class="alert alert-success d-flex align-items-center p-5 mb-10">
                                    <i class="ki-duotone ki-shield-tick fs-2hx text-success me-4"><span class="path1"></span><span class="path2"></span></i>
                                    <div class="d-flex flex-column">
                                        <h4 class="mb-1 text-success">تم بنجاح</h4>
                                        <span>{{ Session::get('success') }}</span>
                                    </div>
                                </div>
                                @endif

                                <!--begin::Heading-->
                                <div class="text-center mb-11">
                                    <!--begin::Title-->
                                    <h1 class="text-dark fw-bolder mb-3">تسجيل الدخول</h1>
                                    <div class="text-gray-500 fw-semibold fs-6">لوحة تحكم إدارة أكسفورد</div>
                                    <!--end::Title-->
                                </div>
                                <!--begin::Heading-->
                                <!--begin::Input group=-->
                                <div class="fv-row mb-8">
                                    <!--begin::username-->
                                    <input type="text" placeholder="اسم المستخدم" name="username" autocomplete="off"
                                           class="form-control bg-transparent" required />
                                    <!--end::username-->
                                </div>
                                <!--end::Input group=-->
                                <div class="fv-row mb-3">
                                    <!--begin::Password-->
                                    <input type="password" placeholder="كلمة المرور" name="password" autocomplete="off"
                                           class="form-control bg-transparent" required />
                                    <!--end::Password-->
                                </div>
                                <!--end::Input group=-->
                                
                                <!--begin::Wrapper-->
                                <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
                                    <div></div>
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1"/>
                                        <label class="form-check-label text-gray-700 fs-6" for="remember">
                                            تذكرني
                                        </label>
                                    </div>
                                </div>
                                <!--end::Wrapper-->

                                <!--begin::Submit button-->
                                <div class="d-grid mb-10">
                                    <button type="submit" id="kt_sign_in_submit" class="btn btn-primary">
                                        <!--begin::Indicator label-->
                                        <span class="indicator-label">دخول</span>
                                        <!--end::Indicator label-->
                                        <!--begin::Indicator progress-->
                                        <span class="indicator-progress">الرجاء الانتظار...
                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                        <!--end::Indicator progress-->
                                    </button>
                                </div>
                                {{ csrf_field() }}
                                <!--end::Submit button-->
                            </form>
                            <!--end::Form-->
                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Form-->
                </div>
                <!--end::Body-->
                <!--begin::Aside-->
                <div class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center order-1 order-lg-2 login-aside">
                    <canvas id="login-canvas"></canvas>
                    <!--begin::Content-->
                    <div class="d-flex flex-column flex-center py-7 py-lg-15 px-5 px-md-15 w-100">
                        <!--begin::Image Rotation-->
                        <div class="image-rotation-container mb-5 mb-lg-20">
                            <img class="rotating-image d-none d-lg-block mx-auto w-275px w-md-50 w-xl-500px active"
                                 src="{{ asset('assets/media/illustrations/sigma-1/7-dark.png') }}" alt="" />
                            <img class="rotating-image d-none d-lg-block mx-auto w-275px w-md-50 w-xl-500px"
                                 src="{{ asset('assets/media/illustrations/sigma-1/2-dark.png') }}" alt="" />
                            <img class="rotating-image d-none d-lg-block mx-auto w-275px w-md-50 w-xl-500px"
                                 src="{{ asset('assets/oxford/img/logo.png') }}" alt="" />
                        </div>
                        <!--end::Image Rotation-->
                        <h1 class="d-none d-lg-block fw-bolder fs-2qx text-center mb-7 text-white">أكسفورد للغات</h1>
                        <div class="d-none d-lg-block fs-base text-center text-white opacity-75" style="max-width: 450px;">
                        مرحباً بك في لوحة تحكم أكسفورد. يمكنك من هنا إدارة جميع جوانب الموقع، المواعيد، الدورات، والطلاب بكل سهولة واحترافية.
                        </div>
                    </div>
                    <!--end::Content-->
                </div>
                <!--end::Aside-->
            </div>
            <!--end::Authentication - Sign-in-->
        </div>
        <!--end::Root-->
        <!--begin::Javascript-->
        <script>
            var hostUrl = "assets/";
        </script>
        <!--begin::Global Javascript Bundle(mandatory for all pages)-->
        <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
        <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
        <!--end::Global Javascript Bundle-->
        
        <script>
            // Form submit indicator
            document.querySelector('#kt_sign_in_form').addEventListener('submit', function(e) {
                if (e.target.checkValidity()) {
                    var btn = document.querySelector('#kt_sign_in_submit');
                    btn.setAttribute('data-kt-indicator', 'on');
                    btn.disabled = true;
                }
            });

            // Image rotation
            document.addEventListener('DOMContentLoaded', function() {
                var images = document.querySelectorAll('.rotating-image');
                var idx = 0;
                if (images.length > 1) {
                    setInterval(function() {
                        images[idx].classList.remove('active');
                        idx = (idx + 1) % images.length;
                        images[idx].classList.add('active');
                    }, 9000);
                }
            });

            // ── Balls & Stars canvas animation ──────────────────────────
            (function () {
                var canvas = document.getElementById('login-canvas');
                if (!canvas) return;
                var ctx = canvas.getContext('2d');
                var W, H, particles = [];

                var COLORS = [
                    'rgba(255,255,255,VAL)',
                    'rgba(247,183,51,VAL)',
                    'rgba(44,154,183,VAL)',
                    'rgba(100,180,255,VAL)',
                ];

                function resize() {
                    W = canvas.width  = canvas.offsetWidth;
                    H = canvas.height = canvas.offsetHeight;
                }

                // Draw a 5-pointed star
                function drawStar(x, y, r, color) {
                    ctx.save();
                    ctx.translate(x, y);
                    ctx.beginPath();
                    for (var i = 0; i < 5; i++) {
                        var outer = (Math.PI * 2 * i / 5) - Math.PI / 2;
                        var inner = outer + Math.PI / 5;
                        var ox = Math.cos(outer) * r, oy = Math.sin(outer) * r;
                        var ix = Math.cos(inner) * (r * 0.42), iy = Math.sin(inner) * (r * 0.42);
                        if (i === 0) ctx.moveTo(ox, oy); else ctx.lineTo(ox, oy);
                        ctx.lineTo(ix, iy);
                    }
                    ctx.closePath();
                    ctx.fillStyle = color;
                    ctx.fill();
                    ctx.restore();
                }

                function rnd(min, max) { return Math.random() * (max - min) + min; }
                function rndColor(a) { return COLORS[Math.floor(Math.random() * COLORS.length)].replace('VAL', a); }

                function createParticle() {
                    var isStar = Math.random() > 0.45; // 55% balls, 45% stars
                    return {
                        x:     rnd(0, W),
                        y:     rnd(0, H),
                        r:     isStar ? rnd(3, 8) : rnd(3, 9),
                        vx:    rnd(-0.35, 0.35),
                        vy:    rnd(-0.5, -0.12),
                        alpha: rnd(0.15, 0.7),
                        color: rndColor(1),
                        type:  isStar ? 'star' : 'ball',
                        pulse: rnd(0, Math.PI * 2),
                        pulseSpeed: rnd(0.012, 0.028),
                    };
                }

                function init() {
                    resize();
                    particles = [];
                    var count = Math.floor((W * H) / 9000);
                    count = Math.max(35, Math.min(count, 90));
                    for (var i = 0; i < count; i++) particles.push(createParticle());
                }

                function draw() {
                    ctx.clearRect(0, 0, W, H);

                    particles.forEach(function(p) {
                        // Pulse alpha
                        p.pulse += p.pulseSpeed;
                        var a = p.alpha * (0.6 + 0.4 * Math.sin(p.pulse));
                        var color = p.color.replace('1)', a + ')');

                        if (p.type === 'ball') {
                            // Soft glow
                            var grd = ctx.createRadialGradient(p.x, p.y, 0, p.x, p.y, p.r);
                            grd.addColorStop(0, color);
                            grd.addColorStop(1, p.color.replace('1)', '0)'));
                            ctx.beginPath();
                            ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                            ctx.fillStyle = grd;
                            ctx.fill();
                        } else {
                            drawStar(p.x, p.y, p.r, color);
                        }

                        // Move
                        p.x += p.vx;
                        p.y += p.vy;

                        // Wrap around
                        if (p.y < -20)  { p.y = H + 20; p.x = rnd(0, W); }
                        if (p.x < -20)  p.x = W + 20;
                        if (p.x > W+20) p.x = -20;
                    });

                    requestAnimationFrame(draw);
                }

                window.addEventListener('resize', init);
                init();
                draw();
            })();
        </script>
    </body>
    <!--end::Body-->

</html>
