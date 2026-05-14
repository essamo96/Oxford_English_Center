<div class="page-sidebar-wrapper">
    <div class="page-sidebar navbar-collapse collapse">
        <ul class="page-sidebar-menu  page-header-fixed page-sidebar-menu-hover-submenu " data-keep-expanded="false"
            data-auto-scroll="true" data-slide-speed="200">
            <li class="nav-item {{ $active_menu == 'dashboard' ? 'active' : '' }}">
                <a href="{{ route('dashboard.view') }}" class="nav-link nav-toggle">
                    <i class="icon-home"></i>
                    <span class="title">الرئيسية</span>
                    <span class="arrow"></span>
                </a>
            </li>
            <li class="nav-item {{ $active_menu == 'memberships' ? 'active' : '' }}">
                <a href="javascript:void(0);" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">الطلبات العالقة </span>
                    <span class="icon-button__badge"
                          style="
                          top: -57px;
                          position: relative;
                          align-items: center;
                          width: 26px;
                          height: 26px;
                          left: -107px;
                          background: #fd6f69;
                          border: none;
                          outline: none;
                          border-radius: 50%;
                          justify-content: center;
                          display: flex;
                          color: white;">
                        {{ $count_disabled_students + $Closed_Classes_count ? $count_disabled_students + $Closed_Classes_count: 0 }}
                    </span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">


                    @if (auth()->user()->can('admin.students.view') ||
                    auth()->user()->can('admin.students.add') ||
                    auth()->user()->can('admin.students.edit') ||
                    auth()->user()->can('admin.students.delete') ||
                    auth()->user()->can('admin.students.status'))
                    <li class="nav-item {{ $active_menu == 'memberships' ? 'active' : '' }}">
                        <a href="{{ route('dashboard.view.membership') }}" class="nav-link nav-toggle">
                            <i class="icon-settings"></i>
                            <span class="title">العضوية </span>
                            <span class="arrow"></span>
                            <span class="icon-button__badge"
                                  style="
                                  top: -5px;
                                  position: relative;
                                  align-items: center;
                                  justify-content: center;
                                  width: 26px;
                                  height: 21px;
                                  left:-11px;
                                  background: #ec7070;
                                  border: none;
                                  outline: none;
                                  border-radius: 50%;">
                                {{ $count_disabled_students ? $count_disabled_students : 0 }}
                            </span>
                        </a>
                    </li>
                    <li class="nav-item {{ $active_menu == 'closed_classes' ? 'active' : '' }}">
                        <a href="{{ route('closed_classes.view') }}" class="nav-link nav-toggle">
                            <i class="icon-settings"></i>
                            <span class="title">المجموعات المنتهية</span>
                            <span class="arrow"></span>
                            <span class="icon-button__badge"
                                  style="
                                  top: -5px;
                                  position: relative;
                                  align-items: center;
                                  justify-content: center;
                                  width: 26px;
                                  height: 21px;
                                  left:-11px;
                                  background: #ec7070;
                                  border: none;
                                  outline: none;
                                  border-radius: 50%;">
                                {{ $Closed_Classes_count ? $Closed_Classes_count : 0 }}
                            </span>
                        </a>
                    </li>
                    <li class="nav-item {{ $active_menu == 'ask_update' ? 'active' : '' }}">
                        <a href="{{ route('ask_update.view') }}" class="nav-link nav-toggle">
                            <i class="icon-settings"></i>
                            <span class="title">طلبات تحديث البيانات</span>
                            <span class="arrow"></span>
                            <span class="icon-button__badge"
                                  style="
                                  top: -5px;
                                  position: relative;
                                  align-items: center;
                                  justify-content: center;
                                  width: 26px;
                                  height: 21px;
                                  left:-11px;
                                  background: #ec7070;
                                  border: none;
                                  outline: none;
                                  border-radius: 50%;">
                                {{ $Closed_Classes_count ? $Closed_Classes_count : 0 }}
                            </span>
                        </a>
                    </li>
                    @endif

                </ul>
            </li>
            <li class="nav-item {{ $active_menu == 'memberships' ? 'active' : '' }}">
                {{-- <a href="{{route('dashboard.view.membership')}}" class="nav-link nav-toggle"> --}}
                <a href="javascript:void(0);" class="nav-link nav-toggle">
                    <i class="bi bi-bell"></i>
                    <span class="title">ادارة الاشعارات</span>
                    <span class="icon-button__badge"
                          style="
                          top: -57px;
                          position: relative;
                          align-items: center;
                          width: 26px;
                          height: 26px;
                          left: -107px;
                          background: #fd6f69;
                          border: none;
                          outline: none;
                          border-radius: 50%;
                          justify-content: center;
                          display: flex;
                          color: white;">
                        {{ $total_teachers_students_measge ? $total_teachers_students_measge : 0 }}
                    </span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">


                    @if (auth()->user()->can('admin.students.view') ||
                    auth()->user()->can('admin.students.add') ||
                    auth()->user()->can('admin.students.edit') ||
                    auth()->user()->can('admin.students.delete') ||
                    auth()->user()->can('admin.students.status'))
                    <li class="nav-item {{ $active_menu == 'memberships' ? 'active' : '' }}">
                        <a href="{{ route('students.messages') }}" class="nav-link nav-toggle">
                            <i class="bi bi-chat-dots-fill"></i>
                            <span class="title">رسائل الطلاب</span>
                            <span class="arrow"></span>
                            <span class="icon-button__badge"
                                  style="
                                  top: -5px;
                                  position: relative;
                                  align-items: center;
                                  justify-content: center;
                                  width: 26px;
                                  height: 21px;
                                  left:-11px;
                                  background: #ec7070;
                                  border: none;
                                  outline: none;
                                  border-radius: 50%;">
                                {{ $Unread_measges_student ? $Unread_measges_student : 0 }}
                            </span>
                        </a>
                    </li>
                    <li class="nav-item {{ $active_menu == 'memberships' ? 'active' : '' }}">
                        <a href="{{ route('teachers.messages') }}" class="nav-link nav-toggle">
                            <i class="bi bi-chat-left-dots-fill"></i>
                            <span class="title">رسائل المعلمين</span>
                            <span class="arrow"></span>
                            <span class="icon-button__badge"
                                  style="
                                  top: -5px;
                                  position: relative;
                                  align-items: center;
                                  justify-content: center;
                                  width: 26px;
                                  height: 21px;
                                  left:-11px;
                                  background: #ec7070;
                                  border: none;
                                  outline: none;
                                  border-radius: 50%;">
                                {{ $Unread_measges_teacher ? $Unread_measges_teacher : 0 }}
                            </span>
                        </a>
                    </li>
                    @endif

                </ul>
            </li>
            <li class="nav-item {{ $active_menu == 'evaluate_items' ? 'active' : '' }}">
                {{-- <a href="{{route('dashboard.view.membership')}}" class="nav-link nav-toggle"> --}}
                <a href="javascript:void(0);" class="nav-link nav-toggle">
                    <i class="bi bi-award"></i>
                    <span class="title">ادارة التقيمات</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    @if (auth()->user()->can('admin.evaluate_items.view'))
                    <li class="nav-item {{ $active_menu == 'evaluate_items' ? 'active' : '' }}">
                        <a href="{{ route('evaluate_items.view') }}" class="nav-link nav-toggle">
                            <i class="bi bi-pass-fill"></i>
                            <span class="title">اسئلة تقييم الطلاب</span>
                            <span class="arrow"></span>
                        </a>
                    </li>
                    <li class="nav-item {{ $active_menu == 'questions' ? 'active' : '' }}">
                        <a href="{{ route('questions.view') }}" class="nav-link nav-toggle">
                            <i class="bi bi-pass-fill"></i>
                            <span class="title">اسئلة تقييم المعلمين</span>
                            <span class="arrow"></span>
                        </a>
                    </li>
                    <li class="nav-item {{ $active_menu == 'memberships' ? 'active' : '' }}">
                        <a href="{{ route('evaluate_items.view') }}" class="nav-link nav-toggle">
                            <i class="bi bi-chat-square-heart"></i>
                            <span class="title">تقييمات المعلمين</span>
                            <span class="arrow"></span>
                        </a>
                    </li>
                    <li class="nav-item {{ $active_menu == 'memberships' ? 'active' : '' }}">
                        <a href="{{ route('evaluate_items.view') }}" class="nav-link nav-toggle">
                            <i class="bi bi-chat-square-heart-fill"></i>
                            <span class="title">تقييمات الطلاب</span>
                            <span class="arrow"></span>
                        </a>
                    </li>
                    @endif

                </ul>
            </li>


            @if (auth()->user()->can('admin.news.view'))
            <li class="nav-item {{ $active_menu == 'absent_teacher' ? 'active' : '' }}">
                <a href="{{ route('absent_teacher.view') }}" class="nav-link nav-toggle">
                    <i class="bi bi-person-gear"></i>
                    <span class="title">سجل حضور المدرسين</span>
                    <span class="arrow"></span>
                </a>
            </li>
            @endif
            @if (auth()->user()->can('admin.news.view') ||
            auth()->user()->can('admin.news.add') ||
            auth()->user()->can('admin.news.edit') ||
            auth()->user()->can('admin.news.delete') ||
            auth()->user()->can('admin.news.status'))
            <li class="nav-item {{ $active_menu == 'news' ? 'active' : '' }}">
                <a href="{{ route('news.view') }}" class="nav-link nav-toggle">
                    <i class="icon-book-open"></i>
                    <span class="title">الأخبار</span>
                    <span class="arrow"></span>
                </a>
            </li>
            @endif
            <li class="nav-item {{ in_array($active_menu, ['photos', 'videos']) ? 'active' : '' }}">
                <a href="#" class="nav-link nav-toggle">
                    <i class="icon-book-open"></i>
                    <span class="title">ادارة الوسائط</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    @if (auth()->user()->can('admin.photos.view') ||
                    auth()->user()->can('admin.photos.add') ||
                    auth()->user()->can('admin.photos.edit') ||
                    auth()->user()->can('admin.photos.delete') ||
                    auth()->user()->can('admin.photos.status'))
                    <li class="nav-item {{ $active_menu == 'photos' ? 'active' : '' }}">
                        <a href="{{ route('photos.view') }}" class="nav-link nav-toggle">
                            <i class="icon-book-open"></i>
                            <span class="title">الصور</span>
                            <span class="arrow"></span>
                        </a>
                    </li>
                    @endif
                    @if (auth()->user()->can('admin.videos.view') ||
                    auth()->user()->can('admin.videos.add') ||
                    auth()->user()->can('admin.videos.edit') ||
                    auth()->user()->can('admin.videos.delete') ||
                    auth()->user()->can('admin.videos.status'))
                    <li class="nav-item {{ $active_menu == 'videos' ? 'active' : '' }}">
                        <a href="{{ route('videos.view') }}" class="nav-link nav-toggle">
                            <i class="icon-book-open"></i>
                            <span class="title">الفيديو</span>
                            <span class="arrow"></span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @if (auth()->user()->can('admin.pages.view') ||
            auth()->user()->can('admin.pages.edit'))
            <li class="nav-item {{ $active_menu == 'pages' ? 'active' : '' }}">
                <a href="{{ route('pages.view') }}" class="nav-link nav-toggle">
                    <i class="icon-grid"></i>
                    <span class="title">الصفحات الثابته</span>
                    <span class="arrow"></span>
                </a>
            </li>
            @endif

            <li
                class="nav-item {{ in_array($active_menu, ['settings', 'socials', 'users', 'roles']) ? 'active' : '' }}">
                <a href="#" class="nav-link nav-toggle">
                    <i class="icon-book-open"></i>
                    <span class="title">ادارة الموقع</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    @can('admin.settings.view')
                    <li class="nav-item {{ $active_menu == 'settings' ? 'active' : '' }}">
                        <a href="{{ route('settings.view') }}" class="nav-link nav-toggle">
                            <i class="icon-settings"></i>
                            <span class="title">الإعدادات</span>
                            <span class="arrow"></span>
                        </a>
                    </li>
                    @endcan

                    @can('admin.social.view')
                    <li class="nav-item {{ $active_menu == 'socials' ? 'active' : '' }}">
                        <a href="{{ route('socials.view') }}" class="nav-link nav-toggle">
                            <i class="icon-social-twitter"></i>
                            <span class="title">الشبكات الإجتماعية</span>
                            <span class="arrow"></span>
                        </a>
                    </li>
                    @endcan
                    @if (auth()->user()->can('admin.users.view') ||
                    auth()->user()->can('admin.users.add') ||
                    auth()->user()->can('admin.users.edit') ||
                    auth()->user()->can('admin.users.delete') ||
                    auth()->user()->can('admin.users.status') ||
                    auth()->user()->can('admin.users.password'))
                    <li class="nav-item {{ $active_menu == 'users' ? 'active' : '' }}">
                        <a href="{{ route('users.view') }}" class="nav-link nav-toggle">
                            <i class="icon-users"></i>
                            <span class="title">المستخدمين</span>
                            <span class="arrow"></span>
                        </a>
                    </li>
                    @endif
                    @if (auth()->user()->can('admin.roles.view') ||
                    auth()->user()->can('admin.roles.add') ||
                    auth()->user()->can('admin.roles.edit') ||
                    auth()->user()->can('admin.roles.delete') ||
                    auth()->user()->can('admin.roles.status') ||
                    auth()->user()->can('admin.roles.permissions'))
                    <li class="nav-item {{ $active_menu == 'roles' ? 'active' : '' }}">
                        <a href="{{ route('roles.view') }}" class="nav-link nav-toggle">
                            <i class="icon-directions"></i>
                            <span class="title">الصلاحيات</span>
                            <span class="arrow"></span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            <li
                class="nav-item {{ in_array($active_menu, ['programs', 'groups', 'students', 'times', 'fees', 'evaluations', 'teachers']) ? 'active' : '' }}">
                <a href="#" class="nav-link nav-toggle">
                    <i class="icon-book-open"></i>
                    <span class="title">ادارة الاكاديمية</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    @if (auth()->user()->can('admin.programs.view') ||
                    auth()->user()->can('admin.programs.add') ||
                    auth()->user()->can('admin.programs.edit') ||
                    auth()->user()->can('admin.programs.delete') ||
                    auth()->user()->can('admin.programs.status'))
                    <li class="nav-item {{ $active_menu == 'programs' ? 'active' : '' }}">
                        <a href="{{ route('programs.view') }}" class="nav-link nav-toggle">
                            <i class="icon-book-open"></i>
                            <span class="title">البرامج</span>
                            <span class="arrow"></span>
                        </a>
                    </li>
                    @endif
                    @if (auth()->user()->can('admin.groups.view') ||
                    auth()->user()->can('admin.groups.add') ||
                    auth()->user()->can('admin.groups.edit') ||
                    auth()->user()->can('admin.groups.delete') ||
                    auth()->user()->can('admin.groups.status'))
                    <li class="nav-item {{ $active_menu == 'groups' ? 'active' : '' }}">
                        <a href="{{ route('groups.view') }}" class="nav-link nav-toggle">
                            <i class="icon-book-open"></i>
                            <span class="title">المجموعات</span>
                            <span class="arrow"></span>
                        </a>
                    </li>
                    @endif
                    @if (auth()->user()->can('admin.teachers.view') ||
                    auth()->user()->can('admin.teachers.add') ||
                    auth()->user()->can('admin.teachers.edit') ||
                    auth()->user()->can('admin.teachers.delete') ||
                    auth()->user()->can('admin.teachers.status'))
                    <li class="nav-item {{ $active_menu == 'teachers' ? 'active' : '' }}">
                        <a href="{{ route('teachers.view') }}" class="nav-link nav-toggle">
                            <i class="icon-book-open"></i>
                            <span class="title">المعلمون</span>
                            <span class="arrow"></span>
                        </a>
                    </li>
                    @endif
                    @if (auth()->user()->can('admin.students.view') ||
                    auth()->user()->can('admin.students.add') ||
                    auth()->user()->can('admin.students.edit') ||
                    auth()->user()->can('admin.students.delete') ||
                    auth()->user()->can('admin.students.status'))
                    <li class="nav-item {{ $active_menu == 'students' ? 'active' : '' }}">
                        <a href="{{ route('students.view') }}" class="nav-link nav-toggle">
                            <i class="icon-book-open"></i>
                            <span class="title">الطلاب</span>
                            <span class="arrow"></span>
                        </a>
                    </li>
                    @endif
                    @if (auth()->user()->can('admin.times.view') ||
                    auth()->user()->can('admin.times.add') ||
                    auth()->user()->can('admin.times.edit') ||
                    auth()->user()->can('admin.times.delete') ||
                    auth()->user()->can('admin.times.status'))
                    <li class="nav-item {{ $active_menu == 'times' ? 'active' : '' }}">
                        <a href="{{ route('times.view') }}" class="nav-link nav-toggle">
                            <i class="icon-book-open"></i>
                            <span class="title">المواعيد والاوقات</span>
                            <span class="arrow"></span>
                        </a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a href="{{ url('admin/file_manager?type=file') }}" class="nav-link nav-toggle"
                           target="_blank">
                            <i class="icon-book-open"></i>
                            <span class="title">المواد التعليمية</span>
                            <span class="arrow"></span>
                        </a>
                    </li>
                    @if (auth()->user()->can('admin.fees.view') ||
                    auth()->user()->can('admin.fees.add') ||
                    auth()->user()->can('admin.fees.edit') ||
                    auth()->user()->can('admin.fees.delete') ||
                    auth()->user()->can('admin.fees.status'))
                    <li class="nav-item {{ $active_menu == 'fees' ? 'active' : '' }}">
                        <a href="{{ route('fees.view') }}" class="nav-link nav-toggle">
                            <i class="icon-book-open"></i>
                            <span class="title">الرسوم</span>
                            <span class="arrow"></span>
                        </a>
                    </li>
                    @endif
                    <!--                    @if (auth()->user()->can('admin.files.view') ||
                            auth()->user()->can('admin.files.add') ||
                            auth()->user()->can('admin.files.edit') ||
                            auth()->user()->can('admin.files.delete') ||
                            auth()->user()->can('admin.files.status'))
