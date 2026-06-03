<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <base href="{{ asset('/') }}" />
    <title>@yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="{{ url('favicon.ico') }}" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600&display=swap&subset=arabic"
        rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />

    @if (App::isLocale('en'))
        <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" />
        <link href="{{ asset('assets/css/style.bundle.css') }}?v={{ time() }}" rel="stylesheet" />
    @else
        <link href="{{ asset('assets/css/plugins.bundle.rtl.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.rtl.css') }}" rel="stylesheet" />
        <link href="{{ asset('assets/css/style.bundle.rtl.css') }}?v={{ time() }}" rel="stylesheet" />
    @endif

    <style>
        .menu-title,
        .form-label,
        .card-label,
        button,
        .page-title h1,
        div {
            font-family: 'Cairo', sans-serif !important;
        }
    </style>
    {{-- Global table alignment fix: keep every cell vertically centred so multi-line cells
         (stacked badges, dates, action buttons) stay consistent across ALL admin tables. --}}
    <style>
        .table > :not(caption) > * > * { vertical-align: middle !important; }
        table.dataTable td, table.dataTable th { vertical-align: middle !important; }
    </style>
    @yield('css')
</head>

<body id="kt_app_body" data-kt-app-layout="dark-sidebar" data-kt-app-header-fixed="true"
    data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true"
    data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true"
    data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true" class="app-default" dir="rtl"
    direction="rtl" style="direction:rtl;">
    <script>
        var defaultThemeMode = "light";
        var themeMode;
        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
            } else {
                if (localStorage.getItem("data-bs-theme") !== null) {
                    themeMode = localStorage.getItem("data-bs-theme");
                } else {
                    themeMode = defaultThemeMode;
                }
            }
            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }
            document.documentElement.setAttribute("data-bs-theme", themeMode);
        }
    </script>
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
            @include('admin.layout.header_top_menu')
            <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
                @include('admin.layout.sidebar_menu')
                <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                    <div class="d-flex flex-column flex-column-fluid">
                        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                                    <h1
                                        class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                                        @yield('title')
                                    </h1>
                                    <div class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                                        @yield('page-breadcrumb')
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2 gap-lg-3">
                                    <a href="javascript:history.back()"
                                        class="btn btn-sm btn-flex bg-body btn-color-gray-700 btn-active-color-primary fw-bold">
                                        <i class="ki-duotone ki-arrow-left fs-6 me-1"><span class="path1"></span><span
                                                class="path2"></span></i> رجوع
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div id="kt_app_content" class="app-content flex-column-fluid">
                            <div id="kt_app_content_container" class="app-container container-xxl">
                                @yield('page-content')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @yield('modal')
    @include('admin.layout.notificatAjax')

    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('admin.components.email-campaign-monitor')
    @include('admin.components.email-composer')
    <script>
        window.metronicFilePickerCallback = function(url, targetId) {
            if (targetId) {
                var input = document.getElementById(targetId);
                if (input) {
                    input.value = url;
                    $(input).trigger('change');

                    // Update preview if exists
                    var previewId = $(input).attr('data-preview') || 'holder';
                    var preview = document.getElementById(previewId);
                    if (preview) {
                        if (preview.tagName === 'IMG') {
                            preview.src = url;
                            preview.style.display = 'block';
                        } else {
                            // If it's a container, find or create an img inside
                            var img = preview.querySelector('img');
                            if (!img) {
                                img = document.createElement('img');
                                img.style.height = '5rem';
                                preview.appendChild(img);
                            }
                            img.src = url;
                            preview.style.display = 'block';
                        }
                    }
                }
            }
        };

        window.openMetronicFileManager = function(type, targetId) {
            var route = "{{ route('admin.file_manager') }}";
            var url = route + "?type=" + type + "&target=" + targetId;
            var w = 1000;
            var h = 700;
            var left = (screen.width / 2) - (w / 2);
            var top = (screen.height / 2) - (h / 2);
            window.open(url, 'MetronicFileManager', 'width=' + w + ',height=' + h + ',top=' + top + ',left=' + left);
        };
    </script>
    @yield('js')
</body>

</html>
