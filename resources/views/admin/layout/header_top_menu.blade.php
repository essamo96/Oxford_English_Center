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
                        @if ($standalone_unread > 0)
                            <span class="bullet bullet-dot bg-success h-6px w-6px position-absolute translate-middle top-0 start-50 animation-blink combo-badge-dot"></span>
                            <span class="rt-bell-badge combo-badge-count">{{ $standalone_unread }}</span>
                        @endif
                    </div>
                    
                    <div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px" data-kt-menu="true">
                        <div class="d-flex flex-column bgi-no-repeat rounded-top" style="background-color: #0A2540;">
                            <h3 class="text-white fw-semibold px-9 mt-10 mb-6">طلبات كومبو
                            <span class="fs-8 opacity-75 ps-3 combo-badge-count">{{ $standalone_unread }} طلبات جديدة</span></h3>
                        </div>
                        <div class="scroll-y mh-325px my-5 px-8">
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
                                    <span class="text-muted">لا يوجد طلبات تسجيل جديدة</span>
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
                        <div class="menu-item px-3">
                            <div class="menu-content d-flex align-items-center px-3">
                                <div class="symbol symbol-50px me-5">
                                    <img alt="Logo" src="{{ url('assets/media/avatars/blank.png') }}" />
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="fw-bold d-flex align-items-center fs-5">
                                        {{ auth()->user()->name }}
                                    </div>
                                    <a href="#" class="fw-semibold text-muted text-hover-info fs-7">{{ auth()->user()->email }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="separator my-2"></div>
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

    const singleItems = document.querySelectorAll('.mark-single-combo-read');
    singleItems.forEach(item => {
        item.addEventListener('click', function(e) {
            const id = this.getAttribute('data-id');
            fetch(`{{ url('admin/standalone-registrations/mark-read') }}/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        });
    });
});
</script>
