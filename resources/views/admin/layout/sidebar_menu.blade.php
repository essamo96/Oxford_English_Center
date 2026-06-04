<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px"
    data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
    <!--begin::Sidebar menu style overrides-->
    <style>
        #kt_app_sidebar_menu_wrapper .menu-title {
            font-size: 1.15rem !important; /* تكبير النص قليلاً */
            font-weight: 600 !important;
        }
        #kt_app_sidebar_menu_wrapper .menu-icon i {
            font-size: 1.75rem !important; /* تكبير الأيقونة لتكون أكبر من fs-1 */
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
            <img alt="Oxford" src="{{ url('assets/oxford/img/logo.png') }}"
                class="h-25px app-sidebar-logo-minimize" />
        </a>
        <div id="kt_app_sidebar_toggle"
            class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary body-bg h-30px w-30px position-absolute top-50 start-100 translate-middle rotate"
            data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
            data-kt-toggle-name="app-sidebar-minimize">
            <i class="ki-duotone ki-double-left fs-1 text-info rotate-180"><span class="path1"></span><span
                    class="path2"></span></i>
        </div>
    </div>
    <div class="app-sidebar-menu overflow-hidden flex-column-fluid">
        <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper hover-scroll-overlay-y my-5"
            data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto"
            data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
            data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">
            <div class="menu menu-column menu-rounded menu-sub-indention px-3" id="kt_app_sidebar_menu"
                data-kt-menu="true" data-kt-menu-expand="false">

                <div class="menu-item">
                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'dashboard' ? 'active' : '' }}"
                        href="{{ route('dashboard.view') }}">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-home fs-5 text-info">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                        </span>
                        <span class="menu-title">الرئيسية</span>
                    </a>
                </div>

                <div class="menu-item">
                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'calendar' ? 'active' : '' }}"
                        href="{{ route('calendar.view') }}">
                        <span class="menu-icon">
                        <i class="ki-duotone ki-calendar-8 fs-1 text-info">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        </i></span>
                        <span class="menu-title">التقويم الدراسي</span>
                    </a>
                </div>

                <div data-kt-menu-trigger="click"
                    class="menu-item {{ in_array(isset($active_menu) ? $active_menu : '', ['programs', 'groups', 'students', 'times', 'fees', 'evaluations', 'teachers']) ? 'here show' : '' }} menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon"><i class="ki-duotone ki-book-open fs-1 text-info"><span
                                    class="path1"></span><span class="path2"></span><span
                                    class="path3"></span><span class="path4"></span></i></span>
                        <span class="menu-title">ادارة الاكاديمية</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        @if (auth()->user()->can('admin.programs.view'))
                            <div class="menu-item">
                                <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'programs' ? 'active' : '' }}"
                                    href="{{ route('programs.view') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">البرامج</span>
                                </a>
                            </div>
                        @endif
                        @if (auth()->user()->can('admin.groups.view'))
                            <div class="menu-item">
                                <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'groups' ? 'active' : '' }}"
                                    href="{{ route('groups.view') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">المجموعات</span>
                                </a>
                            </div>
                        @endif
                        @if (auth()->user()->can('admin.teachers.view'))
                            <div class="menu-item">
                                <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'teachers' ? 'active' : '' }}"
                                    href="{{ route('teachers.view') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">المعلمون</span>
                                </a>
                            </div>
                        @endif
                        @if (auth()->user()->can('admin.students.view'))
                            <div class="menu-item">
                                <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'students' ? 'active' : '' }}"
                                    href="{{ route('students.view') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">الطلاب</span>
                                </a>
                            </div>
                        @endif
                        @if (auth()->user()->can('admin.times.view'))
                            <div class="menu-item">
                                <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'times' ? 'active' : '' }}"
                                    href="{{ route('times.view') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">المواعيد والاوقات</span>
                                </a>
                            </div>
                        @endif
                        @if (auth()->user()->can('admin.fees.view'))
                            <div class="menu-item">
                                <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'fees' ? 'active' : '' }}"
                                    href="{{ route('fees.view') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">الرسوم</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <div data-kt-menu-trigger="click"
                    class="menu-item {{ in_array(isset($active_menu) ? $active_menu : '', ['financial', 'financial_invoices', 'financial_expenses', 'financial_fees']) ? 'here show' : '' }} menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon"><i class="ki-duotone ki-financial-schedule fs-1 text-info"><span
                                    class="path1"></span><span class="path2"></span><span
                                    class="path3"></span><span class="path4"></span></i></span>
                        <span class="menu-title">الإدارة المالية</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        @if (auth()->user()->can('admin.financial.view') ||
                                auth()->user()->can('admin.financial.verify') ||
                                auth()->user()->can('admin.financial.refund'))
                            <div class="menu-item">
                                <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'financial' ? 'active' : '' }}"
                                    href="{{ route('admin.financial.pending') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">الطلبات المالية المعلقة</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'financial_invoices' ? 'active' : '' }}"
                                    href="{{ route('admin.financial.invoices') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">كشف حساب الطلاب</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'financial_expenses' ? 'active' : '' }}"
                                    href="{{ route('admin.financial.expenses') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">المصروفات</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'financial_fees' ? 'active' : '' }}"
                                    href="{{ route('admin.financial.fees') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">إعدادات أنواع الرسوم</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <div data-kt-menu-trigger="click"
                    class="menu-item {{ in_array(isset($active_menu) ? $active_menu : '', ['memberships', 'closed_classes', 'ask_update']) ? 'here show' : '' }} menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon"><i class="ki-duotone ki-time fs-1 text-info"><span
                                    class="path1"></span><span class="path2"></span><span class="path3"></span><span
                                    class="path4"></span></i></span>
                        <span class="menu-title">الطلبات العالقة</span>
                        @if (($count_disabled_students ?? 0) + ($Closed_Classes_count ?? 0) > 0)
                            <span class="menu-badge"><span
                                    class="badge badge-danger">{{ ($count_disabled_students ?? 0) + ($Closed_Classes_count ?? 0) }}</span></span>
                        @endif
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        @if (auth()->user()->can('admin.students.view') ||
                                auth()->user()->can('admin.students.add') ||
                                auth()->user()->can('admin.students.edit') ||
                                auth()->user()->can('admin.students.delete') ||
                                auth()->user()->can('admin.students.status'))
                            <div class="menu-item">
                                <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'memberships' ? 'active' : '' }}"
                                    href="{{ route('dashboard.view.membership') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">العضوية</span>
                                    @if (($count_disabled_students ?? 0) > 0)
                                        <span class="menu-badge"><span
                                                class="badge badge-danger">{{ $count_disabled_students }}</span></span>
                                    @endif
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'closed_classes' ? 'active' : '' }}"
                                    href="{{ route('closed_classes.view') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">المجموعات المنتهية</span>
                                    @if (($Closed_Classes_count ?? 0) > 0)
                                        <span class="menu-badge"><span
                                                class="badge badge-danger">{{ $Closed_Classes_count }}</span></span>
                                    @endif
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'ask_update' ? 'active' : '' }}"
                                    href="{{ route('ask_update.view') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">طلبات تحديث البيانات</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <div data-kt-menu-trigger="click"
                    class="menu-item {{ in_array(isset($active_menu) ? $active_menu : '', ['parents', 'payment_methods', 'placement_tests', 'relationships']) ? 'here show' : '' }} menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon"><i class="ki-duotone ki-briefcase fs-1 text-info"><span
                                     class="path1"></span><span class="path2"></span></i></span>
                        <span class="menu-title">ادارة التسجيل</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'placement_tests' ? 'active' : '' }}"
                                href="{{ route('placement_tests.view') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">اختبارات تحديد المستوى</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'parents' ? 'active' : '' }}"
                                href="{{ route('parents.view') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">أولياء الأمور</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'payment_methods' ? 'active' : '' }}"
                                href="{{ route('payment_methods.view') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">طرق الدفع</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'relationships' ? 'active' : '' }}"
                                href="{{ route('admin.relationships.index') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">صلات القرابة</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div data-kt-menu-trigger="click"
                    class="menu-item {{ in_array(isset($active_menu) ? $active_menu : '', ['messages_students', 'messages_teachers']) ? 'here show' : '' }} menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon"><i class="ki-duotone ki-notification-on fs-1 text-info"><span
                                    class="path1"></span><span class="path2"></span><span
                                    class="path3"></span><span class="path4"></span></i></span>
                        <span class="menu-title">ادارة الاشعارات</span>
                        @if (($total_teachers_students_measge ?? 0) > 0)
                            <span class="menu-badge"><span
                                    class="badge badge-danger">{{ $total_teachers_students_measge }}</span></span>
                        @endif
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        @if (auth()->user()->can('admin.students.view') ||
                                auth()->user()->can('admin.students.add') ||
                                auth()->user()->can('admin.students.edit') ||
                                auth()->user()->can('admin.students.delete') ||
                                auth()->user()->can('admin.students.status'))
                            <div class="menu-item">
                                <a class="menu-link" href="{{ route('students.messages') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">رسائل الطلاب</span>
                                    @if (($Unread_measges_student ?? 0) > 0)
                                        <span class="menu-badge"><span
                                                class="badge badge-danger">{{ $Unread_measges_student }}</span></span>
                                    @endif
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="{{ route('teachers.messages') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">رسائل المعلمين</span>
                                    @if (($Unread_measges_teacher ?? 0) > 0)
                                        <span class="menu-badge"><span
                                                class="badge badge-danger">{{ $Unread_measges_teacher }}</span></span>
                                    @endif
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                @if (auth()->user()->can('admin.email_campaigns.view'))
                    <div class="menu-item">
                        <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'email_campaigns' ? 'active' : '' }}"
                            href="{{ route('admin.email_campaigns.index') }}">
                            <span class="menu-icon"><i class="ki-duotone ki-sms fs-1 text-info"><span
                                        class="path1"></span><span class="path2"></span><span
                                        class="path3"></span><span class="path4"></span></i></span>
                            <span class="menu-title">حملات البريد</span>
                        </a>
                    </div>
                @endif

                <div data-kt-menu-trigger="click"
                    class="menu-item {{ in_array(isset($active_menu) ? $active_menu : '', ['evaluate_items', 'questions']) ? 'here show' : '' }} menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon"><i class="ki-duotone ki-medal-star fs-1 text-info"><span
                                    class="path1"></span><span class="path2"></span><span
                                    class="path3"></span><span class="path4"></span></i></span>
                        <span class="menu-title">ادارة التقيمات</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        @if (auth()->user()->can('admin.evaluate_items.view'))
                            <div class="menu-item">
                                <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'evaluate_items' ? 'active' : '' }}"
                                    href="{{ route('evaluate_items.view') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">اسئلة تقييم الطلاب</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'questions' ? 'active' : '' }}"
                                    href="{{ route('questions.view') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">اسئلة تقييم المعلمين</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                @if (auth()->user()->can('admin.news.view') ||
                        auth()->user()->can('admin.teacher_attendance.view') ||
                        auth()->user()->can('admin.teacher_attendance.edit'))
                    <div data-kt-menu-trigger="click"
                        class="menu-item {{ in_array(isset($active_menu) ? $active_menu : '', ['absent_teacher', 'teacher_salaries', 'attendance_settings']) ? 'here show' : '' }} menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon"><i class="ki-duotone ki-people fs-1 text-info"><span
                                        class="path1"></span><span class="path2"></span><span class="path3"></span><span
                                        class="path4"></span><span class="path5"></span></i></span>
                            <span class="menu-title">شؤون المدرّسين (HR)</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion">
                            @if (auth()->user()->can('admin.news.view'))
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'absent_teacher' ? 'active' : '' }}"
                                        href="{{ route('absent_teacher.view') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">سجل حضور المدرّسين</span>
                                    </a>
                                </div>
                            @endif
                            @if (auth()->user()->can('admin.teacher_attendance.view'))
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'teacher_salaries' ? 'active' : '' }}"
                                        href="{{ route('admin.teacher_salaries') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">استمارات رواتب المعلّمين</span>
                                    </a>
                                </div>
                            @endif
                            @if (auth()->user()->can('admin.teacher_attendance.edit'))
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'attendance_settings' ? 'active' : '' }}"
                                        href="{{ route('admin.attendance.settings') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">إعدادات الحضور والغياب</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- ============ إدارة الموقع الخارجي ============ --}}
                @if (auth()->user()->can('admin.news.view') ||
                        auth()->user()->can('admin.pages.view') ||
                        auth()->user()->can('admin.videos.view') ||
                        auth()->user()->can('admin.categories.view') ||
                        auth()->user()->can('admin.partners.view') ||
                        auth()->user()->can('admin.photos.view'))
                    <div data-kt-menu-trigger="click"
                        class="menu-item {{ in_array(isset($active_menu) ? $active_menu : '', ['news','pages','videos','categories','partners','photos','site_management']) ? 'here show' : '' }} menu-accordion">
                        <span class="menu-link">
                            <span class="menu-icon"><i class="ki-duotone ki-globe fs-1 text-success"><span
                                        class="path1"></span><span class="path2"></span></i></span>
                            <span class="menu-title">إدارة الموقع الخارجي</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion">
                            @if (auth()->user()->can('admin.news.view'))
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'news' ? 'active' : '' }}" href="{{ route('news.view') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">السلايدر والأخبار</span>
                                    </a>
                                </div>
                            @endif
                            @if (auth()->user()->can('admin.pages.view'))
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'pages' ? 'active' : '' }}" href="{{ route('pages.view') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">الصفحات الثابتة</span>
                                    </a>
                                </div>
                            @endif
                            @if (auth()->user()->can('admin.videos.view'))
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'videos' ? 'active' : '' }}" href="{{ route('videos.view') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">الفيديوهات</span>
                                    </a>
                                </div>
                            @endif
                            @if (auth()->user()->can('admin.categories.view'))
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'categories' ? 'active' : '' }}" href="{{ route('categories.view') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">التصنيفات</span>
                                    </a>
                                </div>
                            @endif
                            @if (auth()->user()->can('admin.partners.view'))
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'partners' ? 'active' : '' }}" href="{{ route('partners.view') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">الشركاء</span>
                                    </a>
                                </div>
                            @endif
                            @if (auth()->user()->can('admin.photos.view'))
                                <div class="menu-item">
                                    <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'photos' ? 'active' : '' }}" href="{{ route('photos.view') }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                        <span class="menu-title">الصور</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <div data-kt-menu-trigger="click"
                    class="menu-item {{ in_array(isset($active_menu) ? $active_menu : '', ['file_manager']) ? 'here show' : '' }} menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon"><i class="ki-duotone ki-picture fs-1 text-info"><span
                                    class="path1"></span><span class="path2"></span><span
                                    class="path3"></span><span class="path4"></span></i></span>
                        <span class="menu-title">ادارة الوسائط</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        @if (auth()->user()->can('admin.photos.view'))
                            <div class="menu-item">
                                <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'file_manager' ? 'active' : '' }}"
                                    href="{{ route('admin.file_manager') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">مدير الملفات</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <div data-kt-menu-trigger="click"
                    class="menu-item {{ in_array(isset($active_menu) ? $active_menu : '', ['settings', 'socials', 'users', 'roles']) ? 'here show' : '' }} menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon"><i class="ki-duotone ki-setting-2 fs-1 text-info"><span
                                    class="path1"></span><span class="path2"></span><span
                                    class="path3"></span><span class="path4"></span></i></span>
                        <span class="menu-title">ادارة الموقع</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        @can('admin.settings.view')
                            <div class="menu-item">
                                <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'settings' ? 'active' : '' }}"
                                    href="{{ route('settings.view') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">الإعدادات</span>
                                </a>
                            </div>
                        @endcan
                        @can('admin.social.view')
                            <div class="menu-item">
                                <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'socials' ? 'active' : '' }}"
                                    href="{{ route('socials.view') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">الشبكات الإجتماعية</span>
                                </a>
                            </div>
                        @endcan
                        @if (auth()->user()->can('admin.users.view'))
                            <div class="menu-item">
                                <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'users' ? 'active' : '' }}"
                                    href="{{ route('users.view') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">المستخدمين</span>
                                </a>
                            </div>
                        @endif
                        @if (auth()->user()->can('admin.roles.view'))
                            <div class="menu-item">
                                <a class="menu-link {{ (isset($active_menu) ? $active_menu : '') == 'roles' ? 'active' : '' }}"
                                    href="{{ route('roles.view') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">الصلاحيات</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>



                @if (auth()->user()->can('admin.students_report.view'))
                    <div class="menu-item">
                        <a class="menu-link {{ Route::currentRouteName() == 'students_report.view' ? 'active' : '' }}"
                            href="{{ route('students_report.view') }}">
                            <span class="menu-icon"><i class="ki-duotone ki-user-square fs-1 text-info"><span
                                        class="path1"></span><span class="path2"></span><span
                                        class="path3"></span><span class="path4"></span></i></span>
                            <span class="menu-title">استعلامات الطلاب</span>
                        </a>
                    </div>
                @endif

                @if (auth()->user()->can('admin.certificates.view'))
                    <div class="menu-item">
                        <a class="menu-link {{ Route::currentRouteName() == 'certificates.view' ? 'active' : '' }}"
                            href="{{ route('certificates.view') }}">
                            <span class="menu-icon"><i class="ki-duotone ki-shield-tick fs-1 text-info"><span
                                        class="path1"></span><span class="path2"></span><span
                                        class="path3"></span><span class="path4"></span></i></span>
                            <span class="menu-title">الشهادات</span>
                        </a>
                    </div>
                @endif

                @if (auth()->user()->can('admin.categories.view'))
                    <div class="menu-item">
                        <a class="menu-link {{ Route::currentRouteName() == 'students.delay.view' ? 'active' : '' }}"
                            href="{{ route('students.delay.view') }}">
                            <span class="menu-icon"><i class="ki-duotone ki-timer fs-1 text-info"><span
                                        class="path1"></span><span class="path2"></span><span
                                        class="path3"></span><span class="path4"></span></i></span>
                            <span class="menu-title">الطلاب المؤجلين</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link {{ Route::currentRouteName() == 'birthdayes.view' ? 'active' : '' }}"
                            href="{{ route('birthdayes.view') }}">
                            <span class="menu-icon"><i class="ki-duotone ki-gift fs-1 text-info"><span
                                        class="path1"></span><span class="path2"></span><span
                                        class="path3"></span><span class="path4"></span></i></span>
                            <span class="menu-title">اعياد الميلاد</span>
                            @if (($BirthdaysCount ?? 0) > 0)
                                <span class="menu-badge"><span
                                        class="badge badge-danger">{{ $BirthdaysCount }}</span></span>
                            @endif
                        </a>
                    </div>
                @endif

                @if (auth()->user()->can('admin.progress_menu.view'))
                    <div class="menu-item">
                        <a class="menu-link {{ Route::currentRouteName() == 'progress_menu.view' ? 'active' : '' }}"
                            href="{{ route('progress_menu.view') }}">
                            <span class="menu-icon"><i class="ki-duotone ki-graph-up fs-1 text-info"><span
                                        class="path1"></span><span class="path2"></span><span
                                        class="path3"></span><span class="path4"></span></i></span>
                            <span class="menu-title">مستوى التقدم</span>
                        </a>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
