@extends('frontend.layouts.dashboard')
@section('title', 'السجل المالي')

@section('css')
<style>
/* ── theme-aware tokens ── */
.fin-page { color: var(--d-text, #1e293b); }

.fin-hero {
    background: linear-gradient(135deg, #0E2250 0%, #1a4a8a 100%);
    border-radius: 16px;
    padding: 28px 32px;
    color: #fff;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 16px;
}
.fin-hero__icon { font-size: 2.4rem; opacity: .85; }
.fin-hero h2   { font-size: 1.6rem; font-weight: 800; margin: 0 0 3px; color: #fff !important; }
.fin-hero p    { margin: 0; opacity: .85; font-size: 1.08rem; color: #fff !important; }

.fin-card {
    background: var(--d-card, #fff);
    border: 1px solid var(--d-border, #e2e8f0);
    border-radius: 14px;
    overflow: hidden;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,.06);
}
.fin-card-head {
    padding: 16px 24px;
    border-bottom: 1px solid var(--d-border, #e2e8f0);
    display: flex;
    align-items: center;
    gap: 10px;
    background: var(--d-card-2, #f8fafc);
}
.fin-card-head i   { font-size: 1.45rem; color: #1a4a8a; }
.fin-card-head h5  { margin: 0; font-weight: 700; color: var(--d-heading, #0a3258); font-size: 1.2rem; }
.fin-card-head .badge-count {
    margin-left: auto;
    background: #1a4a8a;
    color: #fff;
    border-radius: 20px;
    padding: 2px 12px;
    font-size: 0.98rem;
    font-weight: 600;
}

/* table */
.fin-table { width: 100%; }
.fin-table thead th {
    background: var(--d-card-2, #f8fafc);
    color: var(--d-muted, #64748b);
    font-size: 1.02rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    padding: 12px 16px;
    border-bottom: 1px solid var(--d-border, #e2e8f0);
    white-space: nowrap;
}
.fin-table tbody td {
    padding: 14px 16px;
    vertical-align: middle;
    border-bottom: 1px solid var(--d-border, #e2e8f0);
    color: var(--d-text, #1e293b);
    font-size: 1.15rem;
}
.fin-table tbody tr:last-child td { border-bottom: none; }
.fin-table tbody tr:hover { background: var(--d-hover, rgba(0,0,0,.03)); }

/* receipt thumbnail */
.receipt-thumb {
    width: 52px; height: 52px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid var(--d-border, #e2e8f0);
    cursor: zoom-in;
    transition: transform .15s, border-color .15s;
    display: block;
}
.receipt-thumb:hover { transform: scale(1.1); border-color: #1a4a8a; }
.receipt-pdf-btn {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 10px; border-radius: 7px;
    background: var(--d-card-2, #f0f4f8);
    border: 1px solid var(--d-border, #e2e8f0);
    color: var(--d-text, #334155);
    font-size: 1rem; cursor: pointer; text-decoration: none;
}
.receipt-pdf-btn:hover { background: #1a4a8a; color: #fff; border-color: #1a4a8a; }

/* badges */
.badge-verified { background: #d1fae5; color: #065f46; border-radius: 20px; padding: 4px 12px; font-size: 0.98rem; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; }
.badge-type     { border-radius: 6px; padding: 3px 9px; font-size: 0.95rem; font-weight: 600; }
.badge-payment  { background: #dbeafe; color: #1d4ed8; }
.badge-refund   { background: #fee2e2; color: #b91c1c; }
.badge-credit   { background: #e0f2fe; color: #0369a1; }
.badge-adjust   { background: #fef3c7; color: #92400e; }

.amount-positive { color: #059669; font-weight: 700; font-size: 1.15rem; }
.amount-negative { color: #dc2626; font-weight: 700; font-size: 1.15rem; }

/* empty state */
.fin-empty { text-align: center; padding: 64px 24px; }
.fin-empty i { font-size: 3.7rem; color: var(--d-muted, #94a3b8); display: block; margin-bottom: 14px; }
.fin-empty p { color: var(--d-muted, #64748b); font-size: 1.13rem; }

/* receipt zoom overlay is created by JS and appended to <body> */
</style>
@endsection

@section('content')
<div class="fin-page container-fluid px-4 py-4">

    {{-- Hero --}}
    <div class="fin-hero">
        <div class="fin-hero__icon"><i class="bi bi-clock-history"></i></div>
        <div>
            <h2>السجل المالي</h2>
            <p>جميع المدفوعات المؤكدة من قبل الإدارة</p>
        </div>
    </div>

    @if(session('fin_success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
            <i class="bi bi-check-circle me-2"></i>{{ session('fin_success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <div class="fin-card">
        <div class="fin-card-head">
            <i class="bi bi-receipt-cutoff"></i>
            <h5>المعاملات المالية المؤكدة</h5>
            <span class="badge-count">{{ $rows->count() }} معاملة</span>
        </div>

        @if($rows->isEmpty())
            <div class="fin-empty">
                <i class="bi bi-inbox"></i>
                <p>لا توجد معاملات مالية مؤكدة حتى الآن.</p>
            </div>
        @else
        <div class="table-responsive">
            <table class="fin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>التاريخ</th>
                        <th>البرنامج / المجموعة</th>
                        <th>النوع</th>
                        <th>المبلغ</th>
                        <th>طريقة الدفع</th>
                        <th>مؤكد بواسطة</th>
                        <th>إيصال الدفع</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $i => $row)
                    <tr>
                        <td style="color:var(--d-muted,#94a3b8);font-size: 0.98rem;">{{ $i + 1 }}</td>
                        <td>
                            <div style="font-weight:600;font-size: 1.07rem;">{{ optional($row->created_at)->format('Y-m-d') }}</div>
                            <div style="color:var(--d-muted,#94a3b8);font-size: 0.96rem;">{{ optional($row->created_at)->format('h:i A') }}</div>
                        </td>
                        <td>
                            @if($row->group)
                                <div style="font-weight:700;color:#1a4a8a;font-size: 1.02rem;">{{ $row->group->program?->title ?? '' }}</div>
                                <div style="color:var(--d-muted,#64748b);font-size: 0.98rem;">{{ $row->group->name }}</div>
                            @else
                                <span style="color:var(--d-muted,#94a3b8);font-style:italic;font-size: 1.02rem;">{{ $row->student_paid_type ?? 'رسوم عامة' }}</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $typeMap = [
                                    'payment'    => ['badge-payment',  'دفعة'],
                                    'refund'     => ['badge-refund',   'استرداد'],
                                    'credit'     => ['badge-credit',   'رصيد'],
                                    'adjustment' => ['badge-adjust',   'تعديل'],
                                ];
                                [$tc, $tl] = $typeMap[$row->transaction_type ?? 'payment'] ?? ['badge-adjust', '—'];
                            @endphp
                            <span class="badge-type {{ $tc }}">{{ $tl }}</span>
                        </td>
                        <td>
                            @php $amt = (float)$row->transaction_amount; @endphp
                            <span class="{{ $amt >= 0 ? 'amount-positive' : 'amount-negative' }}">
                                ₪ {{ number_format(abs($amt), 2) }}
                            </span>
                        </td>
                        <td style="font-size: 1.02rem;color:var(--d-muted,#64748b);">{{ $row->paymentMethod?->name ?? '—' }}</td>
                        <td style="font-size: 1.02rem;color:var(--d-muted,#64748b);">{{ $row->verifiedBy?->name ?? '—' }}</td>
                        <td>
                            @if($row->payment_receipt)
                                @php
                                    $receiptUrl = asset('uploads/' . $row->payment_receipt);
                                    $ext = strtolower(pathinfo($row->payment_receipt, PATHINFO_EXTENSION));
                                    $isImg = in_array($ext, ['jpg','jpeg','png','webp','gif']);
                                @endphp
                                @if($isImg)
                                    <img src="{{ $receiptUrl }}"
                                         class="receipt-thumb js-receipt-zoom"
                                         data-url="{{ $receiptUrl }}"
                                         alt="إيصال"
                                         onerror="this.outerHTML='<span style=\'color:var(--d-muted,#94a3b8);font-size: 0.96rem;\' title=\'الملف غير موجود\'><i class=\'bi bi-image-slash\'></i> محذوف</span>'">
                                @else
                                    <a href="{{ $receiptUrl }}" target="_blank" class="receipt-pdf-btn">
                                        <i class="bi bi-file-earmark-pdf"></i> عرض PDF
                                    </a>
                                @endif
                            @else
                                <span style="color:var(--d-muted,#94a3b8);font-size: 1rem;">لا يوجد</span>
                            @endif
                        </td>
                        <td><span class="badge-verified"><i class="bi bi-patch-check-fill"></i>مؤكد</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

@endsection

@section('js')
<script>
(function () {
    /* Build overlay and append directly to <body> to avoid any CSS stacking-context
       issues caused by overflow:hidden or transform on ancestor elements. */
    var overlay = document.createElement('div');
    overlay.style.cssText =
        'display:none;position:fixed;inset:0;z-index:999999;'
        + 'background:rgba(0,0,0,.85);align-items:center;justify-content:center;'
        + 'cursor:zoom-out;animation:rcptFadeIn .18s ease;';
    overlay.innerHTML =
        '<style>@keyframes rcptFadeIn{from{opacity:0}to{opacity:1}}'
        + '#_rcptImg{max-width:92vw;max-height:88vh;border-radius:12px;'
        + 'box-shadow:0 8px 48px rgba(0,0,0,.6);cursor:default;object-fit:contain;}'
        + '#_rcptClose{position:absolute;top:18px;right:22px;'
        + 'background:rgba(255,255,255,.18);border:none;color:#fff;'
        + 'font-size: 1.8rem;width:40px;height:40px;border-radius:50%;cursor:pointer;'
        + 'display:flex;align-items:center;justify-content:center;transition:background .15s;}'
        + '#_rcptClose:hover{background:rgba(255,255,255,.35);}</style>'
        + '<button id="_rcptClose"><i class="bi bi-x"></i></button>'
        + '<img id="_rcptImg" src="" alt="إيصال">';
    document.body.appendChild(overlay);

    var img      = overlay.querySelector('#_rcptImg');
    var closeBtn = overlay.querySelector('#_rcptClose');

    function openReceipt(url) {
        img.src = url;
        overlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeReceipt() {
        overlay.style.display = 'none';
        document.body.style.overflow = '';
        img.src = '';
    }

    document.querySelectorAll('.js-receipt-zoom').forEach(function (el) {
        el.addEventListener('click', function () { openReceipt(this.dataset.url); });
    });
    closeBtn.addEventListener('click', closeReceipt);
    overlay.addEventListener('click', function (e) { if (e.target === overlay) closeReceipt(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeReceipt(); });
})();
</script>
@endsection
