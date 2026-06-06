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
                            <span class="fs-8 opacity-75 ps-3">{{ $total_notify_count ?? 0 }} تقارير جديدة</span></h3>
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

                        @if(($total_notify_count ?? 0) > 0)
                        <!--begin::Tab content-->
                        <div class="tab-content">
                            <!--begin::Tab panel: Alerts-->
                            <div class="tab-pane fade show active" id="kt_topbar_notifications_1" role="tabpanel">
                                <div class="scroll-y mh-325px my-5 px-8">
                                    {{-- Membership Requests --}}
                                    @foreach($notify_students ?? [] as $st)
                                    <div class="d-flex flex-stack py-4">
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-35px me-4">
                                                <span class="symbol-label bg-light-primary text-primary fw-bold">{{ mb_substr($st->name, 0, 1) }}</span>
                                            </div>
                                            <div class="mb-0 me-2">
                                                <a href="{{ route('dashboard.view.membership') }}" class="fs-6 text-gray-800 text-hover-info fw-bold">طلب عضوية: {{ $st->name }}</a>
                                                <div class="text-gray-400 fs-7">طالب جديد ينتظر التفعيل</div>
                                            </div>
                                        </div>
                                        <span class="badge badge-light fs-8 text-muted">{{ $st->created_at->diffForHumans() }}</span>
                                    </div>
                                    @endforeach

                                    {{-- Closed Classes --}}
                                    @foreach($notify_closed_clases ?? [] as $cl)
                                    <div class="d-flex flex-stack py-4">
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-35px me-4">
                                                <span class="symbol-label bg-light-danger">
                                                    <i class="ki-duotone ki-notification-on fs-2 text-danger"><span class="path1"></span><span class="path2"></span></i>
                                                </span>
                                            </div>
                                            <div class="mb-0 me-2">
                                                <a href="{{ route('closed_classes.view') }}" class="fs-6 text-gray-800 text-hover-info fw-bold">إغلاق مجموعة: {{ $cl->Groups->name ?? 'مجموعة' }}</a>
                                                <div class="text-gray-400 fs-7">قام المدرس <strong>{{ $cl->Teacher->name ?? 'غير معروف' }}</strong> بإغلاق المجموعة بتاريخ {{ $cl->closed_date }}</div>
                                            </div>
                                        </div>
                                        <span class="badge badge-light-danger fs-8">تنبيه</span>
                                    </div>
                                    @endforeach

                                    @if(count($notify_students ?? []) == 0 && count($notify_closed_clases ?? []) == 0)
                                    <div class="d-flex flex-column px-9 items-center justify-content-center py-10">
                                        <div class="text-center">
                                            <i class="bi bi-check2-circle fs-3x text-success mb-3"></i>
                                            <div class="fw-bold fs-6 text-gray-800">لا توجد تنبيهات</div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <!--end::Tab panel-->

                            <!--begin::Tab panel: Updates (Groups)-->
                            <div class="tab-pane fade" id="kt_topbar_notifications_2" role="tabpanel">
                                <div class="scroll-y mh-325px my-5 px-8">
                                    @foreach($notify_Groups ?? [] as $gp)
                                    <div class="d-flex flex-stack py-4">
                                        <div class="d-flex align-items-center me-2">
                                            <div class="symbol symbol-35px me-4">
                                                <span class="symbol-label bg-light-info">
                                                    <i class="ki-duotone ki-chart-line fs-2 text-info"><span class="path1"></span><span class="path2"></span></i>
                                                </span>
                                            </div>
                                            <div class="mb-0 me-2">
                                                <a href="#" class="fs-6 text-gray-800 text-hover-info fw-bold">{{ $gp->name }}</a>
                                                <div class="text-gray-400 fs-7">تقدم المجموعة: {{ $gp->progress }}%</div>
                                            </div>
                                        </div>
                                        <span class="badge badge-light-info fs-8">{{ $gp->progress }}%</span>
                                    </div>
                                    @endforeach

                                    @if(count($notify_Groups ?? []) == 0)
                                    <div class="d-flex flex-column px-9 items-center justify-content-center py-10">
                                        <div class="text-center">
                                            <div class="fw-bold fs-6 text-gray-800">لا توجد تحديثات للمجموعات</div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <!--end::Tab panel-->

                            <!--begin::Tab panel: Messages-->
                            <div class="tab-pane fade" id="kt_topbar_notifications_3" role="tabpanel">
                                <div class="scroll-y mh-325px my-5 px-8">
                                    {{-- Student messages --}}
                                    @foreach($notify_Students_Admin_Messages ?? [] as $ms)
                                    <div class="d-flex flex-stack py-4">
                                        <div class="d-flex align-items-center me-2">
                                            <div class="symbol symbol-35px me-4">
                                                <span class="symbol-label bg-light-warning">
                                                    <i class="ki-duotone ki-message-text-2 fs-2 text-warning"><span class="path1"></span><span class="path2"></span></i>
                                                </span>
                                            </div>
                                            <div class="mb-0 me-2">
                                                <a href="{{ route('students.messages') }}" class="fs-6 text-gray-800 text-hover-info fw-bold">{{ $ms->student->name ?? 'طالب' }}</a>
                                                <div class="text-gray-400 fs-7 text-truncate w-150px">{{ $ms->content }}</div>
                                            </div>
                                        </div>
                                        <span class="badge badge-light-warning fs-8">رسالة طالب</span>
                                    </div>
                                    @endforeach

                                    {{-- Teacher messages --}}
                                    @foreach($notify_Teachers_Admin_Messages ?? [] as $mt)
                                    <div class="d-flex flex-stack py-4">
                                        <div class="d-flex align-items-center me-2">
                                            <div class="symbol symbol-35px me-4">
                                                <span class="symbol-label bg-light-success">
                                                    <i class="ki-duotone ki-sms fs-2 text-success"><span class="path1"></span><span class="path2"></span></i>
                                                </span>
                                            </div>
                                            <div class="mb-0 me-2">
                                                <a href="{{ route('teachers.messages') }}" class="fs-6 text-gray-800 text-hover-info fw-bold">{{ $mt->teacher->name ?? 'معلم' }}</a>
                                                <div class="text-gray-400 fs-7 text-truncate w-150px">{{ $mt->content }}</div>
                                            </div>
                                        </div>
                                        <span class="badge badge-light-success fs-8">رسالة معلم</span>
                                    </div>
                                    @endforeach

                                    {{-- Contact Us messages --}}
                                    @foreach($notify_contacts ?? [] as $ct)
                                    <div class="d-flex flex-stack py-4">
                                        <div class="d-flex align-items-center me-2">
                                            <div class="symbol symbol-35px me-4">
                                                <span class="symbol-label bg-light-primary">
                                                    <i class="ki-duotone ki-sms fs-2 text-primary"><span class="path1"></span><span class="path2"></span></i>
                                                </span>
                                            </div>
                                            <div class="mb-0 me-2">
                                                <a href="{{ route('contacts.view') }}" class="fs-6 text-gray-800 text-hover-info fw-bold">{{ $ct->name ?? 'زائر' }}</a>
                                                <div class="text-gray-400 fs-7 text-truncate w-150px">{{ $ct->subject ?? 'رسالة اتصل بنا' }}</div>
                                            </div>
                                        </div>
                                        <span class="badge badge-light-primary fs-8">اتصل بنا</span>
                                    </div>
                                    @endforeach

                                    @if(count($notify_Students_Admin_Messages ?? []) == 0 && count($notify_Teachers_Admin_Messages ?? []) == 0 && count($notify_contacts ?? []) == 0)
                                    <div class="d-flex flex-column px-9 items-center justify-content-center py-10">
                                        <div class="text-center">
                                            <div class="fw-bold fs-6 text-gray-800">لا توجد رسائل جديدة</div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <!--end::Tab panel-->
                        </div>
                        <!--end::Tab content-->
                        @else
                            <div class="d-flex flex-column px-9 items-center justify-content-center py-10">
                                <div class="text-center">
                                    <img class="mw-100 mh-200px mb-5 theme-light-show" src="{{ asset('assets/media/illustrations/sketchy-1/1.png') }}" alt="Empty" />
                                    <img class="mw-100 mh-200px mb-5 theme-dark-show" src="{{ asset('assets/media/illustrations/sketchy-1/1-dark.png') }}" alt="Empty" />
                                    <div class="fw-bold fs-6 text-gray-800 text-hover-info">لا توجد إشعارات جديدة</div>
                                    <div class="text-gray-400 fs-7 mt-1">أنت مطلع على كل شيء حالياً</div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="py-3 text-center border-top">
                            <a href="{{ route('dashboard.view') }}" class="btn btn-color-gray-600 btn-active-color-primary">عرض الكل <i class="ki-duotone ki-arrow-right fs-5"><span class="path1"></span><span class="path2"></span></i></a>
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

