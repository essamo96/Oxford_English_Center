<!--begin::Header-->
<div id="kt_app_header" class="app-header">
    <div class="app-container container-fluid d-flex align-items-stretch justify-content-between" id="kt_app_header_container">
        <!--begin::Sidebar mobile toggle-->
        <div class="d-flex align-items-center d-lg-none ms-n3 me-1 me-md-2" title="Show sidebar menu">
            <div class="btn btn-icon btn-active-color-primary w-35px h-35px" id="kt_app_sidebar_mobile_toggle">
                <i class="ki-duotone ki-abstract-14 fs-2 fs-md-1">
                    <span class="path1"></span><span class="path2"></span>
                </i>
            </div>
        </div>
        <!--end::Sidebar mobile toggle-->
        <!--begin::Mobile logo-->
        <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
            <a href="{{ url('/admin') }}" class="d-lg-none">
                <img alt="Logo" src="{{ url('assets/oxford/img/logo.png') }}" class="h-30px" />
            </a>
        </div>
        <!--end::Mobile logo-->
        <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1" id="kt_app_header_wrapper">
            <div class="app-navbar flex-shrink-0 align-items-center gap-4 ms-auto">
                <div class="app-navbar-item ms-1 ms-md-3">
                    <a href="#" class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-30px h-30px w-md-40px h-md-40px" data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                        <i class="ki-duotone ki-night-day theme-light-show fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span></i>
                        <i class="ki-duotone ki-moon theme-dark-show fs-2"><span class="path1"></span><span class="path2"></span></i>
                    </a>
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-muted menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px" data-kt-menu="true" data-kt-element="theme-mode-menu">
                        <div class="menu-item px-3 my-0">
                            <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="light">
                                <span class="menu-icon" data-kt-element="icon"><i class="ki-duotone ki-night-day fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span></i></span>
                                <span class="menu-title">Light</span>
                            </a>
                        </div>
                        <div class="menu-item px-3 my-0">
                            <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="dark">
                                <span class="menu-icon" data-kt-element="icon"><i class="ki-duotone ki-moon fs-2"><span class="path1"></span><span class="path2"></span></i></span>
                                <span class="menu-title">Dark</span>
                            </a>
                        </div>
                    </div>
                </div>
                <!--begin::SMS Shuttle Button-->
                <div class="app-navbar-item ms-1 ms-md-3">
                    <button type="button" class="btn btn-sm btn-light-primary fw-bold px-4 d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#smsShuttleModal">
                        <i class="fa fa-envelope fs-5 me-1"></i>
                        رسائل SMS
                    </button>
                </div>
                <!--end::SMS Shuttle Button-->
                <!--begin::Branch Indicator-->
                @if(isset($isBranchScoped) && $isBranchScoped && isset($activeBranch) && $activeBranch)
                <div class="app-navbar-item ms-1 ms-md-3">
                    <span class="badge badge-light-info fs-7 fw-bold px-4 py-2 d-flex align-items-center gap-2">
                        <i class="ki-duotone ki-geolocation fs-5 text-info"><span class="path1"></span><span class="path2"></span></i>
                        {{ $activeBranch->name_ar }}
                    </span>
                </div>
                @elseif(!isset($isBranchScoped) || !$isBranchScoped)
                <div class="app-navbar-item ms-1 ms-md-3">
                    <span class="badge badge-light-success fs-7 fw-bold px-4 py-2 d-flex align-items-center gap-2" title="تعرض جميع الفروع">
                        <i class="ki-duotone ki-geolocation fs-5 text-success"><span class="path1"></span><span class="path2"></span></i>
                        كل الفروع
                    </span>
                </div>
                @endif
                <!--end::Branch Indicator-->

                <!--begin::Combo Requests Notifications-->
                @php
                    $standalone_unread = \App\Models\StudentCompo::where('is_read', false)->count();
                    $standalone_recent = \App\Models\StudentCompo::where('is_read', false)->orderBy('created_at', 'desc')->take(5)->get();
                @endphp
                <div class="app-navbar-item ms-1 ms-md-3">
                    <div class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-30px h-30px w-md-40px h-md-40px position-relative" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                        <i class="ki-duotone ki-user-edit fs-2">
                            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                        </i>
                        <span class="bullet bullet-dot bg-success h-6px w-6px position-absolute translate-middle top-0 start-50 animation-blink combo-badge-dot" style="{{ $standalone_unread > 0 ? '' : 'display:none;' }}"></span>
                        <span class="rt-bell-badge combo-badge-count" data-live-counter="unread_combo_registrations" style="{{ $standalone_unread > 0 ? '' : 'display:none;' }}">{{ $standalone_unread }}</span>
                    </div>
                    
                    <div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px" data-kt-menu="true">
                        <div class="d-flex flex-column bgi-no-repeat rounded-top" style="background-image:url('{{ asset('assets/media/misc/menu-header-bg.jpg') }}')">
                            <h3 class="text-white fw-semibold px-9 mt-10 mb-2">الإشعارات</h3>
                            <h4 class="text-white fw-semibold px-9 mb-6 fs-5">طلبات كومبو
                            <span class="fs-8 opacity-75 ps-3 combo-badge-count"><span data-live-counter="unread_combo_registrations_title">{{ $standalone_unread }}</span> طلبات جديدة</span></h4>
                        </div>
                        <div class="scroll-y mh-325px my-5 px-8" id="combo-notifications-list">
                            @if($standalone_unread > 0)
                                <div class="text-end mb-2">
                                    <a href="javascript:;" class="mark-all-combo-read fs-8 fw-bold text-primary">تحديد الكل كمقروء</a>
                                </div>
                            @endif
                            @forelse ($standalone_recent as $item)
                                <div class="d-flex flex-stack py-4" id="combo_notif_item_{{ $item->id }}">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-35px me-4">
                                            <span class="symbol-label bg-light-success">
                                                <i class="ki-duotone ki-user fs-2 text-success"><span class="path1"></span><span class="path2"></span></i>
                                            </span>
                                        </div>
                                        <div class="mb-0 me-2">
                                            <a href="{{ route('standalone_registrations.view') }}" class="fs-6 text-gray-800 text-hover-primary fw-bold mark-single-combo-read" data-id="{{ $item->id }}">
                                                {{ $item->full_name_ar }}
                                            </a>
                                            <div class="text-gray-500 fs-7">{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <img src="{{ url('assets/media/illustrations/sketchy-1/17.png') }}" alt="Empty" class="w-100px mb-3 theme-light-show" />
                                    <img src="{{ url('assets/media/illustrations/sketchy-1/17-dark.png') }}" alt="Empty" class="w-100px mb-3 theme-dark-show" />
                                    <br>
                                    <span class="text-muted fw-bold">لا يوجد طلبات تسجيل جديدة</span>
                                </div>
                            @endforelse
                        </div>
                        <div class="py-3 text-center border-top">
                            <a href="{{ route('standalone_registrations.view') }}" class="btn btn-color-gray-600 btn-active-color-primary">عرض كل الطلبات <i class="ki-duotone ki-arrow-left fs-5"><span class="path1"></span><span class="path2"></span></i></a>
                        </div>
                    </div>
                </div>
                <!--end::Combo Requests Notifications-->

                <!--begin::Notifications-->
                <div class="app-navbar-item ms-1 ms-md-3">
                    <div class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-30px h-30px w-md-40px h-md-40px position-relative" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                        <i class="ki-duotone ki-notification-on fs-2">
                            <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span>
                        </i>
                        @if(($total_notify_count ?? 0) > 0)
                            <span class="bullet bullet-dot bg-danger h-6px w-6px position-absolute translate-middle top-0 start-50 animation-blink"></span>
                        @endif
                        {{-- Live numeric badge — updated in real time by realtime-notifications.js --}}
                        <span class="rt-bell-badge" data-counter="notify-total"
                              style="{{ ($total_notify_count ?? 0) > 0 ? '' : 'display:none;' }}">{{ $total_notify_count ?? 0 }}</span>
                    </div>
                    
                    <div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px" data-kt-menu="true">
                        <!--begin::Heading-->
                        <div class="d-flex flex-column bgi-no-repeat rounded-top" style="background-image:url('{{ asset('assets/media/misc/menu-header-bg.jpg') }}')">
                            <!--begin::Title-->
                            <h3 class="text-white fw-semibold px-9 mt-10 mb-6">الإشعارات
                            <span class="fs-8 opacity-75 ps-3" id="rt-notif-title-count">{{ $total_notify_count ?? 0 }} تقارير جديدة</span></h3>
                            <!--end::Title-->
                            <!--begin::Tabs-->
                            <ul class="nav nav-line-tabs nav-line-tabs-2x nav-stretch fw-semibold px-9" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link text-white opacity-75 opacity-state-100 pb-4 active" data-bs-toggle="tab" href="#kt_topbar_notifications_1" aria-selected="true" role="tab">تنبيهات</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link text-white opacity-75 opacity-state-100 pb-4" data-bs-toggle="tab" href="#kt_topbar_notifications_2" aria-selected="false" role="tab">المجموعات</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link text-white opacity-75 opacity-state-100 pb-4" data-bs-toggle="tab" href="#kt_topbar_notifications_3" aria-selected="false" role="tab">الرسائل</a>
                                </li>
                            </ul>
                            <!--end::Tabs-->
                        </div>
                        <!--end::Heading-->
                        {{-- Dynamic content — refreshed via AJAX by realtime-notifications.js --}}
                        <div id="rt-notif-body">
                            @include('admin.layout.partials.notifications_dropdown')
                        </div>
                    </div>
                </div>
                <!--end::Notifications-->

                <div class="app-navbar-item ms-1 ms-md-3" id="kt_header_user_menu_toggle">
                    <div class="cursor-pointer symbol symbol-30px symbol-md-40px" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                        <img src="{{ url('assets/media/avatars/blank.png') }}" alt="user" />
                    </div>
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px" data-kt-menu="true">
                        <div class="menu-item px-3 mt-3">
                            <div class="menu-content d-flex align-items-center px-3 py-4 bgi-no-repeat bgi-position-center bgi-size-cover rounded" style="background-image:url('{{ asset('assets/media/misc/menu-header-bg.jpg') }}')">
                                <div class="symbol symbol-50px me-5">
                                    <img alt="Logo" src="{{ url('assets/media/avatars/blank.png') }}" />
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="fw-bold d-flex align-items-center fs-5 text-white">
                                        {{ auth()->user()->name }}
                                    </div>
                                    <a href="#" class="fw-semibold text-white opacity-75 text-hover-primary fs-7">{{ auth()->user()->email }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="separator my-2"></div>
                        <div class="menu-item px-5">
                            <a href="{{ route('admin.profile.index') }}" class="menu-link px-5">إعدادات الملف الشخصي</a>
                        </div>
                        <div class="menu-item px-5">
                            <a href="{{ route('app.logout') }}" class="menu-link px-5">تسجيل الخروج</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Header-->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const markAllBtn = document.querySelector('.mark-all-combo-read');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            fetch("{{ route('admin.standalone_registrations.markAllAsRead') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(r => r.json()).then(res => {
                if(res.success) {
                    document.querySelectorAll('.combo-badge-count').forEach(el => {
                        if (el.tagName === 'SPAN' && el.classList.contains('rt-bell-badge')) {
                            el.style.display = 'none';
                        } else if (el.tagName === 'SPAN') {
                            el.innerText = '0 طلبات جديدة';
                        }
                    });
                    document.querySelectorAll('.combo-badge-dot').forEach(el => el.remove());
                    document.querySelectorAll('[id^="combo_notif_item_"]').forEach(el => el.remove());
                }
            });
        });
    }

    document.addEventListener('click', function(e) {
        const item = e.target.closest('.mark-single-combo-read');
        if(item) {
            const id = item.getAttribute('data-id');
            fetch(`{{ url('admin/new-registrations/mark-read') }}/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        }
    });
});
</script>

<style>
/* ACG Scanner Wrapper to prevent scroll */
.acg-scanner-wrapper {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100px;
    overflow: hidden;
    pointer-events: none;
    z-index: 1000;
}

/* ACG Scanner Beam */
.acg-scanner-beam {
    position: absolute;
    top: 0;
    right: -200px;
    width: 200px;
    height: 3px;
    background: linear-gradient(to left, transparent, #268aff, #268aff, transparent);
    box-shadow: 0 0 15px #268aff, 0 0 5px #268aff;
    border-radius: 50%;
    display: none; /* Initially hidden to prevent scroll */
}

/* Icon Pulse Animation */
@keyframes acgIconPulse {
    0% { transform: scale(1); filter: drop-shadow(0 0 0 rgba(38, 138, 255, 0)); }
    50% { transform: scale(1.2); filter: drop-shadow(0 0 10px rgba(38, 138, 255, 1)); color: #268aff !important; }
    100% { transform: scale(1); filter: drop-shadow(0 0 0 rgba(38, 138, 255, 0)); }
}

.acg-icon-pulsing {
    animation: acgIconPulse 0.6s ease-out;
}
.acg-icon-pulsing.ki-duotone i,
.acg-icon-pulsing.ki-duotone span {
    color: #268aff !important;
}
</style>

<div class="acg-scanner-wrapper">
    <div id="acg_scanner" class="acg-scanner-beam"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const scanner = document.getElementById('acg_scanner');
    // Select the icons within the top bar AND the sidebar logo/name
    const icons = document.querySelectorAll('#kt_app_header_wrapper .ki-duotone, #kt_app_header_wrapper img, #kt_app_sidebar_logo img, #kt_app_sidebar_logo .oxford-sidebar-text');
    
    let currentRight = -200;
    const speed = 4; // speed in pixels per frame
    
    // Find the leftmost bound of all icons to know when to stop/hide the scanner
    let minLeft = window.innerWidth;
    icons.forEach(icon => {
        const rect = icon.getBoundingClientRect();
        if (rect.left < minLeft && rect.left > 0) {
            minLeft = rect.left;
        }
    });

    scanner.style.display = 'block';

    function animateScanner() {
        currentRight += speed;
        scanner.style.right = currentRight + 'px';
        
        const scannerRect = scanner.getBoundingClientRect();
        const scannerCenter = scannerRect.left + (scannerRect.width / 2);
        
        // Reset when it goes past the leftmost icon (the user profile)
        if (scannerRect.right < minLeft - 50) {
            currentRight = -200;
            // Add a small pause before it restarts
            setTimeout(() => {
                requestAnimationFrame(animateScanner);
            }, 2000);
            return;
        }
        
        // Check intersections with icons
        icons.forEach(icon => {
            const rect = icon.getBoundingClientRect();
            if (scannerCenter >= rect.left && scannerCenter <= rect.right) {
                if (!icon.classList.contains('acg-icon-pulsing')) {
                    icon.classList.add('acg-icon-pulsing');
                    setTimeout(() => {
                        icon.classList.remove('acg-icon-pulsing');
                    }, 600);
                }
            }
        });
        
        requestAnimationFrame(animateScanner);
    }
    
    // Initial delay
    setTimeout(() => {
        requestAnimationFrame(animateScanner);
    }, 1000);
});
</script>
