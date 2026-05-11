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
            html,
            body {
                font-family: Cairo, Helvetica, "sans-serif";
            }
            .login-aside {
                background-color: #f2f6ff;
            }
            .btn-primary {
                background-color: #003366 !important;
                border-color: #003366 !important;
            }
            .btn-primary:hover {
                background-color: #002244 !important;
                border-color: #002244 !important;
            }
            .image-rotation-container {
                display: grid;
                grid-template-columns: 1fr;
                grid-template-rows: 1fr;
                place-items: center;
                width: 100%;
            }
            .rotating-image {
                grid-area: 1 / 1;
                opacity: 0;
                transition: opacity 1.5s ease-in-out;
                z-index: 1;
            }
            .rotating-image.active {
                opacity: 1;
                z-index: 2;
            }
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
                        <h1 class="d-none d-lg-block fw-bolder fs-2qx text-center mb-7" style="color: #003366;">أكسفورد للغات</h1>
                        <div class="d-none d-lg-block fs-base text-center" style="color: #4B5563; max-width: 450px;">
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
            // Add loading indicator on form submit
            document.querySelector('#kt_sign_in_form').addEventListener('submit', function(e) {
                var form = e.target;
                if (form.checkValidity()) {
                    var submitButton = document.querySelector('#kt_sign_in_submit');
                    submitButton.setAttribute('data-kt-indicator', 'on');
                    submitButton.disabled = true;
                }
            });

            // Image Rotation Logic
            document.addEventListener('DOMContentLoaded', function() {
                const images = document.querySelectorAll('.rotating-image');
                let currentIndex = 0;

                if (images.length > 1) {
                    setInterval(function() {
                        images[currentIndex].classList.remove('active');
                        currentIndex = (currentIndex + 1) % images.length;
                        images[currentIndex].classList.add('active');
                    }, 9000); // 9 seconds
                }
            });
        </script>
    </body>
    <!--end::Body-->

</html>
