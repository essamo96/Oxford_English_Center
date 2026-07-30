@extends('admin.layout.master')

@section('title')
    مراقبة محادثات المجموعات
@stop

@section('page-title')
    مراقبة محادثات المجموعات
@stop

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">مراقبة المحادثات</li>
@stop

@section('css')
    <link href="{{ asset('assets/oxford/css/group-chat.css') }}?v={{ filemtime(public_path('assets/oxford/css/group-chat.css')) }}" rel="stylesheet" />
@stop

@section('page-content')

    <div class="row g-5 mb-6">
        <div class="col-md-4">
            <div class="card bg-light-success shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <i class="ki-duotone ki-messages fs-2hx text-success me-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                    <div>
                        <div class="fs-2 fw-bold text-gray-900">{{ number_format($totalChats) }}</div>
                        <div class="fs-7 text-muted">إجمالي الرسائل في كل المجموعات</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light-primary shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <i class="ki-duotone ki-people fs-2hx text-primary me-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                    <div>
                        <div class="fs-2 fw-bold text-gray-900">{{ $groups->count() }}</div>
                        <div class="fs-7 text-muted">عدد المجموعات</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light-danger shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <i class="bi bi-bell-fill fs-2hx text-danger me-4"></i>
                    <div>
                        <div class="fs-2 fw-bold text-gray-900" id="gc_total_unread">{{ number_format($totalUnread) }}</div>
                        <div class="fs-7 text-muted">رسائل غير مقروءة</div>
                    </div>
                    <span class="gc-live-dot ms-auto" title="المراقبة اللحظية مُفعّلة"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="card-title">المجموعات — مرتبة حسب آخر نشاط</h3>
            <div class="card-toolbar">
                <form method="GET" action="{{ route('group_chat.view') }}" class="d-flex align-items-center">
                    <input type="text" name="q" value="{{ $search }}" class="form-control form-control-sm w-250px me-2"
                           placeholder="ابحث باسم المجموعة..." />
                    <button type="submit" class="btn btn-sm btn-primary">بحث</button>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-5">
                @forelse($groups as $group)
                    <div class="col-md-6 col-xl-4" data-group-card="{{ $group->id }}">
                        <div class="card card-bordered gc-card h-100 {{ $group->unread_count > 0 ? 'gc-has-unread' : '' }}">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-start justify-content-between mb-4">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-45px symbol-circle me-3">
                                            <span class="symbol-label bg-light-success text-success fw-bold fs-4">
                                                {{ mb_substr($group->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <a href="{{ route('group_chat.show', $group->id) }}"
                                               class="fs-5 fw-bold text-gray-900 text-hover-primary d-block">{{ $group->name }}</a>
                                            <span class="text-muted fs-7">{{ $group->program->name ?? 'بدون برنامج' }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column align-items-end gap-2">
                                        {{-- Always rendered (hidden at zero) so the live updater has a
                                             node to write into without rebuilding the card. --}}
                                        <span class="gc-unread {{ $group->unread_count > 0 ? '' : 'd-none' }}"
                                              data-unread="{{ $group->id }}"
                                              title="رسائل غير مقروءة">{{ $group->unread_count }}</span>
                                        @if($group->chat_total > 0)
                                            <span class="badge badge-light-success">{{ $group->chat_total }}</span>
                                        @else
                                            <span class="badge badge-light">لا رسائل</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="separator separator-dashed mb-4"></div>

                                <div class="d-flex flex-wrap gap-4 mb-4 fs-7 text-muted">
                                    <span><i class="ki-duotone ki-teacher fs-6 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                        {{ $group->teacher->name ?? 'بدون معلم' }}</span>
                                    <span><i class="ki-duotone ki-people fs-6 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                        {{ $group->students_count }} طالب</span>
                                </div>

                                <div class="fs-8 text-muted mb-4">
                                    @if($group->chat_last_at)
                                        آخر رسالة: {{ $group->chat_last_at->diffForHumans() }}
                                    @else
                                        لم تبدأ المحادثة بعد
                                    @endif
                                </div>

                                <a href="{{ route('group_chat.show', $group->id) }}"
                                   class="btn btn-sm btn-light-primary mt-auto w-100">
                                    <i class="ki-duotone ki-messages fs-4 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                    فتح المحادثة
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info mb-0">لا توجد مجموعات مطابقة.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

<audio id="chat-alert-sound" preload="auto" style="display:none">
    <source src="{{ asset('assets/oxford/sound/facebook_chat.mp3') }}" type="audio/mpeg">
</audio>
@stop

@section('js')
<script>
(function () {
    var URL_UNREAD = "{{ route('group_chat.unread') }}";

    /**
     * Repaint every group's red unread badge from the server's counts.
     *
     * The badge node always exists (hidden at zero), so this only writes text and
     * toggles visibility — no card is rebuilt and no scroll position is lost.
     */
    function paintUnread(counts, total) {
        var anyNew = false;

        $('[data-unread]').each(function () {
            var $badge = $(this);
            var id  = String($badge.data('unread'));
            var n   = parseInt(counts[id] || 0, 10);
            var was = parseInt($badge.text(), 10) || 0;

            $badge.text(n).toggleClass('d-none', n === 0);
            $badge.closest('.gc-card').toggleClass('gc-has-unread', n > 0);

            if (n > was) { anyNew = true; }
        });

        $('#gc_total_unread').text(total);
        return anyNew;
    }

    function ding() {
        var el = document.getElementById('chat-alert-sound');
        if (!el) return;
        try {
            el.currentTime = 0;
            var p = el.play();
            if (p && p.catch) { p.catch(function () {}); }
        } catch (e) {}
    }

    var fetching = false;
    function refresh(withSound) {
        if (fetching) return;
        fetching = true;
        $.getJSON(URL_UNREAD, function (res) {
            if (res.state !== 1) return;
            var grew = paintUnread(res.counts || {}, res.total || 0);
            if (grew && withSound) { ding(); }
        }).always(function () { fetching = false; });
    }

    window.addEventListener('load', function () {
        var rt = window.OXFORD_RT || {};

        // Pusher drives the instant update; the interval is the fallback for a
        // dropped socket, matching how the conversation screen behaves.
        if (rt.key && typeof Pusher !== 'undefined') {
            var opts = { cluster: rt.cluster || 'mt1', encrypted: true, forceTLS: (rt.scheme !== 'http') };
            if (rt.host) {
                opts.wsHost = rt.host;
                opts.wsPort = rt.port;
                opts.wssPort = rt.port;
                opts.enabledTransports = ['ws', 'wss'];
            }
            try {
                var ch = new Pusher(rt.key, opts).subscribe('chat');
                // Any message in any group can change a badge here, so no filtering.
                ch.bind('send', function () { refresh(true); });
                ch.bind('group-state', function () { refresh(false); });
            } catch (e) { /* fall through to polling */ }
        }

        setInterval(function () { refresh(true); }, 15000);
    });
})();
</script>
@stop
