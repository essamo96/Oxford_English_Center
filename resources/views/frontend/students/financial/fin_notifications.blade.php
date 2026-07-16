@extends('frontend.layouts.dashboard')
@section('title', 'الإشعارات المالية')

@section('css')
<style>
.fin-page { color: var(--d-text, #1e293b); }

.fin-hero {
    background: linear-gradient(135deg, #0E2250 0%, #1a4a8a 100%);
    border-radius: 16px; padding: 28px 32px; color: #fff;
    margin-bottom: 24px; display: flex; align-items: center; gap: 16px;
}
.fin-hero__icon { font-size: 2.4rem; opacity: .85; }
.fin-hero h2 { font-size: 1.6rem; font-weight: 800; margin: 0 0 3px; color: #fff !important; }
.fin-hero p  { margin: 0; opacity: .85; font-size: 1.08rem; color: #fff !important; }

.notif-card {
    background: var(--d-card, #fff);
    border: 1px solid var(--d-border, #e2e8f0);
    border-radius: 14px; overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,.05);
}
.notif-actions {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 14px;
}
.unread-chip {
    background: #fee2e2; color: #991b1b;
    border-radius: 20px; padding: 4px 12px;
    font-size: 0.98rem; font-weight: 700;
    display: inline-flex; align-items: center; gap: 5px;
}
.btn-mark-all {
    background: var(--d-card-2, #f3f4f6);
    border: 1px solid var(--d-border, #e2e8f0);
    border-radius: 8px; padding: 6px 14px;
    font-size: 1.03rem; cursor: pointer; color: var(--d-text, #374151);
    display: inline-flex; align-items: center; gap: 6px;
    transition: background .15s;
}
.btn-mark-all:hover { background: #1a4a8a; color: #fff; border-color: #1a4a8a; }

.notif-item {
    display: flex; gap: 14px; align-items: flex-start;
    padding: 18px 22px; border-bottom: 1px solid var(--d-border, #f0f0f0);
    transition: background .18s;
}
.notif-item:last-child { border-bottom: none; }
.notif-item.unread { background: rgba(26,74,138,.05); }
.notif-item:hover  { background: var(--d-hover, rgba(0,0,0,.03)); }

.notif-icon {
    width: 44px; height: 44px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; flex-shrink: 0;
}
.notif-icon.approved  { background: #d1fae5; color: #059669; }
.notif-icon.rejected  { background: #fee2e2; color: #dc2626; }
.notif-icon.invoice   { background: #dbeafe; color: #2563eb; }
.notif-icon.submitted { background: #f3e8ff; color: #7c3aed; }
.notif-icon.default   { background: var(--d-card-2,#f3f4f6); color: var(--d-muted,#6b7280); }

.notif-body { flex: 1; }
.notif-title { font-weight: 700; color: var(--d-heading,#0a3258); margin-bottom: 3px; font-size: 1.12rem; }
.notif-msg   { color: var(--d-text,#4b5563); font-size: 1.04rem; margin-bottom: 5px; line-height: 1.5; }
.notif-amount {
    display: inline-flex; align-items: center; gap: 5px;
    background: #d1fae5; color: #065f46;
    border-radius: 6px; padding: 2px 10px; font-size: 1rem; font-weight: 700; margin-bottom: 5px;
}
.notif-time  { color: var(--d-muted,#94a3b8); font-size: 0.96rem; display: flex; align-items: center; gap: 5px; }

.unread-dot  { width: 9px; height: 9px; border-radius: 50%; background: #2563eb; flex-shrink: 0; margin-top: 10px; }
.btn-read {
    background: none; border: 1px solid var(--d-border,#e2e8f0);
    border-radius: 6px; padding: 3px 8px; font-size: 0.96rem;
    cursor: pointer; color: var(--d-muted,#64748b); display: flex; align-items: center; gap: 3px;
    white-space: nowrap; margin-top: 4px; transition: background .15s;
}
.btn-read:hover { background: #1a4a8a; color: #fff; border-color: #1a4a8a; }

.fin-empty { text-align: center; padding: 64px 24px; }
.fin-empty i { font-size: 3.7rem; color: var(--d-muted,#94a3b8); display: block; margin-bottom: 14px; }
.fin-empty p { color: var(--d-muted,#64748b); font-size: 1.13rem; }

.fin-pagination { margin-top: 16px; display: flex; justify-content: center; }
</style>
@endsection

@section('content')
<div class="fin-page container-fluid px-4 py-4">

    <div class="fin-hero">
        <div class="fin-hero__icon"><i class="bi bi-bell-fill"></i></div>
        <div>
            <h2>الإشعارات المالية</h2>
            <p>متابعة حالة طلبات الدفع والفواتير الجديدة من الإدارة</p>
        </div>
    </div>

    @if(session('fin_success'))
        <div class="alert alert-success alert-dismissible fade in" style="margin-bottom:16px;">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            <i class="bi bi-check-circle me-2"></i>{{ session('fin_success') }}
        </div>
    @endif

    <div class="notif-actions">
        @if($unreadCount > 0)
            <span class="unread-chip"><i class="bi bi-circle-fill" style="font-size: 0.7rem;"></i>{{ $unreadCount }} غير مقروء</span>
            <form action="{{ route('student.financial.mark-all-read') }}" method="POST" style="margin:0;">
                @csrf
                <button type="submit" class="btn-mark-all">
                    <i class="bi bi-check2-all"></i>تعليم الكل كمقروء
                </button>
            </form>
        @else
            <span style="color:var(--d-muted,#94a3b8);font-size: 1.04rem;"><i class="bi bi-check2-all me-1"></i>جميع الإشعارات مقروءة</span>
            <span></span>
        @endif
    </div>

    <div class="notif-card">
        @forelse($notifications as $notif)
        @php
            $data    = $notif->data;
            $type    = $data['type'] ?? 'default';
            $title   = $data['title'] ?? 'إشعار';
            $message = $data['message'] ?? '';
            $amount  = $data['amount_paid'] ?? ($data['total_due'] ?? null);
            $isUnread = is_null($notif->read_at);

            $iconClass = match(true) {
                $type === 'payment_status_updated' && ($data['status'] ?? '') === 'approved' => 'approved',
                $type === 'payment_status_updated' && ($data['status'] ?? '') === 'rejected' => 'rejected',
                $type === 'new_invoice'             => 'invoice',
                $type === 'student_payment_submitted' => 'submitted',
                default => 'default'
            };
            $iconName = match($iconClass) {
                'approved'  => 'check-circle-fill',
                'rejected'  => 'x-circle-fill',
                'invoice'   => 'receipt-cutoff',
                'submitted' => 'send-fill',
                default     => 'bell-fill'
            };
        @endphp
        <div class="notif-item {{ $isUnread ? 'unread' : '' }}" id="notif-{{ $notif->id }}">
            <div class="notif-icon {{ $iconClass }}">
                <i class="bi bi-{{ $iconName }}"></i>
            </div>
            <div class="notif-body">
                <div class="notif-title">{{ $title }}</div>
                @if($message)
                    <div class="notif-msg">{{ $message }}</div>
                @endif
                @if($amount)
                    <div class="notif-amount"><i class="bi bi-currency-exchange"></i>₪ {{ number_format((float)$amount, 2) }}</div>
                @endif
                {{-- Extra invoice details --}}
                @if(!empty($data['program']) || !empty($data['group_name']))
                <div style="font-size: 1rem;color:var(--d-muted,#64748b);margin-bottom:4px;">
                    @if(!empty($data['program']))<span style="margin-left:8px;"><i class="bi bi-mortarboard me-1"></i>{{ $data['program'] }}</span>@endif
                    @if(!empty($data['group_name']))<span><i class="bi bi-people me-1"></i>{{ $data['group_name'] }}</span>@endif
                </div>
                @endif
                <div class="notif-time">
                    <i class="bi bi-clock"></i>
                    {{ optional($notif->created_at)->diffForHumans() }}
                    &nbsp;·&nbsp;
                    {{ optional($notif->created_at)->format('Y-m-d H:i') }}
                </div>
            </div>
            @if($isUnread)
            <div style="display:flex;flex-direction:column;align-items:center;gap:6px;">
                <div class="unread-dot"></div>
                <button type="button" class="btn-read js-mark-read" data-id="{{ $notif->id }}" title="تعليم كمقروء">
                    <i class="bi bi-check2"></i>
                </button>
            </div>
            @endif
        </div>
        @empty
        <div class="fin-empty">
            <i class="bi bi-bell-slash"></i>
            <p>لا توجد إشعارات مالية بعد.</p>
        </div>
        @endforelse
    </div>

    <div class="fin-pagination">
        {{ $notifications->links() }}
    </div>
</div>
@endsection

@section('js')
<script>
(function () {
    /* mark single read */
    document.querySelectorAll('.js-mark-read').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id   = this.dataset.id;
            var item = document.getElementById('notif-' + id);
            var self = this;
            fetch('{{ url("student/financial/notifications") }}/' + id + '/read', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' }
            }).then(function () {
                if (item) item.classList.remove('unread');
                self.closest('div').remove();
                var chip = document.querySelector('.unread-chip');
                if (chip) {
                    var n = parseInt(chip.textContent) - 1;
                    if (n <= 0) chip.remove(); else chip.childNodes[chip.childNodes.length-1].textContent = ' ' + n + ' غير مقروء';
                }
            });
        });
    });

    /* Pusher real-time — prepend notification card without page reload */
    @auth('students')
    (function () {
        var ch = window.studentChannel;
        if (!ch && typeof Pusher !== 'undefined') {
            var _host = '{{ config("broadcasting.connections.pusher.options.host", "") }}';
            var _port = {{ (int) config("broadcasting.connections.pusher.options.port", 443) }};
            var _scheme = '{{ config("broadcasting.connections.pusher.options.scheme", "https") }}';
            var _opts = { cluster: '{{ config("broadcasting.connections.pusher.options.cluster", "mt1") }}', forceTLS: _scheme === 'https', disableStats: true };
            if (_host) { _opts.wsHost = _host; _opts.wsPort = _port; _opts.wssPort = _port; _opts.enabledTransports = [_scheme === 'https' ? 'wss' : 'ws']; }
            var p = new Pusher('{{ config("broadcasting.connections.pusher.key") }}', _opts);
            ch = p.subscribe('student-notifications-{{ Auth::guard("students")->id() }}');
        }
        if (ch) {
            ch.bind('student.notification', function (data) {
                prependNotifCard(data);
                // Update unread chip
                var chip = document.querySelector('.unread-chip');
                if (chip) {
                    var n = (parseInt(chip.textContent, 10) || 0) + 1;
                    chip.childNodes[chip.childNodes.length - 1].textContent = ' ' + n + ' غير مقروء';
                } else {
                    var actionsEl = document.querySelector('.notif-actions');
                    if (actionsEl) {
                        var span = document.createElement('span');
                        span.className = 'unread-chip';
                        span.innerHTML = '<i class="bi bi-circle-fill" style="font-size: 0.7rem;"></i> 1 غير مقروء';
                        actionsEl.insertBefore(span, actionsEl.firstChild);
                    }
                }
            });
        }

        function _esc(s) {
            return String(s || '').replace(/[&<>"']/g, function(m) {
                return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m];
            });
        }

        function prependNotifCard(data) {
            var type    = data.type || 'default';
            var title   = data.title || 'إشعار جديد';
            var message = data.message || '';
            var amount  = data.amount_paid || data.total_due || null;
            var status  = data.status || '';

            var iconMap = {
                'payment_status_updated': status === 'approved' ? 'approved' : 'rejected',
                'new_invoice': 'invoice',
                'student_payment_submitted': 'submitted'
            };
            var iconClass = iconMap[type] || 'default';
            var iconNameMap = { approved:'check-circle-fill', rejected:'x-circle-fill', invoice:'receipt-cutoff', submitted:'send-fill', 'default':'bell-fill' };
            var iconName = iconNameMap[iconClass] || 'bell-fill';

            var amountHtml = amount
                ? '<div class="notif-amount"><i class="bi bi-currency-exchange"></i>₪ ' + parseFloat(amount).toFixed(2) + '</div>'
                : '';
            var progHtml = '';
            if (data.program || data.group_name) {
                progHtml = '<div style="font-size: 1rem;color:var(--d-muted,#64748b);margin-bottom:4px;">';
                if (data.program)    progHtml += '<span style="margin-left:8px;"><i class="bi bi-mortarboard me-1"></i>' + _esc(data.program) + '</span>';
                if (data.group_name) progHtml += '<span><i class="bi bi-people me-1"></i>' + _esc(data.group_name) + '</span>';
                progHtml += '</div>';
            }

            var now = new Date();
            var timeStr = now.getFullYear() + '-' + String(now.getMonth()+1).padStart(2,'0') + '-' + String(now.getDate()).padStart(2,'0') + ' ' + String(now.getHours()).padStart(2,'0') + ':' + String(now.getMinutes()).padStart(2,'0');

            var html = '<div class="notif-item unread" style="border-top:2px solid #3b82f6;">'
                + '<div class="notif-icon ' + iconClass + '"><i class="bi bi-' + iconName + '"></i></div>'
                + '<div class="notif-body">'
                + '<div class="notif-title">' + _esc(title) + '</div>'
                + (message ? '<div class="notif-msg">' + _esc(message) + '</div>' : '')
                + amountHtml
                + progHtml
                + '<div class="notif-time"><i class="bi bi-clock"></i> الآن · ' + timeStr + '</div>'
                + '</div>'
                + '<div style="display:flex;flex-direction:column;align-items:center;gap:6px;">'
                + '<div class="unread-dot"></div>'
                + '</div>'
                + '</div>';

            var container = document.querySelector('.notif-card');
            if (!container) return;
            // Remove "لا توجد إشعارات" empty state if present
            var empty = container.querySelector('.fin-empty');
            if (empty) empty.remove();
            container.insertAdjacentHTML('afterbegin', html);
        }
    })();
    @endauth
})();
</script>
@endsection
