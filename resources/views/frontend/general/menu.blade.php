{{-- ============================================================
     Oxford English Centre — Header / Navigation (redesigned 2026)
     Markup modernised. ALL auth guards, routes, dashboard tab links
     (data-toggle="tab") and active states preserved exactly.
     ============================================================ --}}
@php
    $isStudent = Auth::guard('students')->check();
    $isTeacher = Auth::guard('teachers')->check();
    $onStudent = Request::is('student');
    $onTeacher = Request::is('teacher');
@endphp

<header class="ox-header" data-nav-sentinel>

    {{-- ---------- Top utility bar ---------- --}}
    <div class="ox-topbar">
        <div class="ox-container ox-topbar__inner">
            <ul class="ox-topbar__contact">
                <li><i class="bi bi-telephone-fill"></i> <a href="tel:+{{ optional($mysettings)->mobile }}">+{{ optional($mysettings)->mobile }}</a></li>
                <li><i class="bi bi-envelope-fill"></i> <a href="mailto:{{ optional($mysettings)->contact_email }}">{{ optional($mysettings)->contact_email }}</a></li>
            </ul>
            <div class="ox-topbar__brand">
                <img src="{{ url('assets/oxford/img/OTE-Approved-Test-Centre-Logo.png') }}" alt="OTE Approved Test Centre" style="max-width: 150px; height: 70px;">
            </div>
            <ul class="ox-topbar__cta">
                <li><a class="ox-toplink ox-toplink--accent" href="{{ url('book') }}"><i class="bi bi-mortarboard-fill"></i> Apply Now</a></li>
                @if(!$isStudent && !$isTeacher)
                    <li><a class="ox-toplink" href="{{ url('login') }}"><i class="bi bi-person-fill"></i> Student Gate</a></li>
                    <li><a class="ox-toplink" href="{{ url('login/teacher') }}"><i class="bi bi-person-workspace"></i> Teacher Gate</a></li>
                @else
                    <li><a class="ox-toplink" href="{{ url('logout') }}"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                @endif
            </ul>
        </div>
    </div>

    {{-- ---------- Main navbar ---------- --}}
    <div class="ox-navbar" data-navbar>
        <div class="ox-container ox-navbar__inner">

            {{-- Brand / avatar --}}
            @if($isStudent)
                <a class="ox-brand ox-brand--avatar" href="{{ url('/student') }}">
                    <img src="{{ Auth::guard('students')->user()->image ? url(Auth::guard('students')->user()->image) : url('assets/oxford/img/students/avatar.png') }}" alt="student">
                </a>
            @elseif($isTeacher)
                <a class="ox-brand ox-brand--avatar" href="{{ url('/teacher') }}">
                    <img src="{{ Auth::guard('teachers')->user()->image ? url(Auth::guard('teachers')->user()->image) : url('assets/oxford/img/students/avatar.png') }}" alt="teacher">
                </a>
            @else
                <a class="ox-brand" href="{{ url('/') }}"><img src="{{ url('assets/oxford/img/logo.png') }}" alt="Oxford English Centre"></a>
            @endif

            {{-- Desktop nav --}}
            <nav class="ox-nav" aria-label="Primary">
                <ul class="ox-menu">
                    @if($isStudent)
                        {{-- Student Dashboard Menu --}}
                        <li class="{{ $onStudent ? 'is-active' : '' }}"><a href="{{ $onStudent ? '#Welcome' : url('/student#Welcome') }}" @if($onStudent) data-toggle="tab" @endif>Welcome</a></li>
                        <li><a href="{{ $onStudent ? '#Courses' : url('/student#Courses') }}" @if($onStudent) data-toggle="tab" @endif>Courses</a></li>
                        <li><a href="{{ $onStudent ? '#Exam' : url('/student#Exam') }}" @if($onStudent) data-toggle="tab" @endif>Exam Data</a></li>
                        <li><a href="{{ $onStudent ? '#Teacher_Evaluations' : url('/student#Teacher_Evaluations') }}" @if($onStudent) data-toggle="tab" @endif>Evaluations</a></li>
                        <li><a href="{{ $onStudent ? '#Profile' : url('/student#Profile') }}" @if($onStudent) data-toggle="tab" @endif>Profile</a></li>
                        <li><a href="{{ $onStudent ? '#Password' : url('/student#Password') }}" @if($onStudent) data-toggle="tab" @endif>Password</a></li>
                        <li><a href="{{ url('/logout') }}">Logout</a></li>

                    @elseif($isTeacher)
                        {{-- Teacher Dashboard Menu --}}
                        <li class="{{ $onTeacher ? 'is-active' : '' }}"><a href="{{ $onTeacher ? '#Welcome' : url('/teacher#Welcome') }}" @if($onTeacher) data-toggle="tab" @endif>Welcome</a></li>
                        <li><a href="{{ $onTeacher ? '#Courses' : url('/teacher#Courses') }}" @if($onTeacher) data-toggle="tab" @endif>Courses</a></li>
                        <li class="{{ (Request::is('grope/Exam*') || Request::is('examDate*')) ? 'is-active' : '' }}"><a href="{{ route('teacher.ExamDates', Auth::guard('teachers')->user()->id) }}">Exam Schedule</a></li>
                        <li class="{{ Request::is('progress*') ? 'is-active' : '' }}"><a href="{{ route('teacher.progress', Auth::guard('teachers')->user()->id) }}">Progress</a></li>
                        <li><a href="{{ $onTeacher ? '#Profile' : url('/teacher#Profile') }}" @if($onTeacher) data-toggle="tab" @endif>Profile</a></li>
                        <li><a href="{{ $onTeacher ? '#Password' : url('/teacher#Password') }}" @if($onTeacher) data-toggle="tab" @endif>Password</a></li>
                        <li><a href="{{ url('/logout') }}">Logout</a></li>

                    @else
                        {{-- Main Website Menu --}}
                        <li class="{{ Request::is('/') ? 'is-active' : '' }}"><a href="{{ url('/') }}">Home</a></li>
                        <li>
                            <a href="#">About <i class="bi bi-chevron-down ox-caret"></i></a>
                            <ul class="ox-submenu">
                                <li><a href="{{ url('page/about') }}">About us</a></li>
                                <li><a href="{{ url('page/promise') }}">Oxford Promise</a></li>
                                <li><a href="{{ url('family') }}">Oxford Family</a></li>
                                <li><a href="{{ url('page/methods') }}">Teaching Methods</a></li>
                                <li><a href="{{ url('partners') }}">Partners</a></li>
                            </ul>
                        </li>
                        <li><a href="{{ url('test-of-english') }}">Oxford Test of English</a></li>
                        <li>
                            <a href="#">Courses <i class="bi bi-chevron-down ox-caret"></i></a>
                            <ul class="ox-submenu">
                                <li><a href="{{ url('page/ielts') }}">IELTS Preparation Course</a></li>
                                <li><a href="{{ url('page/general') }}">General English Levels</a></li>
                                <li><a href="{{ url('page/speaking') }}">Speaking Course</a></li>
                                <li><a href="{{ url('page/writing') }}">Academic Writing Course</a></li>
                                <li><a href="{{ url('page/business') }}">Business English Course</a></li>
                                <li><a href="{{ url('page/esp') }}">ESP Course</a></li>
                            </ul>
                        </li>
                        <li><a href="{{ url('page/prize') }}">IELTS Prize</a></li>
                        <li><a href="{{ url('community') }}">Community</a></li>
                        <li>
                            <a href="#">Gallery <i class="bi bi-chevron-down ox-caret"></i></a>
                            <ul class="ox-submenu">
                                <li><a href="{{ url('photos') }}">Photos</a></li>
                                <li><a href="{{ url('videos') }}">Videos</a></li>
                                <li><a href="{{ url('certificates') }}">Certificates</a></li>
                            </ul>
                        </li>
                        <li><a href="{{ url('jobs') }}">Jobs</a></li>
                        <li><a href="{{ url('contact') }}">Contact Us</a></li>
                    @endif
                </ul>
            </nav>

            {{-- Actions --}}
            <div class="ox-nav-actions">
                <button class="ox-iconbtn" data-search-toggle aria-label="Search"><i class="bi bi-search"></i></button>
                <button class="ox-iconbtn ox-burger" data-drawer-open aria-label="Open menu"><i class="bi bi-list"></i></button>
                <div class="ox-search-box" data-search-box>
                    <form action="#" style="display:flex;gap:8px;width:100%">
                        <input class="ox-input" type="text" placeholder="Search…." aria-label="Search">
                        <button type="submit" class="ox-btn ox-btn--primary ox-btn--sm" aria-label="Search"><i class="bi bi-search"></i></button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</header>

