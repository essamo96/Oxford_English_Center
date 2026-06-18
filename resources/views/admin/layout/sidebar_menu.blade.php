<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px"
    data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">

    <style>
        #kt_app_sidebar_menu_wrapper .menu-title {
            font-size: 1.15rem !important;
            font-weight: 600 !important;
        }
        #kt_app_sidebar_menu_wrapper .menu-icon i {
            font-size: 1.75rem !important;
        }
        #kt_app_sidebar_menu_wrapper .menu-bullet .bullet {
            width: 6px !important;
            height: 6px !important;
        }
    </style>

    <div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
        <a href="{{ route('dashboard.view') }}">
            <img alt="Oxford" src="{{ url('assets/oxford/img/logo.png') }}" class="h-40px app-sidebar-logo-default" />
            <img alt="Oxford" src="{{ url('assets/oxford/img/logo.png') }}" class="h-25px app-sidebar-logo-minimize" />
        </a>
        <div id="kt_app_sidebar_toggle"
            class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary body-bg h-30px w-30px position-absolute top-50 start-100 translate-middle rotate"
            data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
            data-kt-toggle-name="app-sidebar-minimize">
            <i class="ki-duotone ki-double-left fs-1 text-info rotate-180"><span class="path1"></span><span class="path2"></span></i>
        </div>
    </div>

    <div class="app-sidebar-menu overflow-hidden flex-column-fluid">
        <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper hover-scroll-overlay-y my-5"
            data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto"
            data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
            data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">
            <div class="menu menu-column menu-rounded menu-sub-indention px-3" id="kt_app_sidebar_menu"
                data-kt-menu="true" data-kt-menu-expand="false">

                {{-- ── الرئيسية (ثابتة دائماً، لونها من DB) ──────────────────── --}}
                @php $dashColor = $dashboardGroup->color ?? '#009ef7'; @endphp
                <div class="menu-item">
                    <a class="menu-link {{ ($active_menu ?? '') == 'dashboard' ? 'active' : '' }}"
                        href="{{ route('dashboard.view') }}">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-home fs-1" style="color: {{ $dashColor }}">
                                <span class="path1"></span><span class="path2"></span>
                                <span class="path3"></span><span class="path4"></span>
                            </i>
                        </span>
                        <span class="menu-title">الرئيسية</span>
                    </a>
                </div>

                {{-- ── القوائم الديناميكية من DB حسب الصلاحيات ─────────────────── --}}
                @foreach($sidebar ?? [] as $item)
                    @if($item->mychild->count() > 0)
                        @include('admin.components.sidebar-item-with-children', [
                            'item'        => $item,
                            'active_menu' => $active_menu ?? '',
                        ])
                    @else
                        @include('admin.components.sidebar-item-single', [
                            'item'        => $item,
                            'active_menu' => $active_menu ?? '',
                        ])
                    @endif
                @endforeach

                {{-- ── إدارة خدمة tweetSMS (ثابتة) ──────────────────── --}}
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ in_array($active_menu ?? '', ['sms_log', 'sms_archive']) ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-sms fs-1" style="color: #009ef7">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                        </span>
                        <span class="menu-title">إدارة خدمة tweetSMS</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <!--
                        <div class="menu-item">
                            <a class="menu-link {{ ($active_menu ?? '') == 'sms_log' ? 'active' : '' }}" href="#">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">LOG SMS</span>
                            </a>
                        </div>
                        -->
                        <div class="menu-item">
                            <a class="menu-link {{ ($active_menu ?? '') == 'sms_archive' ? 'active' : '' }}" href="{{ route('admin.sms.archive') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">أرشيف الرسائل</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
