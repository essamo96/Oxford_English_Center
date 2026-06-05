{{-- ============================================================
     Dashboard top header: brand + sidebar toggle | search | theme
     toggle + notifications + profile dropdown. English / LTR only.
     Expects (from frontend.layouts.dashboard): $isStudent, $isTeacher,
     $onStudent, $onTeacher, $dashUser, $dashBase, $dashCount.
     ============================================================ --}}
@php
    $dashAvatar = ($dashUser && $dashUser->image)
        ? url($dashUser->image)
        : url('assets/oxford/img/students/avatar.png');
    $dashRole   = $isStudent ? 'Student' : ($isTeacher ? 'Teacher' : '');
    $onDash     = $isStudent ? $onStudent : $onTeacher;
    $notifyHref = $onDash ? '#AdminNotify' : url($dashBase.'#AdminNotify');
@endphp

<header class="ox-dash__topbar">
    <a class="ox-dash__brand" href="{{ url($dashBase) }}">
        <img src="{{ url('assets/oxford/img/logo.png') }}" class="logo-light" alt="Oxford English Centre">
        <img src="{{ url('assets/oxford/img/footer-logo.png') }}" class="logo-dark" alt="Oxford English Centre">
        <span class="ox-dash__brand-title">
            @if($isTeacher)
                Teacher Dashboard
            @elseif($isStudent)
                Student Dashboard
            @else
                Dashboard
            @endif
        </span>
    </a>
    <button class="ox-dash__toggle" data-dash-toggle type="button" aria-label="Toggle sidebar">
        <i class="fa fa-angle-double-left icon-expanded"></i>
        <i class="fa fa-angle-double-right icon-collapsed" style="display: none;"></i>
    </button>

    <div class="ox-dash__search">
        <i class="bi bi-search"></i>
        <input type="text" placeholder="Search…" aria-label="Search">
    </div>

    <div class="ox-dash__actions">
        {{-- theme toggle (sun shows in dark mode, moon in light mode) --}}
        <button class="ox-dash__iconbtn" data-theme-toggle type="button" aria-label="Toggle theme" title="Toggle light / dark">
            <i class="bi bi-sun-fill"></i>
            <i class="bi bi-moon-stars-fill"></i>
        </button>

        {{-- notifications --}}
        <a class="ox-dash__iconbtn AdminNotify" href="{{ $notifyHref }}"
           @if($onDash) data-toggle="tab" @endif title="Messages">
            <i class="bi bi-bell"></i>
            @if($dashCount > 0)
                <span class="ox-dash__badge">{{ $dashCount }}</span>
            @endif
        </a>

        {{-- profile --}}
        <div class="ox-dash__profile">
            <button class="ox-dash__profile-btn" data-profile-toggle type="button">
                <img src="{{ $dashAvatar }}" alt="{{ optional($dashUser)->name }}">
                <span class="ox-dash__profile-name">
                    {{ optional($dashUser)->name ?: $dashRole }}
                    <small>{{ $dashRole }}</small>
                </span>
                <i class="bi bi-chevron-down"></i>
            </button>
            <div class="ox-dash__menu">
                <a href="{{ $onDash ? '#MyInfo' : url($dashBase.'#MyInfo') }}" @if($onDash) data-toggle="tab" @endif>
                    <i class="bi bi-info-circle"></i> My Information
                </a>
                <a href="{{ $onDash ? '#Profile' : url($dashBase.'#Profile') }}" @if($onDash) data-toggle="tab" @endif>
                    <i class="bi bi-person"></i> Profile
                </a>
                <a href="{{ $onDash ? '#Password' : url($dashBase.'#Password') }}" @if($onDash) data-toggle="tab" @endif>
                    <i class="bi bi-shield-lock"></i> Password
                </a>
                <hr>
                <a class="is-danger" href="{{ url('/logout') }}">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
    </div>
</header>
