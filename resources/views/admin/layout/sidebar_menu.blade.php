<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px"
    data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
    <!--begin::Sidebar menu style overrides-->
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
    <!--end::Sidebar menu style overrides-->

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

                {{-- ════════════════════════════════ الرئيسية ════════════════════════════════ --}}
                <div class="menu-item">
                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'dashboard' ? 'active' : '' }}"
                        href="{{ route('dashboard.view') }}">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-home fs-5 text-info">
                                <span class="path1"></span><span class="path2"></span>
                                <span class="path3"></span><span class="path4"></span>
                            </i>
                        </span>
                        <span class="menu-title">الرئيسية</span>
                    </a>
                </div>

                {{-- ════════════════════════════ التقويم الدراسي ══════════════════════════════ --}}
                @can('admin.calendar.view')
                    <div class="menu-item">
                        <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'calendar' ? 'active' : '' }}"
                            href="{{ route('calendar.view') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-calendar-8 fs-1 text-info">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">التقويم الدراسي</span>
                        </a>
                    </div>
                @endcan

                {{-- ════════════════════════════ ادارة الاكاديمية ═════════════════════════════ --}}
                @can('admin.academy_management.view')
                    <div data-kt-menu-trigger="click"
                        class="menu-item {{ in_array(isset($active_menu) ? $active_menu : '', ['programs','branches','groups','teachers','students','times','fees']) ? 'here show' : '' }} menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon"><i class="ki-duotone ki-book-open fs-1 text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i></span>
                            <span class="menu-title">ادارة الاكاديمية</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion">
                            @can('admin.programs.view')
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'programs' ? 'active' : '' }}" href="{{ route('programs.view') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">البرامج</span>
                                    </a>
                                </div>
                            @endcan
                            @can('admin.branches.view')
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'branches' ? 'active' : '' }}" href="{{ route('branches.view') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">الفروع</span>
                                    </a>
                                </div>
                            @endcan
                            @can('admin.groups.view')
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'groups' ? 'active' : '' }}" href="{{ route('groups.view') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">المجموعات</span>
                                    </a>
                                </div>
                            @endcan
                            @can('admin.teachers.view')
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'teachers' ? 'active' : '' }}" href="{{ route('teachers.view') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">المعلمون</span>
                                    </a>
                                </div>
                            @endcan
                            @can('admin.students.view')
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'students' ? 'active' : '' }}" href="{{ route('students.view') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">الطلاب</span>
                                    </a>
                                </div>
                            @endcan
                            @can('admin.times.view')
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'times' ? 'active' : '' }}" href="{{ route('times.view') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">المواعيد والاوقات</span>
                                    </a>
                                </div>
                            @endcan
                            @can('admin.fees.view')
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'fees' ? 'active' : '' }}" href="{{ route('fees.view') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">الرسوم</span>
                                    </a>
                                </div>
                            @endcan
                        </div>
                    </div>
                @endcan

                {{-- ════════════════════════════ الإدارة المالية ══════════════════════════════ --}}
                @can('admin.financial_management.view')
                    <div data-kt-menu-trigger="click"
                        class="menu-item {{ in_array(isset($active_menu) ? $active_menu : '', ['financial','financial_invoices','financial_expenses','financial_fees']) ? 'here show' : '' }} menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon"><i class="ki-duotone ki-financial-schedule fs-1 text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i></span>
                            <span class="menu-title">الإدارة المالية</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion">
                            @canany(['admin.financial.view', 'admin.financial.verify', 'admin.financial.refund'])
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'financial' ? 'active' : '' }}" href="{{ route('admin.financial.pending') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">الطلبات المالية المعلقة</span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'financial_invoices' ? 'active' : '' }}" href="{{ route('admin.financial.invoices') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">كشف حساب الطلاب</span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'financial_expenses' ? 'active' : '' }}" href="{{ route('admin.financial.expenses') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">المصروفات</span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'financial_fees' ? 'active' : '' }}" href="{{ route('admin.financial.fees') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">إعدادات أنواع الرسوم</span>
                                    </a>
                                </div>
                            @endcanany
                        </div>
                    </div>
                @endcan

                {{-- ════════════════════════════ الطلبات العالقة ══════════════════════════════ --}}
                @can('admin.pending_requests.view')
                    <div data-kt-menu-trigger="click"
                        class="menu-item {{ in_array(isset($active_menu) ? $active_menu : '', ['memberships','closed_classes','ask_update']) ? 'here show' : '' }} menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon"><i class="ki-duotone ki-time fs-1 text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i></span>
                            <span class="menu-title">الطلبات العالقة</span>
                            <span class="menu-badge" style="display: {{ (($count_disabled_students ?? 0) + ($Closed_Classes_count ?? 0) > 0) ? 'inline-block' : 'none' }};"><span
                                class="badge badge-danger" data-live-counter="pending_requests">{{ ($count_disabled_students ?? 0) + ($Closed_Classes_count ?? 0) }}</span></span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion">
                            @can('admin.memberships.view')
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'memberships' ? 'active' : '' }}" href="{{ route('dashboard.view.membership') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">العضوية</span>
                                        <span class="menu-badge" style="display: {{ ($count_disabled_students ?? 0) > 0 ? 'inline-block' : 'none' }};"><span
                                            class="badge badge-danger" data-live-counter="unread_bookings">{{ $count_disabled_students }}</span></span>
                                    </a>
                                </div>
                            @endcan
                            @can('admin.closed_classes.view')
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'closed_classes' ? 'active' : '' }}" href="{{ route('closed_classes.view') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">المجموعات المنتهية</span>
                                        <span class="menu-badge" style="display: {{ ($Closed_Classes_count ?? 0) > 0 ? 'inline-block' : 'none' }};"><span
                                            class="badge badge-danger" data-live-counter="closed_classes">{{ $Closed_Classes_count }}</span></span>
                                    </a>
                                </div>
                            @endcan
                            @can('admin.ask_update.view')
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'ask_update' ? 'active' : '' }}" href="{{ route('ask_update.view') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">طلبات تحديث البيانات</span>
                                    </a>
                                </div>
                            @endcan
                        </div>
                    </div>
                @endcan

                {{-- ════════════════════════════ ادارة التسجيل ═══════════════════════════════ --}}
                @can('admin.registration_management.view')
                    <div data-kt-menu-trigger="click"
                        class="menu-item {{ in_array(isset($active_menu) ? $active_menu : '', ['placement_tests','parents','payment_methods','relationships']) ? 'here show' : '' }} menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon"><i class="ki-duotone ki-briefcase fs-1 text-info"><span class="path1"></span><span class="path2"></span></i></span>
                            <span class="menu-title">ادارة التسجيل</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion">
                            @can('admin.placement_tests.view')
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'placement_tests' ? 'active' : '' }}" href="{{ route('placement_tests.view') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">اختبارات تحديد المستوى</span>
                                    </a>
                                </div>
                            @endcan
                            @can('admin.parents.view')
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'parents' ? 'active' : '' }}" href="{{ route('parents.view') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">أولياء الأمور</span>
                                    </a>
                                </div>
                            @endcan
                            @can('admin.payment_methods.view')
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'payment_methods' ? 'active' : '' }}" href="{{ route('payment_methods.view') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">طرق الدفع</span>
                                    </a>
                                </div>
                            @endcan
                            @can('admin.relationships.view')
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'relationships' ? 'active' : '' }}" href="{{ route('admin.relationships.index') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">صلات القرابة</span>
                                    </a>
                                </div>
                            @endcan
                        </div>
                    </div>
                @endcan

                {{-- ════════════════════════ ادارة جهات الاتصال ════════════════════════════ --}}
                @canany(['admin.contact.view', 'admin.contact.reply', 'admin.contact.delete', 'admin.contact.status'])
                    <div class="menu-item">
                        <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'contacts' ? 'active' : '' }}" href="{{ route('contacts.view') }}">
                            <span class="menu-icon"><i class="ki-duotone ki-sms fs-1 text-info"><span class="path1"></span><span class="path2"></span></i></span>
                            <span class="menu-title">ادارة جهات الاتصال</span>
                        </a>
                    </div>
                @endcanany

                {{-- ════════════════════════════ ادارة الاشعارات ═══════════════════════════ --}}
                @can('admin.notifications_management.view')
                    <div data-kt-menu-trigger="click"
                        class="menu-item {{ in_array(isset($active_menu) ? $active_menu : '', ['messages_students','messages_teachers']) ? 'here show' : '' }} menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon"><i class="ki-duotone ki-notification-on fs-1 text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i></span>
                            <span class="menu-title">ادارة الاشعارات</span>
                            <span class="menu-badge" style="display: {{ ($total_teachers_students_measge ?? 0) > 0 ? 'inline-block' : 'none' }};"><span
                                class="badge badge-danger" data-live-counter="total_messages">{{ $total_teachers_students_measge }}</span></span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion">
                            @can('admin.messages_students.view')
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'messages_students' ? 'active' : '' }}" href="{{ route('students.messages') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">رسائل الطلاب</span>
                                        <span class="menu-badge" style="display: {{ ($Unread_measges_student ?? 0) > 0 ? 'inline-block' : 'none' }};"><span
                                            class="badge badge-danger" data-live-counter="student_messages">{{ $Unread_measges_student }}</span></span>
                                    </a>
                                </div>
                            @endcan
                            @can('admin.messages_teachers.view')
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'messages_teachers' ? 'active' : '' }}" href="{{ route('teachers.messages') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">رسائل المعلمين</span>
                                        <span class="menu-badge" style="display: {{ ($Unread_measges_teacher ?? 0) > 0 ? 'inline-block' : 'none' }};"><span
                                            class="badge badge-danger" data-live-counter="teacher_messages">{{ $Unread_measges_teacher }}</span></span>
                                    </a>
                                </div>
                            @endcan
                        </div>
                    </div>
                @endcan

                {{-- ═════════════════════════════ حملات البريد ════════════════════════════ --}}
                @can('admin.email_campaigns.view')
                    <div class="menu-item">
                        <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'email_campaigns' ? 'active' : '' }}" href="{{ route('admin.email_campaigns.index') }}">
                            <span class="menu-icon"><i class="ki-duotone ki-sms fs-1 text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i></span>
                            <span class="menu-title">حملات البريد</span>
                        </a>
                    </div>
                @endcan

                {{-- ════════════════════════════ ادارة التقيمات ═══════════════════════════ --}}
                @can('admin.evaluations_management.view')
                    <div data-kt-menu-trigger="click"
                        class="menu-item {{ in_array(isset($active_menu) ? $active_menu : '', ['evaluate_items','questions']) ? 'here show' : '' }} menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon"><i class="ki-duotone ki-medal-star fs-1 text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i></span>
                            <span class="menu-title">ادارة التقيمات</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion">
                            @can('admin.evaluate_items.view')
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'evaluate_items' ? 'active' : '' }}" href="{{ route('evaluate_items.view') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">اسئلة تقييم الطلاب</span>
                                    </a>
                                </div>
                            @endcan
                            @can('admin.questions.view')
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'questions' ? 'active' : '' }}" href="{{ route('questions.view') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">اسئلة تقييم المعلمين</span>
                                    </a>
                                </div>
                            @endcan
                        </div>
                    </div>
                @endcan

                {{-- ══════════════════════════ شؤون المدرّسين (HR) ════════════════════════ --}}
                @can('admin.hr_teachers.view')
                    <div data-kt-menu-trigger="click"
                        class="menu-item {{ in_array(isset($active_menu) ? $active_menu : '', ['absent_teacher','teacher_salaries','attendance_settings']) ? 'here show' : '' }} menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon"><i class="ki-duotone ki-people fs-1 text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i></span>
                            <span class="menu-title">شؤون المدرّسين (HR)</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion">
                            @can('admin.absent_teacher.view')
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'absent_teacher' ? 'active' : '' }}" href="{{ route('absent_teacher.view') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">سجل حضور المدرّسين</span>
                                    </a>
                                </div>
                            @endcan
                            @can('admin.teacher_salaries.view')
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'teacher_salaries' ? 'active' : '' }}" href="{{ route('admin.teacher_salaries') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">استمارات رواتب المعلّمين</span>
                                    </a>
                                </div>
                            @endcan
                            @can('admin.teacher_attendance.view')
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'attendance_settings' ? 'active' : '' }}" href="{{ route('admin.attendance.settings') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">إعدادات الحضور والغياب</span>
                                    </a>
                                </div>
                            @endcan
                        </div>
                    </div>
                @endcan

                {{-- ══════════════════════════ إدارة الموقع الخارجي ════════════════════════ --}}
                @can('admin.site_management.view')
                    <div data-kt-menu-trigger="click"
                        class="menu-item {{ in_array(isset($active_menu) ? $active_menu : '', ['news','pages','videos','categories','partners','photos']) ? 'here show' : '' }} menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-mouse-square fs-1 text-success">
                                    <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                                    <span class="path4"></span><span class="path5"></span><span class="path6"></span>
                                </i>
                            </span>
                            <span class="menu-title">إدارة الموقع الخارجي</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion">
                            @can('admin.news.view')
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'news' ? 'active' : '' }}" href="{{ route('news.view') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">السلايدر والأخبار</span>
                                    </a>
                                </div>
                            @endcan
                            @can('admin.pages.view')
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'pages' ? 'active' : '' }}" href="{{ route('pages.view') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">الصفحات الثابتة</span>
                                    </a>
                                </div>
                            @endcan
                            @can('admin.videos.view')
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'videos' ? 'active' : '' }}" href="{{ route('videos.view') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">الفيديوهات</span>
                                    </a>
                                </div>
                            @endcan
                            @can('admin.categories.view')
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'categories' ? 'active' : '' }}" href="{{ route('categories.view') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">التصنيفات</span>
                                    </a>
                                </div>
                            @endcan
                            @can('admin.partners.view')
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'partners' ? 'active' : '' }}" href="{{ route('partners.view') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">الشركاء</span>
                                    </a>
                                </div>
                            @endcan
                            @can('admin.photos.view')
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'photos' ? 'active' : '' }}" href="{{ route('photos.view') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">الصور</span>
                                    </a>
                                </div>
                            @endcan
                        </div>
                    </div>
                @endcan

                {{-- ════════════════════════════ ادارة الوسائط ════════════════════════════ --}}
                @can('admin.media_management.view')
                    <div data-kt-menu-trigger="click"
                        class="menu-item {{ in_array(isset($active_menu) ? $active_menu : '', ['file_manager']) ? 'here show' : '' }} menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon"><i class="ki-duotone ki-picture fs-1 text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i></span>
                            <span class="menu-title">ادارة الوسائط</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion">
                            @can('admin.file_manager.view')
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'file_manager' ? 'active' : '' }}" href="{{ route('admin.file_manager') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">مدير الملفات</span>
                                    </a>
                                </div>
                            @endcan
                        </div>
                    </div>
                @endcan

                {{-- ════════════════════════════ ادارة الموقع ═════════════════════════════ --}}
                @can('admin.site_admin.view')
                    <div data-kt-menu-trigger="click"
                        class="menu-item {{ in_array(isset($active_menu) ? $active_menu : '', ['settings','socials','users','roles']) ? 'here show' : '' }} menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon"><i class="ki-duotone ki-setting-2 fs-1 text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i></span>
                            <span class="menu-title">ادارة الموقع</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion">
                            @can('admin.settings.view')
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'settings' ? 'active' : '' }}" href="{{ route('settings.view') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">الإعدادات</span>
                                    </a>
                                </div>
                            @endcan
                            @can('admin.social.view')
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'socials' ? 'active' : '' }}" href="{{ route('socials.view') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">الشبكات الإجتماعية</span>
                                    </a>
                                </div>
                            @endcan
                            @can('admin.users.view')
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'users' ? 'active' : '' }}" href="{{ route('users.view') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">المستخدمين</span>
                                    </a>
                                </div>
                            @endcan
                            @can('admin.roles.view')
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'roles' ? 'active' : '' }}" href="{{ route('roles.view') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">الصلاحيات</span>
                                    </a>
                                </div>
                            @endcan
                        </div>
                    </div>
                @endcan

                {{-- ═══════════════════════════ استعلامات الطلاب ══════════════════════════ --}}
                @can('admin.students_report.view')
                    <div class="menu-item">
                        <a class="menu-link {{ Route::currentRouteName() == 'students_report.view' ? 'active' : '' }}" href="{{ route('students_report.view') }}">
                            <span class="menu-icon"><i class="ki-duotone ki-user-square fs-1 text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i></span>
                            <span class="menu-title">استعلامات الطلاب</span>
                        </a>
                    </div>
                @endcan

                {{-- ══════════════════════════════ الشهادات ═══════════════════════════════ --}}
                @can('admin.certificates.view')
                    <div class="menu-item">
                        <a class="menu-link {{ Route::currentRouteName() == 'certificates.view' ? 'active' : '' }}" href="{{ route('certificates.view') }}">
                            <span class="menu-icon"><i class="ki-duotone ki-shield-tick fs-1 text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i></span>
                            <span class="menu-title">الشهادات</span>
                        </a>
                    </div>
                @endcan

                {{-- ══════════════════════════ الطلاب المؤجلين ════════════════════════════ --}}
                @can('admin.delayed_students.view')
                    <div class="menu-item">
                        <a class="menu-link {{ Route::currentRouteName() == 'students.delay.view' ? 'active' : '' }}" href="{{ route('students.delay.view') }}">
                            <span class="menu-icon"><i class="ki-duotone ki-timer fs-1 text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i></span>
                            <span class="menu-title">الطلاب المؤجلين</span>
                        </a>
                    </div>
                @endcan

                {{-- ═══════════════════════════ أعياد الميلاد ════════════════════════════ --}}
                @can('admin.birthdayes.view')
                    <div class="menu-item">
                        <a class="menu-link {{ Route::currentRouteName() == 'birthdayes.view' ? 'active' : '' }}" href="{{ route('birthdayes.view') }}">
                            <span class="menu-icon"><i class="ki-duotone ki-gift fs-1 text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i></span>
                            <span class="menu-title">اعياد الميلاد</span>
                            @if (($BirthdaysCount ?? 0) > 0)
                                <span class="menu-badge"><span class="badge badge-danger">{{ $BirthdaysCount }}</span></span>
                            @endif
                        </a>
                    </div>
                @endcan

                {{-- ══════════════════════════ مستوى التقدم ═══════════════════════════════ --}}
                @can('admin.progress_menu.view')
                    <div class="menu-item">
                        <a class="menu-link {{ Route::currentRouteName() == 'progress_menu.view' ? 'active' : '' }}" href="{{ route('progress_menu.view') }}">
                            <span class="menu-icon"><i class="ki-duotone ki-graph-up fs-1 text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i></span>
                            <span class="menu-title">مستوى التقدم</span>
                        </a>
                    </div>
                @endcan

            </div>
        </div>
    </div>
</div>
