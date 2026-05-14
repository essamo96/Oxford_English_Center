<div class="text-center mb-8">
    <h2 class="fw-bold mb-1">البيانات المالية للطالب</h2>
    <div class="text-muted fw-semibold fs-5">{{ $student->name }}</div>
</div>

<div class="row g-5 mb-8">
    @foreach($ledgers as $ledger)
        @if($ledger)
        <div class="col-md-6">
            <div class="card border border-dashed border-gray-300 rounded p-5 bg-light-{{ $ledger['remaining_balance'] > 0 ? 'danger' : 'success' }}">
                <div class="fw-bold fs-6 text-gray-800 mb-2">{{ $ledger['group_student']->group->program->title ?? 'N/A' }}</div>
                <div class="d-flex flex-stack mb-2">
                    <span class="text-gray-600 fw-bold">إجمالي الرسوم:</span>
                    <span class="text-gray-800 fw-bolder">{{ number_format($ledger['total_fee'], 2) }} ILS</span>
                </div>
                <div class="d-flex flex-stack mb-2">
                    <span class="text-gray-600 fw-bold">المسدد المؤكد:</span>
                    <span class="text-success fw-bolder">{{ number_format($ledger['total_paid'], 2) }} ILS</span>
                </div>
                <div class="separator separator-dashed my-3"></div>
                <div class="d-flex flex-stack">
                    <span class="text-gray-800 fw-bold">الرصيد المتبقي:</span>
                    <span class="text-{{ $ledger['remaining_balance'] > 0 ? 'danger' : 'success' }} fw-bolder fs-4">{{ number_format($ledger['remaining_balance'], 2) }} ILS</span>
                </div>
            </div>
        </div>
        @endif
    @endforeach
</div>

<div class="table-responsive">
    <table class="table table-row-bordered table-row-gray-300 align-middle g-4 text-center">
        <thead>
            <tr class="fw-bold fs-6 text-gray-800">
                <th>التاريخ</th>
                <th>النوع</th>
                <th>المبلغ المدخل</th>
                <th>المؤكد</th>
                <th>الحالة</th>
                <th>الإيصال</th>
            </tr>
        </thead>
        <tbody>
            @forelse($fees as $fee)
                <tr>
                    <td>{{ $fee->created_at->format('Y-m-d') }}</td>
                    <td><span class="badge badge-light-info">{{ $fee->student_paid_type }}</span></td>
                    <td class="fw-bold">{{ number_format($fee->student_fee_paid, 2) }} ILS</td>
                    <td class="text-success fw-bold">{{ number_format($fee->transaction_amount ?: 0, 2) }} ILS</td>
                    <td>
                        @php
                            $statusClass = $fee->audit_status == 'verified' ? 'success' : ($fee->audit_status == 'pending' ? 'warning' : 'danger');
                            $statusText = $fee->audit_status == 'verified' ? 'مؤكد' : ($fee->audit_status == 'pending' ? 'قيد التدقيق' : 'مرفوض');
                        @endphp
                        <span class="badge badge-light-{{ $statusClass }}">{{ $statusText }}</span>
                    </td>
                    <td>
                        @if($fee->payment_receipt)
                            <a href="{{ asset('uploads/'.$fee->payment_receipt) }}" target="_blank" class="btn btn-icon btn-sm btn-light-primary" title="عرض الإيصال">
                                <i class="ki-duotone ki-picture fs-3"></i>
                            </a>
                        @else
                            <span class="text-muted fs-7">لا يوجد</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">لا توجد سجلات مالية بعد.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-center gap-3 mt-8">
    <a href="{{ route('admin.financial.pending') }}" class="btn btn-warning">
        <i class="ki-duotone ki-time fs-2 me-2"><span class="path1"></span><span class="path2"></span></i>
        الطلبات العالقة
    </a>
    <a href="{{ route('admin.financial.ledger', ['studentId' => $student->id, 'groupId' => $ledgers[0]['group_student']->group_id ?? 0]) }}" class="btn btn-info {{ empty($ledgers) ? 'disabled' : '' }}">
        <i class="ki-duotone ki-book-open fs-2 me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
        السجل المالي الكامل
    </a>
</div>
