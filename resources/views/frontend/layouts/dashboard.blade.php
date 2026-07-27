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

        {{-- Pusher library — needed by both roles: chat.js (group chat) runs for students AND
             teachers, plus the student-only real-time notification channel below. Loading it
             only under @if($isStudent) left teachers with no window.Pusher at all, so their
             chat.js never even finished initializing (ReferenceError on the very first line). --}}
        @if($isStudent || $isTeacher)
        <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
        @endif

        {{-- ── Student real-time notifications (Pusher) ─────────────────── --}}
        @if($isStudent)
        @auth('students')
        <script>
        (function () {
            var key     = '{{ config("broadcasting.connections.pusher.key") }}';
            var cluster = '{{ config("broadcasting.connections.pusher.options.cluster", "mt1") }}';
            var host    = '{{ env("PUSHER_PUBLIC_HOST", config("broadcasting.connections.pusher.options.host", "")) }}';
            var port    = {{ (int) env("PUSHER_PUBLIC_PORT", config("broadcasting.connections.pusher.options.port", 443)) }};
            var scheme  = '{{ env("PUSHER_PUBLIC_SCHEME", config("broadcasting.connections.pusher.options.scheme", "https")) }}';
            var sid     = {{ Auth::guard('students')->id() }};
            if (!key || typeof Pusher === 'undefined') return;

            var useTLS = scheme === 'https';
            var opts   = { cluster: cluster, forceTLS: useTLS, disableStats: true };
            if (host) {
                opts.wsHost           = host;
                opts.wsPort           = port;
                opts.wssPort          = port;
                opts.enabledTransports = useTLS ? ['ws', 'wss'] : ['ws'];
            }
            var pusher = new Pusher(key, opts);
            var ch = pusher.subscribe('student-notifications-' + sid);

            ch.bind('student.notification', function (data) {
                var type = data.type || '';

                // Always play notification sound
                _playStudentChime(type === 'payment_status_updated' && data.status === 'approved');

                // Always update unread badge in sidebar
                _incrementFinBadge();

                // Show toast for new invoice or payment status
                if (type === 'new_invoice' || type === 'payment_status_updated') { _showStudentToast(data); }
            });

            window.studentPusher  = pusher;
            window.studentChannel = ch;

            function _playStudentChime(ok) {
                try {
                    var AC = window.AudioContext || window.webkitAudioContext;
                    if (!AC) return;
                    var ctx = new AC();
                    var freqs = ok ? [659, 784, 880] : [440, 392];
                    freqs.forEach(function (f, i) {
                        setTimeout(function () {
                            var osc = ctx.createOscillator();
                            var g   = ctx.createGain();
                            osc.connect(g); g.connect(ctx.destination);
                            osc.type = 'sine';
                            osc.frequency.setValueAtTime(f, ctx.currentTime);
                            g.gain.setValueAtTime(0.25, ctx.currentTime);
                            g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.18);
                            osc.start(ctx.currentTime);
                            osc.stop(ctx.currentTime + 0.18);
                        }, i * 160);
                    });
                } catch (e) {}
            }

            function _incrementFinBadge() {
                var badge = document.querySelector('[data-fin-unread-badge]');
                if (!badge) return;
                var n = (parseInt(badge.textContent, 10) || 0) + 1;
                badge.textContent = n;
                badge.style.display = '';
            }

            function _showStudentToast(data) {
                var t = document.createElement('div');
                t.style.cssText = 'position:fixed;bottom:24px;left:24px;z-index:99999;'
                    + 'min-width:300px;max-width:380px;'
                    + 'background:linear-gradient(135deg,#1e3a5f,#1e3a5fcc);'
                    + 'border:1px solid #3b82f6;border-left:4px solid #3b82f6;'
                    + 'border-radius:12px;padding:16px;color:#fff;'
                    + 'font-family:Cairo,sans-serif;direction:rtl;text-align:right;'
                    + 'box-shadow:0 8px 32px rgba(0,0,0,.4);'
                    + 'opacity:0;transform:translateX(-60px);'
                    + 'transition:opacity .35s ease,transform .35s ease;';
                var amount = data.total_due || data.remaining;
                t.innerHTML = '<div style="font-weight:700;font-size: 1.15rem;margin-bottom:4px;">📋 '
                    + _esc(data.title || 'فاتورة جديدة') + '</div>'
                    + (data.message ? '<div style="font-size: 1.02rem;opacity:.9;margin-bottom:6px;line-height:1.4;">'
                        + _esc(data.message) + '</div>' : '')
                    + (amount ? '<div style="font-size: 1.08rem;font-weight:700;background:rgba(255,255,255,.15);'
                        + 'border-radius:6px;padding:3px 10px;display:inline-block;">₪ '
                        + parseFloat(amount).toFixed(2) + '</div>' : '');
                document.body.appendChild(t);
                requestAnimationFrame(function () {
                    t.style.opacity = '1'; t.style.transform = 'translateX(0)';
                });
                setTimeout(function () {
                    t.style.opacity = '0'; t.style.transform = 'translateX(-60px)';
                    setTimeout(function () { if (t.parentNode) t.remove(); }, 400);
                }, 7000);
            }

            function _esc(s) {
                return String(s).replace(/[&<>"']/g, function(m) {
                    return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m];
                });
            }
        })();
        </script>
        @endauth
        @endif
        {{-- ──────────────────────────────────────────────────────────────── --}}

        @yield('js')
    </body>
</html>
