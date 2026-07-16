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
    {{-- Real-time notification animations --}}
    <style>
        @keyframes rtProgress { from { width: 100%; } to { width: 0%; } }
        @keyframes rtPulse { 0% { transform: scale(1); opacity: 1; } 100% { transform: scale(2.2); opacity: 0; } }
        [data-counter] { transition: all .3s ease; display: inline-block; }
        .rt-bell-badge {
            position: absolute; top: -6px; inset-inline-end: -6px;
            min-width: 18px; height: 18px; padding: 0 4px;
            background: #ef4444; color: #fff; font-size: 10px; font-weight: 700;
            border-radius: 9px; display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 0 2px #fff; z-index: 2;
        }
    </style>
    @yield('css')
    @php
        $sidebarLayout      = $siteSettings->sidebar_layout      ?? 'dark-sidebar';
        $sidebarBgColor     = $siteSettings->sidebar_bg_color    ?? '';
        $sidebarTextColor   = $siteSettings->sidebar_text_color  ?? '';
        $sidebarActiveColor = $siteSettings->sidebar_active_color ?? '';
        $isHeaderLayout     = in_array($sidebarLayout, ['dark-header', 'light-header']);
    @endphp
    <style id="custom-sidebar-settings">
    @if($sidebarBgColor)
        #kt_app_sidebar { background-color: {{ $sidebarBgColor }} !important; }
    @endif
    @if($sidebarTextColor)
        .app-sidebar .menu-title,
        .app-sidebar .menu-link .menu-title,
        .app-sidebar .menu-section .menu-content { color: {{ $sidebarTextColor }} !important; }
        .app-sidebar .menu-link:hover .menu-title { color: {{ $sidebarTextColor }} !important; opacity:.85; }
        .app-sidebar .menu-arrow:after { border-color: {{ $sidebarTextColor }} !important; }
    @endif
    @if($sidebarActiveColor)
        .app-sidebar .menu-item.show > .menu-link .menu-title,
        .app-sidebar .menu-item .menu-link.active .menu-title { color: {{ $sidebarActiveColor }} !important; opacity:1; }
        .app-sidebar .menu-item.show > .menu-link .menu-icon i,
        .app-sidebar .menu-item.show > .menu-link .menu-icon svg path,
        .app-sidebar .menu-item .menu-link.active .menu-icon i,
        .app-sidebar .menu-item .menu-link.active .menu-icon svg path { color: {{ $sidebarActiveColor }} !important; fill: {{ $sidebarActiveColor }} !important; }
        .app-sidebar .menu-item .menu-link.active { background-color: {{ $sidebarActiveColor }}22 !important; }
    @endif
    </style>
</head>
<body id="kt_app_body" data-kt-app-layout="{{ $sidebarLayout }}" data-kt-app-header-fixed="true" data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true" data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true" class="app-default" dir="rtl" direction="rtl" style="direction:rtl;">
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
    @include('admin.students.sms_modal')
    @include('admin.layout.notificatAjax')
    @include('admin.layout.upload_progress')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/resumable.js/1.1.0/resumable.min.js"></script>
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
            var route = "{{ route('file_manager.view') }}";
            var url = route + "?type=" + type + "&target=" + targetId;
            var w = 1000;
            var h = 700;
            var left = (screen.width / 2) - (w / 2);
            var top = (screen.height / 2) - (h / 2);
            window.open(url, 'MetronicFileManager', 'width=' + w + ',height=' + h + ',top=' + top + ',left=' + left);
        };
    </script>
    @yield('js')

    {{-- ============ Real-time admin notifications (laravel-websockets) ============ --}}
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script>
        window.OXFORD_RT = {
            key:              @json(config('broadcasting.connections.pusher.key')),
            host:             @json(env('PUSHER_PUBLIC_HOST', config('broadcasting.connections.pusher.options.host', ''))),
            port:             {{ (int) env('PUSHER_PUBLIC_PORT', config('broadcasting.connections.pusher.options.port', 443)) }},
            scheme:           @json(env('PUSHER_PUBLIC_SCHEME', config('broadcasting.connections.pusher.options.scheme', 'https'))),
            cluster:          @json(config('broadcasting.connections.pusher.options.cluster', 'mt1')),
            branch_id:        @json(auth()->guard('admin')->user()?->branch_id),
            broadcast_driver: @json(config('broadcasting.default')),
            locale:           @json(app()->getLocale())
        };
    </script>
    <script src="{{ asset('js/realtime-notifications.js') }}?v=8"></script>
    <script>
        // Sync sidebar theme with global theme and manage custom settings priority
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof KTThemeMode !== 'undefined') {
                var updateSidebarTheme = function() {
                    var theme = KTThemeMode.getMode();
                    var menuMode = KTThemeMode.getMenuMode(); // "light", "dark", or "system"
                    var body = document.getElementById('kt_app_body');
                    var customStyles = document.getElementById('custom-sidebar-settings');
                    
                    // If user explicitly chose Light or Dark, ignore custom DB settings
                    if (menuMode === 'light' || menuMode === 'dark') {
                        if (customStyles) customStyles.disabled = true;
                    } else {
                        // If user is on "System" (default), apply custom DB settings
                        if (customStyles) customStyles.disabled = false;
                    }

                    if (body) {
                        if (theme === 'light') {
                            body.setAttribute('data-kt-app-layout', 'light-sidebar');
                        } else {
                            body.setAttribute('data-kt-app-layout', 'dark-sidebar');
                        }
                    }
                };
                KTThemeMode.on("kt.thememode.change", updateSidebarTheme);
                // Initial check after a slight delay to ensure scripts are loaded
                setTimeout(updateSidebarTheme, 100);
            }
        });
    </script>
</body>

</html>