<li class="nav-item {{ $active_menu == 'files' ? 'active' : '' }}">
                                            <a href="{{ route('files.view') }}" class="nav-link nav-toggle">
                                                <i class="icon-book-open"></i>
                                                <span class="title">المواد التعلمية</span>
                                                <span class="arrow"></span>
                                            </a>
                                        </li>
@endif-->
                    <?php /* @if(auth()->user()->can('admin.evaluations.view') || auth()->user()->can('admin.evaluations.add') || auth()->user()->can('admin.evaluations.edit') || auth()->user()->can('admin.evaluations.delete') || auth()->user()->can('admin.evaluations.status'))
                      <li class="nav-item {{ $active_menu == 'evaluations' ? 'active' : '' }}">
                      <a href="{{ route('evaluations.view') }}" class="nav-link nav-toggle">
                      <i class="icon-book-open"></i>
                      <span class="title">التقيمات</span>
                      <span class="arrow"></span>
                      </a>
                      </li>
                      @endif
                     */
                    ?>

                </ul>
            </li>

            @if (auth()->user()->can('admin.students_report.view'))
            @php $routeName = Route::currentRouteName(); @endphp
            <li class="nav-item {{ $routeName == 'students_report.view' ? 'active' : '' }}">
                <a href="{{ route('students_report.view') }}" class="nav-link nav-toggle">
                    <i class="bi bi-person-bounding-box"></i>
                    <span class="title">استعلامات الطلاب</span>
                    <span class="arrow"></span>
                </a>
            </li>
            @endif
            @if (auth()->user()->can('admin.certificates.view'))
            @php $routeName = Route::currentRouteName(); @endphp
            <li class="nav-item {{ $routeName == 'certificates.view' ? 'active' : '' }}">
                <a href="{{ route('certificates.view') }}" class="nav-link nav-toggle">
                    <i class="bi bi-mortarboard-fill"></i>
                    <span class="title">الشهادات</span>
                    <span class="arrow"></span>
                </a>
            </li>
            @endif
            @if (auth()->user()->can('admin.categories.view') ||
            auth()->user()->can('admin.categories.add') ||
            auth()->user()->can('admin.categories.edit') ||
            auth()->user()->can('admin.categories.delete') ||
            auth()->user()->can('admin.categories.status'))
            @php $routeName = Route::currentRouteName(); @endphp
            <li class="nav-item {{ $routeName == 'students.delay.view' ? 'active' : '' }}">
                <a href="{{ route('students.delay.view') }}" class="nav-link nav-toggle">
                    <i class="bi bi-clock-history"></i>
                    <span class="title">الطلاب المؤجلين</span>
                    <span class="arrow"></span>
                </a>
            </li>
            @endif
            @if (auth()->user()->can('admin.progress_menu.view'))
            @php $routeName = Route::currentRouteName(); @endphp
            <li class="nav-item {{ $routeName == 'progress_menu.view' ? 'active' : '' }}">
                <a href="{{ route('progress_menu.view') }}" class="nav-link nav-toggle">
                    <i class="bi bi-hourglass-split"></i>
                    <span class="title">مستوى التقدم</span>
                    <span class="arrow"></span>
                </a>
            </li>
            @endif

            @if (auth()->user()->can('admin.categories.view') ||
            auth()->user()->can('admin.categories.add') ||
            auth()->user()->can('admin.categories.edit') ||
            auth()->user()->can('admin.categories.delete') ||
            auth()->user()->can('admin.categories.status'))
            @php $routeName = Route::currentRouteName(); @endphp
            <li class="nav-item {{ $routeName == 'birthdayes.view' ? 'active' : '' }}">
                <a href="{{ route('birthdayes.view') }}" class="nav-link nav-toggle">
                    <i class="bi bi-gift"></i>
                    <span class="title">اعيادالميلاد</span>
                    <span class="icon-button__badge"
                          style="
                          top: -5px;
                          position: relative;
                          align-items: center;
                          justify-content: center;
                          width: 26px;
                          height: 21px;
                          left:-11px;
                          background: #ec7070;
                          border: none;
                          outline: none;
                          border-radius: 50%;">
                        {{ $BirthdaysCount ? $BirthdaysCount : 0 }}
                    </span>
                    <span class="arrow"></span>


                </a>

            </li>
            @endif
            @if (auth()->user()->can('admin.categories.view') ||
            auth()->user()->can('admin.categories.add') ||
            auth()->user()->can('admin.categories.edit') ||
            auth()->user()->can('admin.categories.delete') ||
            auth()->user()->can('admin.categories.status'))
            <li class="nav-item {{ $active_menu == 'categories' ? 'active' : '' }}">
                <a href="{{ route('categories.view') }}" class="nav-link nav-toggle">
                    <i class="icon-grid"></i>
                    <span class="title">التصنيفات</span>
                    <span class="arrow"></span>
                </a>
            </li>
            @endif
            @if (auth()->user()->can('admin.partners.view') ||
            auth()->user()->can('admin.partners.add') ||
            auth()->user()->can('admin.partners.edit') ||
            auth()->user()->can('admin.partners.delete') ||
            auth()->user()->can('admin.partners.status'))
            <li class="nav-item {{ $active_menu == 'partners' ? 'active' : '' }}">
                <a href="{{ route('partners.view') }}" class="nav-link nav-toggle">
                    <i class="icon-film"></i>
                    <span class="title">الشركاء</span>
                    <span class="arrow"></span>
                </a>
            </li>
            @endif

            {{-- ===== الإدارة المالية ===== --}}
            @if (auth()->user()->can('admin.financial.view') ||
                auth()->user()->can('admin.financial.verify') ||
                auth()->user()->can('admin.financial.refund'))
            <li class="nav-item {{ in_array($active_menu, ['financial', 'financial_fees']) ? 'active' : '' }}">
                <a href="javascript:void(0);" class="nav-link nav-toggle">
                    <i class="bi bi-cash-stack"></i>
                    <span class="title">الإدارة المالية</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    @can('admin.financial.view')
                    <li class="nav-item {{ $active_menu == 'financial' ? 'active' : '' }}">
                        <a href="{{ route('admin.financial.pending') }}" class="nav-link nav-toggle">
                            <i class="bi bi-hourglass-split"></i>
                            <span class="title">الطلبات المالية المعلقة</span>
                            <span class="arrow"></span>
                        </a>
                    </li>
                    <li class="nav-item {{ $active_menu == 'financial_fees' ? 'active' : '' }}">
                        <a href="{{ route('admin.financial.fees') }}" class="nav-link nav-toggle">
                            <i class="bi bi-sliders"></i>
                            <span class="title">إعدادات أنواع الرسوم</span>
                            <span class="arrow"></span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endif

        </ul>
        <!-- END SIDEBAR MENU -->
    </div>
    <!-- END SIDEBAR -->
</div>
