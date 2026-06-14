@extends('frontend.layouts.dashboard')
@section('title', 'الفواتير المستحقة')

@section('css')
<style>
.fin-page { color: var(--d-text, #1e293b); }

.fin-hero {
    background: linear-gradient(135deg, #0E2250 0%, #1a4a8a 100%);
    border-radius: 16px; padding: 28px 32px; color: #fff;
    margin-bottom: 24px; display: flex; align-items: center; gap: 16px;
}
.fin-hero__icon { font-size: 2.2rem; opacity: .85; }
.fin-hero h2 { font-size: 1.4rem; font-weight: 800; margin: 0 0 3px; color: #fff !important; }
.fin-hero p  { margin: 0; opacity: .85; font-size: .88rem; color: #fff !important; }

/* KPI cards */
.fin-kpis { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 22px; }
.fin-kpi {
    flex: 1; min-width: 140px;
    background: var(--d-card, #fff);
    border: 1px solid var(--d-border, #e2e8f0);
    border-radius: 14px; padding: 18px 20px; text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,.05);
}
.fin-kpi .kpi-val { font-size: 1.4rem; font-weight: 800; line-height: 1.2; }
.fin-kpi .kpi-lbl { font-size: .75rem; color: var(--d-muted, #64748b); margin-top: 4px; font-weight: 500; }
.kpi-blue  { color: #2563eb; }
.kpi-green { color: #059669; }
.kpi-red   { color: #dc2626; }
.kpi-purple{ color: #7c3aed; }

/* Invoice bucket */
.inv-bucket {
    background: var(--d-card, #fff);
    border: 1.5px solid var(--d-border, #e2e8f0);
    border-radius: 14px; padding: 20px 24px; margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
    transition: border-color .2s;
}
.inv-bucket.has-balance { border-color: #1a4a8a; }
.inv-bucket-title {
    font-weight: 700; font-size: .95rem;
    color: var(--d-heading, #0a3258); margin-bottom: 14px;
    display: flex; align-items: center; gap: 8px;
}
.inv-bucket-title i { color: #1a4a8a; }

.balance-grid { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 16px; }
.balance-cell {
    flex: 1; min-width: 110px;
    background: var(--d-card-2, #f8fafc);
    border: 1px solid var(--d-border, #e8edf2);
    border-radius: 10px; padding: 12px 14px; text-align: center;
}
.balance-cell .val { font-size: 1.05rem; font-weight: 800; }
.balance-cell .lbl { font-size: .72rem; color: var(--d-muted, #94a3b8); margin-top: 3px; font-weight: 500; }

/* pending review status */
.pending-review-status {
    background: linear-gradient(135deg, #fef9eb, #fef3c7);
    border: 1.5px solid #fbbf24; border-radius: 12px;
    padding: 14px 18px; margin-bottom: 12px;
    display: flex; align-items: center; gap: 12px;
}
.pending-review-status .prv-icon {
    width: 40px; height: 40px; border-radius: 50%;
    background: #fef3c7; border: 1.5px solid #fbbf24;
    display: flex; align-items: center; justify-content: center;
    color: #d97706; font-size: 1.1rem; flex-shrink: 0;
}
.pending-review-status .prv-body { flex: 1; }
.pending-review-status .prv-title { font-weight: 700; color: #92400e; font-size: .9rem; margin-bottom: 2px; }
.pending-review-status .prv-sub   { font-size: .78rem; color: #b45309; }
.btn-cancel-small {
    font-size: .75rem; padding: 4px 10px;
    border: 1px solid #dc2626; color: #dc2626;
    background: transparent; border-radius: 6px; cursor: pointer;
    transition: background .15s;
}
.btn-cancel-small:hover { background: #dc2626; color: #fff; }

/* pay confirm modal */
.pay-confirm-overlay {
    display: none; position: fixed; inset: 0; z-index: 1060;
    background: rgba(0,0,0,.55); align-items: center; justify-content: center;
}
.pay-confirm-overlay.active { display: flex; }
.pay-confirm-box {
    background: var(--d-card,#fff); border-radius: 14px;
    width: 100%; max-width: 420px; margin: 20px;
    box-shadow: 0 8px 40px rgba(0,0,0,.3);
    animation: slideUp .2s ease;
}
.pay-confirm-head {
    padding: 16px 20px; border-bottom: 1px solid var(--d-border,#e2e8f0);
    display: flex; align-items: center; gap: 10px;
}
.pay-confirm-head h5 { margin: 0; font-weight: 700; color: var(--d-heading,#0a3258); font-size: .95rem; }
.pay-confirm-body { padding: 18px 20px; }
.pay-confirm-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 7px 0; border-bottom: 1px solid var(--d-border,#f0f0f0); font-size: .88rem;
}
.pay-confirm-row:last-child { border-bottom: none; }
.pay-confirm-row .lbl { color: var(--d-muted,#6b7280); font-weight: 500; }
.pay-confirm-row .val { font-weight: 700; color: var(--d-heading,#0a3258); }
.pay-confirm-foot {
    padding: 14px 20px; border-top: 1px solid var(--d-border,#e2e8f0);
    display: flex; gap: 10px; justify-content: flex-end;
}

/* pay button */
.btn-pay {
    display: inline-flex; align-items: center; gap: 7px;
    background: #1a4a8a; color: #fff;
    border: none; border-radius: 9px; padding: 8px 18px;
    font-size: .85rem; font-weight: 600; cursor: pointer;
    transition: background .18s;
}
.btn-pay:hover { background: #0E2250; }
.badge-paid-full {
    display: inline-flex; align-items: center; gap: 6px;
    background: #d1fae5; color: #065f46;
    border-radius: 8px; padding: 6px 14px; font-size: .82rem; font-weight: 600;
}

/* submissions table */
.fin-card {
    background: var(--d-card, #fff);
    border: 1px solid var(--d-border, #e2e8f0);
    border-radius: 14px; overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,.05);
}
.fin-card-head {
    padding: 15px 22px; border-bottom: 1px solid var(--d-border, #e2e8f0);
    background: var(--d-card-2, #f8fafc);
    display: flex; align-items: center; gap: 10px;
}
.fin-card-head i { font-size: 1.2rem; color: #1a4a8a; }
.fin-card-head h5 { margin: 0; font-weight: 700; color: var(--d-heading, #0a3258); font-size: .95rem; }
.fin-table { width: 100%; }
.fin-table thead th {
    background: var(--d-card-2, #f8fafc);
    color: var(--d-muted, #64748b); font-size: .82rem;
    font-weight: 700; text-transform: uppercase; letter-spacing: .5px;
    padding: 10px 16px; border-bottom: 1px solid var(--d-border, #e2e8f0);
}
.fin-table tbody td {
    padding: 12px 16px; vertical-align: middle;
    border-bottom: 1px solid var(--d-border, #e2e8f0);
    color: var(--d-text, #1e293b); font-size: .95rem;
}
.fin-table tbody tr:last-child td { border-bottom: none; }
.fin-table tbody tr:hover { background: var(--d-hover, rgba(0,0,0,.025)); }

/* status badges */
.s-pending  { background:#fef3c7;color:#92400e;border-radius:20px;padding:3px 10px;font-size:.76rem;font-weight:600; }
.s-approved { background:#d1fae5;color:#065f46;border-radius:20px;padding:3px 10px;font-size:.76rem;font-weight:600; }
.s-rejected { background:#fee2e2;color:#991b1b;border-radius:20px;padding:3px 10px;font-size:.76rem;font-weight:600; }

.receipt-sm {
    width:42px;height:42px;object-fit:cover;
    border-radius:7px;border:1px solid var(--d-border,#e2e8f0);
    cursor:zoom-in;transition:transform .15s;
}
.receipt-sm:hover { transform:scale(1.12); }

.fin-empty { text-align:center;padding:52px 20px; }
.fin-empty i { font-size:3rem;color:var(--d-muted,#94a3b8);display:block;margin-bottom:12px; }
.fin-empty p { color:var(--d-muted,#64748b); }

/* Modal */
.fin-modal-backdrop {
    display:none;position:fixed;inset:0;z-index:1040;
    background:rgba(0,0,0,.5);
}
.fin-modal-backdrop.active { display:block; }
.fin-modal {
    display:none;position:fixed;z-index:1050;
    inset:0;align-items:center;justify-content:center;
}
.fin-modal.active { display:flex; }
.fin-modal-dialog {
    background:var(--d-card,#fff);border-radius:14px;
    width:100%;max-width:480px;margin:20px;
    box-shadow:0 8px 40px rgba(0,0,0,.2);
    animation:slideUp .22s ease;
}
@keyframes slideUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
.fin-modal-head {
    padding:18px 22px;border-bottom:1px solid var(--d-border,#e2e8f0);
    display:flex;align-items:center;gap:10px;
}
.fin-modal-head h5 { margin:0;font-weight:700;color:var(--d-heading,#0a3258);font-size:.97rem; }
.fin-modal-head .close-modal { margin-left:auto;background:none;border:none;font-size:1.3rem;cursor:pointer;color:var(--d-muted,#64748b);line-height:1; }
.fin-modal-body { padding:20px 22px; }
.fin-modal-foot { padding:14px 22px;border-top:1px solid var(--d-border,#e2e8f0);display:flex;gap:10px;justify-content:flex-end; }
.fin-input { width:100%;border:1.5px solid var(--d-border,#d1d5db);border-radius:8px;padding:9px 12px;font-size:.88rem;background:var(--d-card,#fff);color:var(--d-text,#1e293b);transition:border-color .15s; }
.fin-input:focus { outline:none;border-color:#1a4a8a; }
.fin-label { display:block;margin-bottom:5px;font-size:.83rem;font-weight:600;color:var(--d-text,#374151); }
.fin-form-group { margin-bottom:16px; }
.fin-hint { font-size:.76rem;color:var(--d-muted,#6b7280);margin-top:4px; }

/* receipt zoom overlay is appended to <body> via JS */

/* payment method buttons */
.pm-btn.selected {
    border-color: #1a4a8a !important;
    background: rgba(26,74,138,.08) !important;
    color: #1a4a8a !important;
    box-shadow: 0 0 0 3px rgba(26,74,138,.15);
}
</style>
@endsection

@section('content')
<div class="fin-page container-fluid px-4 py-4">

    <div class="fin-hero">
        <div class="fin-hero__icon"><i class="bi bi-receipt"></i></div>
        <div>
            <h2>الفواتير المستحقة</h2>
            <p>اعرض رصيدك وأرسل دفعاتك لمراجعة الإدارة</p>
        </div>
    </div>

    @if(session('fin_success'))
        <div class="alert alert-success alert-dismissible fade in">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            <i class="bi bi-check-circle me-2"></i>{{ session('fin_success') }}
        </div>
    @endif
    @if(session('fin_error'))
        <div class="alert alert-danger alert-dismissible fade in">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('fin_error') }}
        </div>
    @endif

    {{-- KPI summary --}}
    @if($allInvoices)
    <div class="fin-kpis">
        <div class="fin-kpi">
            <div class="kpi-val kpi-blue">₪ {{ number_format($allInvoices['grand_total_due'], 2) }}</div>
            <div class="kpi-lbl">إجمالي المستحق</div>
        </div>
        <div class="fin-kpi">
            <div class="kpi-val kpi-green">₪ {{ number_format($allInvoices['grand_total_paid'], 2) }}</div>
            <div class="kpi-lbl">إجمالي المدفوع</div>
        </div>
        <div class="fin-kpi">
            <div class="kpi-val kpi-red">₪ {{ number_format($allInvoices['grand_total_left'], 2) }}</div>
            <div class="kpi-lbl">إجمالي المتبقي</div>
        </div>
        @if($allInvoices['grand_total_credit'] > 0)
        <div class="fin-kpi">
            <div class="kpi-val kpi-purple">₪ {{ number_format($allInvoices['grand_total_credit'], 2) }}</div>
            <div class="kpi-lbl">رصيد إضافي</div>
        </div>
        @endif
    </div>

    {{-- Invoice buckets --}}
    @foreach($allInvoices['summary'] as $bucket)
    @php
        $bucketKey = $bucket['group_id'] ?? 'pre_group';
        $pending   = $pendingSubmissions[$bucketKey] ?? null;
        $hasBalance = $bucket['remaining'] > 0;
    @endphp
    <div class="inv-bucket {{ $hasBalance ? 'has-balance' : '' }}"
         data-bucket-key="{{ $bucketKey }}">
        <div class="inv-bucket-title">
            <i class="bi bi-folder2-open"></i>
            {{ $bucket['label'] }}
        </div>
        <div class="balance-grid" data-bucket-balance>
            <div class="balance-cell">
                <div class="val kpi-blue">₪ {{ number_format($bucket['total_fee'], 2) }}</div>
                <div class="lbl">المستحق الكلي</div>
            </div>
            <div class="balance-cell">
                <div class="val kpi-green" data-bucket-paid>₪ {{ number_format($bucket['paid'], 2) }}</div>
                <div class="lbl">المدفوع المؤكد</div>
            </div>
            <div class="balance-cell">
                <div class="val kpi-red" data-bucket-remaining>₪ {{ number_format($bucket['remaining'], 2) }}</div>
                <div class="lbl">المتبقي</div>
            </div>
            @if($bucket['credit'] > 0)
            <div class="balance-cell">
                <div class="val kpi-purple">₪ {{ number_format($bucket['credit'], 2) }}</div>
                <div class="lbl">رصيد إضافي</div>
            </div>
            @endif
        </div>

        @if($pending)
            <div class="pending-review-status">
                <div class="prv-icon"><i class="bi bi-hourglass-split"></i></div>
                <div class="prv-body">
                    <div class="prv-title">⏳ قيد التدقيق — بانتظار موافقة الإدارة</div>
                    <div class="prv-sub">المبلغ المُرسَل: ₪ {{ number_format($pending->amount_paid, 2) }} · تم الإرسال {{ optional($pending->created_at)->diffForHumans() }}</div>
                </div>
                <form action="{{ route('student.financial.cancel', $pending->id) }}" method="POST"
                      style="margin:0;" onsubmit="return confirm('هل تريد إلغاء طلب الدفع؟')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-cancel-small"><i class="bi bi-x"></i> إلغاء</button>
                </form>
            </div>
            <div data-bucket-action style="display:none;"></div>
        @elseif($hasBalance)
            <div data-bucket-action>
                <button type="button" class="btn-pay js-open-pay"
                        data-group-id="{{ $bucket['group_id'] ?? '' }}"
                        data-label="{{ addslashes($bucket['label']) }}"
                        data-remaining="{{ $bucket['remaining'] }}">
                    <i class="bi bi-credit-card"></i>دفع الفاتورة
                </button>
            </div>
        @else
            <div data-bucket-action>
                <span class="badge-paid-full"><i class="bi bi-check-circle-fill"></i>مدفوع بالكامل</span>
            </div>
        @endif
    </div>
    @endforeach

    @else
    <div class="fin-card">
        <div class="fin-empty">
            <i class="bi bi-inbox"></i>
            <p>لا توجد فواتير مرتبطة بحسابك حالياً.</p>
        </div>
    </div>
    @endif

    {{-- My submissions history --}}
    @if($mySubmissions->isNotEmpty())
    <div class="fin-card" style="margin-top:24px;">
        <div class="fin-card-head">
            <i class="bi bi-send-check"></i>
            <h5>طلبات الدفع المُرسَلة</h5>
        </div>
        <div class="table-responsive">
            <table class="fin-table">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>الفاتورة</th>
                        <th>المبلغ</th>
                        <th>الإيصال</th>
                        <th>الحالة</th>
                        <th>ملاحظة الإدارة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mySubmissions as $sub)
                    <tr>
                        <td style="color:var(--d-muted,#64748b);font-size:.8rem;">{{ optional($sub->created_at)->format('Y-m-d H:i') }}</td>
                        <td>{{ $sub->group?->name ?? 'خارج المجموعة' }}</td>
                        <td style="font-weight:700;color:#059669;">₪ {{ number_format($sub->amount_paid, 2) }}</td>
                        <td>
                            @php $ext = strtolower(pathinfo($sub->receipt_file, PATHINFO_EXTENSION)); @endphp
                            @if(in_array($ext, ['jpg','jpeg','png','webp','gif']))
                                <img src="{{ asset('uploads/'.$sub->receipt_file) }}"
                                     class="receipt-sm js-receipt-zoom"
                                     data-url="{{ asset('uploads/'.$sub->receipt_file) }}"
                                     alt="إيصال">
                            @else
                                <a href="{{ asset('uploads/'.$sub->receipt_file) }}" target="_blank"
                                   style="font-size:.8rem;color:#1a4a8a;display:inline-flex;gap:4px;align-items:center;">
                                    <i class="bi bi-file-earmark-pdf"></i> PDF
                                </a>
                            @endif
                        </td>
                        <td>
                            @php
                                $smap = ['pending'=>'s-pending','approved'=>'s-approved','rejected'=>'s-rejected'];
                                $slab = ['pending'=>'بانتظار المراجعة','approved'=>'مقبول','rejected'=>'مرفوض'];
                            @endphp
                            <span class="{{ $smap[$sub->status] ?? '' }}">{{ $slab[$sub->status] ?? $sub->status }}</span>
                        </td>
                        <td style="color:var(--d-muted,#64748b);font-size:.8rem;">{{ $sub->admin_notes ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

{{-- Pay Modal --}}
<div class="fin-modal-backdrop" id="payBackdrop"></div>
<div class="fin-modal" id="payModal">
    <div class="fin-modal-dialog" style="max-width:540px;">
        <div class="fin-modal-head">
            <i class="bi bi-credit-card" style="color:#1a4a8a;font-size:1.2rem;"></i>
            <h5>إرسال دفعة</h5>
            <button class="close-modal" id="closePayModal"><i class="bi bi-x"></i></button>
        </div>
        <form action="{{ route('student.financial.submit-payment') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="group_id" id="payGroupId">
            <input type="hidden" name="payment_method_id" id="payMethodId">

            <div class="fin-modal-body">
                {{-- Invoice label --}}
                <p id="payLabel" style="color:var(--d-muted,#64748b);font-size:.85rem;margin-bottom:16px;"></p>

                {{-- Step 1: Choose payment method --}}
                <div class="fin-form-group">
                    <label class="fin-label">طريقة الدفع <span style="color:#dc2626;">*</span></label>
                    <div id="pmethods" style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:4px;">
                        @foreach($paymentMethods as $pm)
                        @php $creds = is_string($pm->credentials) ? json_decode($pm->credentials, true) : (array)$pm->credentials; @endphp
                        <button type="button"
                                class="pm-btn"
                                data-id="{{ $pm->id }}"
                                data-creds="{{ json_encode($creds) }}"
                                data-name="{{ $pm->name }}"
                                data-img="{{ $pm->image ? asset('uploads/'.$pm->image) : '' }}"
                                style="display:flex;align-items:center;gap:8px;
                                       border:2px solid var(--d-border,#e2e8f0);
                                       border-radius:10px;padding:10px 14px;
                                       background:var(--d-card,#fff);cursor:pointer;
                                       font-size:.86rem;font-weight:600;
                                       color:var(--d-text,#1e293b);transition:all .18s;">
                            @if($pm->image)
                            <img src="{{ asset('uploads/'.$pm->image) }}" alt="{{ $pm->name }}"
                                 style="width:32px;height:32px;object-fit:contain;border-radius:6px;">
                            @else
                            <i class="bi bi-bank" style="font-size:1.3rem;color:#1a4a8a;"></i>
                            @endif
                            {{ $pm->name }}
                        </button>
                        @endforeach
                    </div>
                    <div id="pmError" style="display:none;color:#dc2626;font-size:.78rem;margin-top:4px;">
                        يرجى اختيار طريقة الدفع
                    </div>
                </div>

                {{-- Required amount banner --}}
                <div id="payRequiredBanner" style="display:none;background:#f0fdf4;border:1.5px solid #86efac;
                     border-radius:10px;padding:12px 16px;margin-bottom:14px;
                     display:flex;align-items:center;gap:10px;">
                    <i class="bi bi-cash-coin" style="color:#059669;font-size:1.3rem;"></i>
                    <div>
                        <div style="font-size:.78rem;color:#6b7280;font-weight:500;">المبلغ المستحق عليك</div>
                        <div id="payRequiredAmt" style="font-size:1.35rem;font-weight:800;color:#059669;letter-spacing:.5px;"></div>
                    </div>
                </div>

                {{-- Credentials box — shown after method selected --}}
                <div id="credsBox" style="display:none;background:linear-gradient(135deg,#0e2250,#1a4a8a);
                     border-radius:12px;padding:16px 20px;color:#fff;margin-bottom:16px;">
                    <div style="font-weight:700;font-size:1rem;margin-bottom:12px;display:flex;align-items:center;gap:8px;">
                        <i class="bi bi-info-circle-fill"></i>
                        <span>بيانات الحوالة — <span id="credsMethodName"></span></span>
                    </div>
                    <div id="credsContent" style="font-size:.95rem;line-height:2;"></div>
                    <div style="margin-top:12px;font-size:.82rem;opacity:.85;border-top:1px solid rgba(255,255,255,.2);padding-top:10px;">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        أرسل الحوالة على البيانات أعلاه ثم ارفع صورة الإيصال أدناه.
                    </div>
                </div>

                {{-- Amount --}}
                <div class="fin-form-group">
                    <label class="fin-label">المبلغ المُرسَل <span style="color:#dc2626;">*</span></label>
                    <div style="display:flex;align-items:center;gap:0;">
                        <span style="background:var(--d-card-2,#f8fafc);border:1.5px solid var(--d-border,#d1d5db);border-right:none;border-radius:8px 0 0 8px;padding:9px 12px;font-size:.88rem;color:var(--d-muted,#64748b);">₪</span>
                        <input type="number" name="amount_paid" id="payAmount" class="fin-input"
                               style="border-radius:0 8px 8px 0;font-size:1rem;font-weight:700;" step="0.01" min="0.01" required placeholder="0.00">
                    </div>
                    <div class="fin-hint" id="payRemaining" style="color:#dc2626;font-size:.8rem;margin-top:5px;"></div>
                </div>

                {{-- Receipt upload --}}
                <div class="fin-form-group">
                    <label class="fin-label">إيصال الحوالة <span style="color:#dc2626;">*</span></label>
                    <input type="file" name="receipt" class="fin-input" accept=".jpg,.jpeg,.png,.pdf" required style="padding:6px;">
                    <div class="fin-hint">الصيغ المقبولة: JPG, PNG, PDF — الحد الأقصى 5MB</div>
                </div>

                {{-- Notes --}}
                <div class="fin-form-group">
                    <label class="fin-label">ملاحظات للإدارة (اختياري)</label>
                    <textarea name="notes" class="fin-input" rows="2" placeholder="أي ملاحظات إضافية..."></textarea>
                </div>
            </div>

            <div class="fin-modal-foot">
                <button type="button" id="closePayModal2"
                        style="background:var(--d-card-2,#f3f4f6);border:1px solid var(--d-border,#e2e8f0);
                               border-radius:8px;padding:8px 18px;font-size:.87rem;cursor:pointer;
                               color:var(--d-text,#374151);">إلغاء</button>
                <button type="submit" id="paySubmitBtn" class="btn-pay">
                    <i class="bi bi-send"></i>إرسال الطلب
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Payment confirmation overlay --}}
<div class="pay-confirm-overlay" id="payConfirmOverlay">
    <div class="pay-confirm-box">
        <div class="pay-confirm-head">
            <i class="bi bi-shield-check" style="color:#059669;font-size:1.2rem;"></i>
            <h5>تأكيد إرسال الدفعة</h5>
        </div>
        <div class="pay-confirm-body">
            <p style="color:var(--d-muted,#6b7280);font-size:.85rem;margin-bottom:14px;">يرجى مراجعة تفاصيل الدفعة قبل الإرسال:</p>
            <div class="pay-confirm-row"><span class="lbl">طريقة الدفع</span><span class="val" id="cfmMethod">—</span></div>
            <div class="pay-confirm-row"><span class="lbl">المبلغ</span><span class="val" id="cfmAmount" style="color:#059669;">—</span></div>
            <div class="pay-confirm-row"><span class="lbl">الإيصال</span><span class="val" id="cfmFile" style="font-size:.8rem;max-width:200px;text-align:left;word-break:break-all;">—</span></div>
        </div>
        <div class="pay-confirm-foot">
            <button type="button" id="cfmCancel"
                    style="background:var(--d-card-2,#f3f4f6);border:1px solid var(--d-border,#e2e8f0);
                           border-radius:8px;padding:8px 18px;font-size:.87rem;cursor:pointer;color:var(--d-text,#374151);">
                تعديل
            </button>
            <button type="button" id="cfmConfirm" class="btn-pay">
                <i class="bi bi-send"></i> نعم، أرسل الطلب
            </button>
        </div>
    </div>
</div>

{{-- Receipt zoom overlay is built via JS and appended to <body> --}}

@endsection

@section('js')
<script>
(function () {
    /* ── Pay modal ── */
    var payModal    = document.getElementById('payModal');
    var payBackdrop = document.getElementById('payBackdrop');

    /* ── Payment method selection ── */
    var credsBox    = document.getElementById('credsBox');
    var credsName   = document.getElementById('credsMethodName');
    var credsContent= document.getElementById('credsContent');
    var payMethodId = document.getElementById('payMethodId');
    var pmError     = document.getElementById('pmError');

    var credLabels = {
        mobile:  'رقم الجوال',
        account: 'رقم الحساب',
        iban:    'رقم IBAN',
        name:    'اسم الحساب',
        bank:    'اسم البنك',
        branch:  'الفرع',
        note:    'ملاحظة'
    };

    document.querySelectorAll('.pm-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            // deselect others
            document.querySelectorAll('.pm-btn').forEach(function (b) { b.classList.remove('selected'); });
            this.classList.add('selected');

            var id    = this.dataset.id;
            var name  = this.dataset.name;
            var creds = {};
            try { creds = JSON.parse(this.dataset.creds || '{}'); } catch(e){}

            payMethodId.value = id;
            pmError.style.display = 'none';

            // Build credentials HTML
            var keys = Object.keys(creds).filter(function(k){ return creds[k]; });
            if (keys.length) {
                var html = '';
                keys.forEach(function (k) {
                    var label = credLabels[k] || k;
                    var val   = creds[k];
                    html += '<div style="display:flex;justify-content:space-between;align-items:center;'
                          + 'border-bottom:1px solid rgba(255,255,255,.12);padding:7px 0;gap:8px;">'
                          + '<span style="opacity:.8;font-size:.9rem;">' + label + '</span>'
                          + '<div style="display:flex;align-items:center;gap:8px;">'
                          + '<strong style="font-size:1rem;letter-spacing:.5px;">' + val + '</strong>'
                          + '<button type="button" onclick="copyCredVal(this,\'' + val.replace(/'/g,"\\'") + '\')" '
                          + 'style="background:rgba(255,255,255,.18);border:none;border-radius:5px;'
                          + 'padding:2px 7px;cursor:pointer;color:#fff;font-size:.78rem;white-space:nowrap;'
                          + 'transition:background .15s;" title="نسخ">'
                          + '<i class="bi bi-copy"></i></button>'
                          + '</div>'
                          + '</div>';
                });
                credsContent.innerHTML = html;
                credsName.textContent = name;
                credsBox.style.display = 'block';
            } else {
                credsBox.style.display = 'none';
            }
        });
    });

    /* ── Copy credential value ── */
    window.copyCredVal = function (btn, val) {
        navigator.clipboard.writeText(val).then(function () {
            var orig = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-check2"></i>';
            btn.style.background = 'rgba(34,197,94,.4)';
            setTimeout(function () { btn.innerHTML = orig; btn.style.background = 'rgba(255,255,255,.18)'; }, 1800);
        }).catch(function () {
            // fallback
            var ta = document.createElement('textarea');
            ta.value = val; ta.style.position = 'fixed'; ta.style.opacity = '0';
            document.body.appendChild(ta); ta.select();
            document.execCommand('copy'); document.body.removeChild(ta);
            btn.innerHTML = '<i class="bi bi-check2"></i>';
            setTimeout(function () { btn.innerHTML = '<i class="bi bi-copy"></i>'; }, 1800);
        });
    };

    /* ── Pay modal open/close ── */
    function openPay(groupId, label, remaining) {
        // Reset method selection when opening
        document.querySelectorAll('.pm-btn').forEach(function(b){ b.classList.remove('selected'); });
        payMethodId.value = '';
        credsBox.style.display = 'none';
        pmError.style.display = 'none';

        document.getElementById('payGroupId').value     = groupId || '';
        document.getElementById('payLabel').textContent = label || '';
        document.getElementById('payAmount').max        = remaining;
        document.getElementById('payAmount').value      = parseFloat(remaining || 0).toFixed(2);

        var rem = parseFloat(remaining || 0);
        document.getElementById('payRemaining').textContent = 'الحد الأقصى للدفعة: ₪ ' + rem.toFixed(2);

        // Show required amount banner
        var banner = document.getElementById('payRequiredBanner');
        var bannerAmt = document.getElementById('payRequiredAmt');
        if (rem > 0) {
            bannerAmt.textContent = '₪ ' + rem.toFixed(2);
            banner.style.display = 'flex';
        } else {
            banner.style.display = 'none';
        }

        payModal.classList.add('active');
        payBackdrop.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closePay() {
        payModal.classList.remove('active');
        payBackdrop.classList.remove('active');
        document.body.style.overflow = '';
    }

    /* ── Payment confirmation dialog ── */
    var payConfirmOverlay = document.getElementById('payConfirmOverlay');
    var cfmConfirm        = document.getElementById('cfmConfirm');
    var cfmCancel         = document.getElementById('cfmCancel');
    var _pendingForm      = null;

    payModal.querySelector('form').addEventListener('submit', function (e) {
        e.preventDefault();
        if (!payMethodId.value) {
            pmError.style.display = 'block';
            pmError.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            return;
        }
        // Populate confirmation dialog
        var selectedBtn = document.querySelector('.pm-btn.selected');
        var methodName  = selectedBtn ? selectedBtn.dataset.name : '—';
        var amount      = document.getElementById('payAmount').value;
        var fileInput   = this.querySelector('input[name="receipt"]');
        var fileName    = fileInput && fileInput.files[0] ? fileInput.files[0].name : 'لم يتم تحديد ملف';

        document.getElementById('cfmMethod').textContent = methodName;
        document.getElementById('cfmAmount').textContent = '₪ ' + parseFloat(amount || 0).toFixed(2);
        document.getElementById('cfmFile').textContent   = fileName;

        _pendingForm = this;
        payConfirmOverlay.classList.add('active');
    });

    cfmCancel.addEventListener('click', function () {
        payConfirmOverlay.classList.remove('active');
        _pendingForm = null;
    });

    cfmConfirm.addEventListener('click', function () {
        payConfirmOverlay.classList.remove('active');
        if (_pendingForm) {
            // Disable button to prevent double-submit
            cfmConfirm.disabled = true;
            cfmConfirm.innerHTML = '<i class="bi bi-hourglass-split"></i> جارٍ الإرسال...';
            _pendingForm.submit();
        }
    });

    document.querySelectorAll('.js-open-pay').forEach(function (btn) {
        btn.addEventListener('click', function () {
            openPay(this.dataset.groupId, this.dataset.label, this.dataset.remaining);
        });
    });
    document.getElementById('closePayModal').addEventListener('click', closePay);
    document.getElementById('closePayModal2').addEventListener('click', closePay);
    payBackdrop.addEventListener('click', closePay);

    /* ── Receipt zoom (appended to body) ── */
    var overlay = document.createElement('div');
    overlay.style.cssText = 'display:none;position:fixed;inset:0;z-index:999999;'
        + 'background:rgba(0,0,0,.85);align-items:center;justify-content:center;cursor:zoom-out;';
    overlay.innerHTML = '<style>@keyframes rcptFI2{from{opacity:0}to{opacity:1}}'
        + '#_invRImg{max-width:92vw;max-height:88vh;border-radius:12px;box-shadow:0 8px 48px rgba(0,0,0,.6);object-fit:contain;}'
        + '#_invRClose{position:absolute;top:18px;right:22px;background:rgba(255,255,255,.18);border:none;color:#fff;'
        + 'font-size:1.6rem;width:40px;height:40px;border-radius:50%;cursor:pointer;'
        + 'display:flex;align-items:center;justify-content:center;transition:background .15s;}'
        + '#_invRClose:hover{background:rgba(255,255,255,.35);}</style>'
        + '<button id="_invRClose"><i class="bi bi-x"></i></button>'
        + '<img id="_invRImg" src="" alt="إيصال">';
    document.body.appendChild(overlay);
    var overlayImg  = overlay.querySelector('#_invRImg');
    var overlayClose = overlay.querySelector('#_invRClose');

    function openReceipt(url) {
        overlayImg.src = url;
        overlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeReceipt() {
        overlay.style.display = 'none';
        document.body.style.overflow = '';
        overlayImg.src = '';
    }

    document.querySelectorAll('.js-receipt-zoom').forEach(function (el) {
        el.addEventListener('click', function () { openReceipt(this.dataset.url); });
    });
    overlayClose.addEventListener('click', closeReceipt);
    overlay.addEventListener('click', function (e) { if (e.target === overlay) closeReceipt(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { closePay(); closeReceipt(); } });

    /* ── Real-time Pusher ── */
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
                if ((data.type || '') === 'new_invoice') return; // handled globally in dashboard

                var type     = data.type || '';
                var approved = data.status === 'approved';

                // Show toast notification
                showToast(data.title || 'إشعار', data.message || '', approved);

                // Update invoice bucket DOM when payment is approved or rejected
                if (type === 'payment_status_updated') {
                    var key    = data.group_id ? String(data.group_id) : 'pre_group';
                    var bucket = document.querySelector('[data-bucket-key="' + key + '"]');

                    if (!bucket) {
                        // Bucket not visible on page — reload after toast
                        setTimeout(function () { location.reload(); }, 2000);
                        return;
                    }

                    // Remove pending-review-status
                    var prv = bucket.querySelector('.pending-review-status');
                    if (prv) prv.remove();

                    var actionArea = bucket.querySelector('[data-bucket-action]');

                    if (approved) {
                        // Fetch updated balance from server to show correct next state
                        var url = '/student/financial/bucket-status'
                            + (data.group_id ? '?group_id=' + data.group_id : '');
                        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                            .then(function (r) { return r.ok ? r.json() : null; })
                            .then(function (d) {
                                if (!d || !actionArea) return;
                                actionArea.style.display = '';
                                if (d.remaining > 0.009) {
                                    actionArea.innerHTML =
                                        '<button type="button" class="btn-pay js-open-pay"'
                                        + ' data-group-id="' + (data.group_id || '') + '"'
                                        + ' data-label="' + (d.label || '') + '"'
                                        + ' data-remaining="' + d.remaining + '">'
                                        + '<i class="bi bi-credit-card"></i>دفع الفاتورة</button>';
                                    actionArea.querySelector('.js-open-pay').addEventListener('click', function () {
                                        openPay(this.dataset.groupId, this.dataset.label, this.dataset.remaining);
                                    });
                                    // Update "المتبقي" cell
                                    var remCell = bucket.querySelector('[data-bucket-remaining]');
                                    if (remCell) remCell.textContent = '₪ ' + parseFloat(d.remaining).toFixed(2);
                                } else {
                                    actionArea.innerHTML =
                                        '<span class="badge-paid-full"><i class="bi bi-check-circle-fill"></i>مدفوع بالكامل</span>';
                                    var remCell = bucket.querySelector('[data-bucket-remaining]');
                                    if (remCell) { remCell.textContent = '₪ 0.00'; remCell.classList.remove('kpi-red'); remCell.classList.add('kpi-green'); }
                                    bucket.classList.remove('has-balance');
                                }
                                // Update paid cell
                                if (d.total_paid) {
                                    var paidCell = bucket.querySelector('[data-bucket-paid]');
                                    if (paidCell) paidCell.textContent = '₪ ' + parseFloat(d.total_paid).toFixed(2);
                                }
                            })
                            .catch(function () {
                                setTimeout(function () { location.reload(); }, 1500);
                            });
                    } else {
                        // Rejected: restore pay button (reload to get fresh state)
                        setTimeout(function () { location.reload(); }, 2000);
                    }
                }
            });
        }
    })();
    @endauth

    function showToast(title, msg, ok) {
        var t = document.createElement('div');
        t.style.cssText = 'position:fixed;bottom:20px;left:20px;z-index:9000;min-width:290px;max-width:380px;background:'+(ok?'#d1fae5':'#fee2e2')+';border:1px solid '+(ok?'#6ee7b7':'#fca5a5')+';border-radius:12px;padding:14px 18px;box-shadow:0 4px 20px rgba(0,0,0,.12);';
        t.innerHTML = '<div style="font-weight:700;color:'+(ok?'#065f46':'#991b1b')+';margin-bottom:3px;"><i class="bi bi-'+(ok?'check-circle':'x-circle')+' me-2"></i>'+title+'</div>'
            + (msg ? '<div style="font-size:.84rem;color:#374151;">'+msg+'</div>' : '');
        document.body.appendChild(t);
        setTimeout(function () { t.remove(); }, 6000);
    }
})();
</script>
@endsection
