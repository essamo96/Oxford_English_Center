{{-- ============================================================
     Dashboard sidebar (role-aware). English / LTR only. Every hash link
     keeps the SAME href + data-toggle="tab" as the old navbar so the
     Bootstrap-3 tab plugin / AJAX loaders fire exactly as before.
     data-tab-target is read by dashboard.js to mirror the active state.
     Expects: $isStudent, $isTeacher, $onStudent, $onTeacher, $dashUser,
     $dashBase, $dashCount.
     ============================================================ --}}
<aside class="ox-dash__sidebar">
    <div class="ox-dash__sidebar-mobile-logo">
        <img src="{{ url('assets/oxford/img/logo.png') }}" class="logo-light" alt="Logo">
        <img src="{{ url('assets/oxford/img/footer-logo.png') }}" class="logo-dark" alt="Logo">
    </div>
    <nav class="ox-dash__nav" aria-label="Dashboard">

        @if($isStudent)
            {{-- ---------------- STUDENT ---------------- --}}
            <div class="ox-dash__group">
                <div class="ox-dash__group-label">Overview</div>
                <a class="ox-dash__navlink" data-tab-target="#Welcome"
                   href="{{ $onStudent ? '#Welcome' : url('/student#Welcome') }}" @if($onStudent) data-toggle="tab" @endif>
                    <i class="bi bi-grid-1x2"></i><span class="ox-dash__navtext">Dashboard</span>
                </a>
                <a class="ox-dash__navlink" data-tab-target="#Courses"
                   href="{{ $onStudent ? '#Courses' : url('/student#Courses') }}" @if($onStudent) data-toggle="tab" @endif>
                    <i class="bi bi-mortarboard"></i><span class="ox-dash__navtext">Courses</span>
                </a>
            </div>

            <div class="ox-dash__group">
                <div class="ox-dash__group-label">Academics</div>
                <a class="ox-dash__navlink" data-tab-target="#Exam"
                   href="{{ $onStudent ? '#Exam' : url('/student#Exam') }}" @if($onStudent) data-toggle="tab" @endif>
                    <i class="bi bi-calendar-check"></i><span class="ox-dash__navtext">Exam Data</span>
                </a>
                <a class="ox-dash__navlink" data-tab-target="#Teacher_Evaluations"
                   href="{{ $onStudent ? '#Teacher_Evaluations' : url('/student#Teacher_Evaluations') }}" @if($onStudent) data-toggle="tab" @endif>
                    <i class="bi bi-star"></i><span class="ox-dash__navtext">Evaluations</span>
                </a>
            </div>

            <div class="ox-dash__group">
                <div class="ox-dash__group-label">Finance</div>
                <a class="ox-dash__navlink {{ Request::is('student/financial/history') ? 'is-active' : '' }}"
                   href="{{ url('student/financial/history') }}">
                    <i class="bi bi-clock-history"></i><span class="ox-dash__navtext">السجل المالي</span>
                </a>
                <a class="ox-dash__navlink {{ Request::is('student/financial/invoices') ? 'is-active' : '' }}"
                   href="{{ url('student/financial/invoices') }}">
                    <i class="bi bi-receipt"></i><span class="ox-dash__navtext">الفواتير المستحقة</span>
                </a>
                <a class="ox-dash__navlink {{ Request::is('student/financial/notifications') ? 'is-active' : '' }}"
                   href="{{ url('student/financial/notifications') }}">
                    <i class="bi bi-bell"></i><span class="ox-dash__navtext">الإشعارات المالية</span>
                    @php
                        $finUnread = 0;
                        if(Auth::guard('students')->check()) {
                            $finUnread = Auth::guard('students')->user()->unreadNotifications()->count();
                        }
                    @endphp
                    @if($finUnread > 0)<span class="ox-dash__navbadge">{{ $finUnread }}</span>@endif
                </a>
            </div>

            <div class="ox-dash__group">
                <div class="ox-dash__group-label">Account</div>
                <a class="ox-dash__navlink AdminNotify" data-tab-target="#AdminNotify"
                   href="{{ $onStudent ? '#AdminNotify' : url('/student#AdminNotify') }}" @if($onStudent) data-toggle="tab" @endif>
                    <i class="bi bi-chat-dots"></i><span class="ox-dash__navtext">Messages</span>
                    @if($dashCount > 0)<span class="ox-dash__navbadge">{{ $dashCount }}</span>@endif
                </a>
                <a class="ox-dash__navlink" data-tab-target="#MyInfo"
                   href="{{ $onStudent ? '#MyInfo' : url('/student#MyInfo') }}" @if($onStudent) data-toggle="tab" @endif>
                    <i class="bi bi-info-circle"></i><span class="ox-dash__navtext">My Information</span>
                </a>
                <a class="ox-dash__navlink" data-tab-target="#Profile"
                   href="{{ $onStudent ? '#Profile' : url('/student#Profile') }}" @if($onStudent) data-toggle="tab" @endif>
                    <i class="bi bi-person"></i><span class="ox-dash__navtext">Profile</span>
                </a>
                <a class="ox-dash__navlink" data-tab-target="#Password"
                   href="{{ $onStudent ? '#Password' : url('/student#Password') }}" @if($onStudent) data-toggle="tab" @endif>
                    <i class="bi bi-shield-lock"></i><span class="ox-dash__navtext">Password</span>
                </a>
            </div>

        @elseif($isTeacher)
            {{-- ---------------- TEACHER ---------------- --}}
            @php $tid = optional($dashUser)->id; @endphp
            <div class="ox-dash__group">
                <div class="ox-dash__group-label">Overview</div>
                <a class="ox-dash__navlink" data-tab-target="#Welcome"
                   href="{{ $onTeacher ? '#Welcome' : url('/teacher#Welcome') }}" @if($onTeacher) data-toggle="tab" @endif>
                    <i class="bi bi-grid-1x2"></i><span class="ox-dash__navtext">Dashboard</span>
                </a>
                <a class="ox-dash__navlink" data-tab-target="#Courses"
                   href="{{ $onTeacher ? '#Courses' : url('/teacher#Courses') }}" @if($onTeacher) data-toggle="tab" @endif>
                    <i class="bi bi-mortarboard"></i><span class="ox-dash__navtext">Courses</span>
                </a>
            </div>

            <div class="ox-dash__group">
                <div class="ox-dash__group-label">Teaching</div>
                <a class="ox-dash__navlink {{ (Request::is('grope/Exam*') || Request::is('examDate*')) ? 'is-active' : '' }}"
                   href="{{ route('teacher.ExamDates', $tid) }}">
                    <i class="bi bi-calendar3"></i><span class="ox-dash__navtext">Exam Schedule</span>
                </a>
                <a class="ox-dash__navlink {{ Request::is('progress*') ? 'is-active' : '' }}"
                   href="{{ route('teacher.progress', $tid) }}">
                    <i class="bi bi-graph-up"></i><span class="ox-dash__navtext">Progress</span>
                </a>
            </div>

            <div class="ox-dash__group">
                <div class="ox-dash__group-label">Account</div>
                <a class="ox-dash__navlink AdminNotify" data-tab-target="#AdminNotify"
                   href="{{ $onTeacher ? '#AdminNotify' : url('/teacher#AdminNotify') }}" @if($onTeacher) data-toggle="tab" @endif>
                    <i class="bi bi-chat-dots"></i><span class="ox-dash__navtext">Messages</span>
                    @if($dashCount > 0)<span class="ox-dash__navbadge">{{ $dashCount }}</span>@endif
                </a>
                <a class="ox-dash__navlink" data-tab-target="#MyInfo"
                   href="{{ $onTeacher ? '#MyInfo' : url('/teacher#MyInfo') }}" @if($onTeacher) data-toggle="tab" @endif>
                    <i class="bi bi-info-circle"></i><span class="ox-dash__navtext">My Information</span>
                </a>
                <a class="ox-dash__navlink" data-tab-target="#Profile"
                   href="{{ $onTeacher ? '#Profile' : url('/teacher#Profile') }}" @if($onTeacher) data-toggle="tab" @endif>
                    <i class="bi bi-person"></i><span class="ox-dash__navtext">Profile</span>
                </a>
                <a class="ox-dash__navlink" data-tab-target="#Password"
                   href="{{ $onTeacher ? '#Password' : url('/teacher#Password') }}" @if($onTeacher) data-toggle="tab" @endif>
                    <i class="bi bi-shield-lock"></i><span class="ox-dash__navtext">Password</span>
                </a>
            </div>
        @endif

    </nav>

    <div class="ox-dash__sidebar-foot">
        <a class="ox-dash__logout" href="{{ url('/logout') }}">
            <i class="bi bi-box-arrow-right"></i><span>Logout</span>
        </a>
    </div>
</aside>
