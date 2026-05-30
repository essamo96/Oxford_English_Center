{{-- Per-student "All Invoices" modal body --}}
@php
    $student = $student ?? null;
    $data    = $data ?? null;
@endphp

@if(!$student || !$data)
    <div class="alert alert-warning m-4">لا توجد فواتير لهذا الطالب.</div>
@else
<div class="p-5">
    {{-- Student header --}}
    <div class="d-flex align-items-center mb-5 pb-4 border-bottom">
        <div class="symbol symbol-50px me-3">
            <img src="{{ $student->image && file_exists(public_path($student->image)) ? asset($student->image) : asset('uploads/default.jpg') }}" alt="{{ $student->name }}">
        </div>
        <div>
            <h3 class="mb-1 fw-bold">{{ $student->name }}</h3>
            <div class="text-muted fs-7">
                <i class="bi bi-envelope me-1"></i> {{ $student->email ?: '—' }}
                <span class="ms-3"><i class="bi bi-phone me-1"></i> {{ $student->mobile ?: '—' }}</span>
            </div>
        </div>
    </div>

    {{-- Totals summary --}}
    <div class="row g-3 mb-5">
        <div class="col-md-4">
            <div class="p-4 rounded text-center" style="background: linear-gradient(135deg,#1e3a8a 0%,#3b82f6 100%); color:#fff;">
                <div class="fs-7 opacity-75 mb-1">إجمالي المستحق</div>
                <div class="fs-2 fw-bold">{{ number_format($data['grand_total_due'], 2) }} <small class="fs-6 opacity-75">ILS</small></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-4 rounded text-center" style="background: linear-gradient(135deg,#065f46 0%,#10b981 100%); color:#fff;">
                <div class="fs-7 opacity-75 mb-1">إجمالي المسدد</div>
                <div class="fs-2 fw-bold">{{ number_format($data['grand_total_paid'], 2) }} <small class="fs-6 opacity-75">ILS</small></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-4 rounded text-center" style="background: linear-gradient(135deg,#7f1d1d 0%,#ef4444 100%); color:#fff;">
                <div class="fs-7 opacity-75 mb-1">إجمالي المتبقي على الطالب</div>
                <div class="fs-2 fw-bold">{{ number_format($data['grand_total_left'], 2) }} <small class="fs-6 opacity-75">ILS</small></div>
            </div>
        </div>
    </div>

    {{-- Credit owed BY the center TO the student (over-payments / program-swap surplus) --}}
    @if(($data['grand_total_credit'] ?? 0) > 0.01)
    <div class="alert d-flex align-items-center gap-3 mb-5 border-0" style="background:linear-gradient(135deg,#0c4a6e 0%,#0ea5e9 100%);color:#fff;border-radius:12px;">
        <i class="bi bi-piggy-bank-fill fs-2hx"></i>
        <div class="flex-grow-1">
            <div class="fw-bold fs-5">رصيد دائن للطالب — المركز مدين له</div>
            <div class="opacity-75 fs-7">مبلغ فائض دفعه الطالب (يُحسم تلقائياً من أي رسوم لاحقة: دورات / كتب)</div>
        </div>
        <div class="fs-2hx fw-bold">{{ number_format($data['grand_total_credit'], 2) }} <small class="fs-5 opacity-75">ILS</small></div>
    </div>
    @endif

    {{-- Per-bucket (per program / placement test) breakdown --}}
    @foreach($data['summary'] as $bucket)
        <div class="card mb-5 shadow-sm border">
            <div class="card-header bg-light d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h6 class="mb-0 fw-bold text-dark">
                        @if($bucket['group_id'])
                            <i class="bi bi-mortarboard-fill text-primary me-1"></i>
                        @else
                            <i class="bi bi-clipboard-check text-warning me-1"></i>
                        @endif
                        {{ $bucket['label'] }}
                    </h6>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge badge-light-primary fs-8 fw-bold">المستحق: {{ number_format($bucket['total_fee'], 2) }}</span>
                    <span class="badge badge-light-success fs-8 fw-bold">المسدد: {{ number_format($bucket['paid'], 2) }}</span>
                    <span class="badge {{ $bucket['remaining'] > 0 ? 'badge-light-danger' : 'badge-light-success' }} fs-8 fw-bold">
                        المتبقي: {{ number_format($bucket['remaining'], 2) }}
                    </span>
                    @if($bucket['remaining'] > 0)
                        @php $firstRow = $bucket['rows']->first(); @endphp
                        <button type="button" class="btn btn-sm btn-success"
                                onclick="recordPayment({{ $firstRow->id }}, {{ number_format($bucket['remaining'], 2, '.', '') }})">
                            <i class="bi bi-cash-stack me-1"></i> سدد دفعة
                        </button>
                    @endif
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-row-bordered table-row-gray-200 align-middle mb-0 fs-7">
                    <thead class="bg-light-secondary">
                        <tr class="text-muted fw-bold fs-8 text-uppercase">
                            <th class="ps-4">#</th>
                            <th>التاريخ</th>
                            <th>النوع</th>
                            <th>المبلغ</th>
                            <th>طريقة الدفع</th>
                            <th>الحالة</th>
                            <th class="pe-4">الإيصال</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bucket['rows'] as $i => $tx)
                            @php
                                $isRefund   = $tx->transaction_type === 'refund';
                                $amount     = $tx->transaction_amount ?: $tx->student_fee_paid;
                                $statusMap  = [
                                    'verified' => ['مؤكد', 'badge-light-success'],
                                    'pending'  => ['قيد التدقيق', 'badge-light-warning'],
                                    'rejected' => ['مرفوض', 'badge-light-danger'],
                                ];
                                $st = $statusMap[$tx->audit_status] ?? [$tx->audit_status, 'badge-light-secondary'];
                            @endphp
                            <tr>
                                <td class="ps-4 fw-bold text-muted">{{ $i + 1 }}</td>
                                <td>
                                    <div class="fw-bold">{{ \Carbon\Carbon::parse($tx->created_at)->format('Y-m-d') }}</div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($tx->created_at)->format('h:i A') }}</small>
                                </td>
                                <td>
                                    @if($isRefund)
                                        <span class="badge badge-light-danger fw-bold"><i class="bi bi-arrow-counterclockwise me-1"></i>استرداد</span>
                                    @else
                                        <span class="badge badge-light-info fw-bold">{{ $tx->student_paid_type ?: 'دفعة' }}</span>
                                    @endif
                                </td>
                                <td class="fw-bold {{ $isRefund ? 'text-danger' : 'text-success' }}">
                                    {{ $isRefund ? '-' : '+' }}{{ number_format(abs($amount), 2) }} <small class="text-muted">ILS</small>
                                </td>
                                <td>{{ $tx->paymentMethod->name ?? '—' }}</td>
                                <td><span class="badge {{ $st[1] }}">{{ $st[0] }}</span></td>
                                <td class="pe-4">
                                    @if($tx->payment_receipt)
                                        <a href="{{ asset('uploads/' . $tx->payment_receipt) }}" target="_blank" class="btn btn-sm btn-icon btn-light-primary" title="عرض الإيصال">
                                            <i class="bi bi-receipt"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>
@endif