{{-- ---------- Mobile drawer ---------- --}}
<div class="ox-backdrop" data-backdrop></div>
<aside class="ox-drawer" data-drawer aria-label="Mobile menu">
    <div class="ox-drawer__head">
        <img src="{{ url('assets/oxford/img/logo.png') }}" alt="Oxford" style="height:52px">
        <button class="ox-iconbtn" data-drawer-close aria-label="Close menu"><i class="bi bi-x-lg"></i></button>
    </div>

    <ul class="ox-drawer__menu">
        @if($isStudent)
            <li><a href="{{ $onStudent ? '#Welcome' : url('/student#Welcome') }}" @if($onStudent) data-toggle="tab" @endif>Welcome</a></li>
            <li><a href="{{ $onStudent ? '#Courses' : url('/student#Courses') }}" @if($onStudent) data-toggle="tab" @endif>Courses</a></li>
            <li><a href="{{ $onStudent ? '#Exam' : url('/student#Exam') }}" @if($onStudent) data-toggle="tab" @endif>Exam Data</a></li>
            <li><a href="{{ $onStudent ? '#Teacher_Evaluations' : url('/student#Teacher_Evaluations') }}" @if($onStudent) data-toggle="tab" @endif>Evaluations</a></li>
            <li><a href="{{ $onStudent ? '#Profile' : url('/student#Profile') }}" @if($onStudent) data-toggle="tab" @endif>Profile</a></li>
            <li><a href="{{ $onStudent ? '#Password' : url('/student#Password') }}" @if($onStudent) data-toggle="tab" @endif>Password</a></li>
            <li><a href="{{ url('/logout') }}">Logout</a></li>

        @elseif($isTeacher)
            <li><a href="{{ $onTeacher ? '#Welcome' : url('/teacher#Welcome') }}" @if($onTeacher) data-toggle="tab" @endif>Welcome</a></li>
            <li><a href="{{ $onTeacher ? '#Courses' : url('/teacher#Courses') }}" @if($onTeacher) data-toggle="tab" @endif>Courses</a></li>
            <li><a href="{{ route('teacher.ExamDates', Auth::guard('teachers')->user()->id) }}">Exam Schedule</a></li>
            <li><a href="{{ route('teacher.progress', Auth::guard('teachers')->user()->id) }}">Progress</a></li>
            <li><a href="{{ $onTeacher ? '#Profile' : url('/teacher#Profile') }}" @if($onTeacher) data-toggle="tab" @endif>Profile</a></li>
            <li><a href="{{ $onTeacher ? '#Password' : url('/teacher#Password') }}" @if($onTeacher) data-toggle="tab" @endif>Password</a></li>
            <li><a href="{{ url('/logout') }}">Logout</a></li>

        @else
            <li><a href="{{ url('/') }}">Home</a></li>
            <li>
                <a href="#" data-submenu-toggle>About Oxford <i class="bi bi-chevron-down"></i></a>
                <ul>
                    <li><a href="{{ url('page/about') }}">About us</a></li>
                    <li><a href="{{ url('page/promise') }}">Oxford Promise</a></li>
                    <li><a href="{{ url('family') }}">Oxford Family</a></li>
                    <li><a href="{{ url('page/methods') }}">Teaching Methods</a></li>
                    <li><a href="{{ url('partners') }}">Partners</a></li>
                </ul>
            </li>
            <li><a href="{{ url('test-of-english') }}">Oxford Test of English</a></li>
            <li>
                <a href="#" data-submenu-toggle>Our Courses <i class="bi bi-chevron-down"></i></a>
                <ul>
                    <li><a href="{{ url('page/ielts') }}">IELTS Preparation Course</a></li>
                    <li><a href="{{ url('page/general') }}">General English Levels</a></li>
                    <li><a href="{{ url('page/speaking') }}">Speaking Course</a></li>
                    <li><a href="{{ url('page/writing') }}">Academic Writing Course</a></li>
                    <li><a href="{{ url('page/business') }}">Business English Course</a></li>
                    <li><a href="{{ url('page/esp') }}">ESP Course</a></li>
                </ul>
            </li>
            <li><a href="{{ url('page/prize') }}">Oxford IELTS Prize</a></li>
            <li><a href="{{ url('community') }}">Community</a></li>
            <li>
                <a href="#" data-submenu-toggle>Gallery <i class="bi bi-chevron-down"></i></a>
                <ul>
                    <li><a href="{{ url('photos') }}">Photos</a></li>
                    <li><a href="{{ url('videos') }}">Videos</a></li>
                </ul>
            </li>
            <li><a href="{{ url('jobs') }}">Jobs</a></li>
            <li><a href="{{ url('contact') }}">Contact Us</a></li>
        @endif
    </ul>

    @if(!$isStudent && !$isTeacher)
        <div class="ox-drawer__cta">
            <a class="ox-btn ox-btn--accent ox-btn--block" href="{{ url('book') }}"><i class="bi bi-mortarboard-fill"></i> Book Now</a>
            <div style="display:flex;gap:10px">
                <a class="ox-btn ox-btn--outline ox-btn--sm ox-btn--block" href="{{ url('login') }}"><i class="bi bi-person-fill"></i> Student Gate</a>
                <a class="ox-btn ox-btn--outline ox-btn--sm ox-btn--block" href="{{ url('login/teacher') }}"><i class="bi bi-person-workspace"></i> Teacher Gate</a>
            </div>
        </div>
    @else
        <a class="ox-btn ox-btn--outline ox-btn--block" href="{{ url('logout') }}" style="margin-top:auto"><i class="bi bi-box-arrow-right"></i> Logout</a>
    @endif
</aside>
