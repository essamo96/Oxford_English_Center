{{-- ============================================================
     Oxford English Centre — Dashboard Shell layout (student & teacher)
     Metronic-style: fixed top header + collapsible sidebar + top menu
     bar + padded content. Reuses head + footer (scripts) from the
     existing frontend so ALL current JS, chat, tabs and AJAX keep
     working. Public website + login pages are unaffected (they keep
     using frontend.layouts.master).
     ============================================================ --}}
@php
    $isStudent = Auth::guard('students')->check();
    $isTeacher = Auth::guard('teachers')->check();
    $onStudent = Request::is('student');
    $onTeacher = Request::is('teacher');
    $dashUser  = $isStudent ? Auth::guard('students')->user() : ($isTeacher ? Auth::guard('teachers')->user() : null);
    $dashBase  = $isStudent ? '/student' : '/teacher';
    $dashCount = $count ?? 0;
@endphp
<!doctype html>
<html class="no-js" lang="en" dir="ltr">
    <head>
        @include('frontend.general.head')
        <link rel="stylesheet" href="{{ url('assets/css/dashboard.css?v=4') }}">
        <link rel="stylesheet" href="{{ url('assets/css/dash-components.css?v=3') }}">
        @yield('css')
    </head>
    <body>
        <div class="ox-dash" data-dash-theme="dark" dir="ltr">
            {{-- restore theme + sidebar state before paint (no FOUC) --}}
            <script>
                (function () {
                    try {
                        var d = document.currentScript.parentNode;
                        var t = localStorage.getItem('ox_dash_theme');
                        if (t) d.setAttribute('data-dash-theme', t);
                        if (window.innerWidth >= 992 && localStorage.getItem('ox_dash_sidebar') === 'collapsed') {
                            d.classList.add('is-collapsed');
                        }
                    } catch (e) {}
                })();
            </script>

            @include('frontend.general.dashboard-topbar')

            <div class="ox-dash__body">
                @include('frontend.general.dashboard-sidebar')
                <div class="ox-dash__backdrop" data-dash-backdrop></div>

                <main class="ox-dash__main">
                    @include('frontend.general.dashboard-topmenu')
                    <div class="ox-dash__content">
                        @yield('content')
                    </div>
                    {{-- included for its script bundle (jQuery/Bootstrap/etc.); its
                         visual chrome is hidden via dashboard.css --}}
                    @include('frontend.general.footer')
                </main>
            </div>
        </div>

        {{-- preserved global chat overlay + alert sound --}}
        <div id="chat-overlay" class="row"></div>
        <audio id="chat-alert-sound" style="display: none">
            <source src="{{ url('assets/oxford/sound/facebook_chat.mp3') }}" />
        </audio>

        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script src="{{ url('assets/js/dashboard.js?v=1') }}"></script>
        @yield('js')
    </body>
</html>
