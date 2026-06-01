@php
    $monthsAr = [1=>'يناير',2=>'فبراير',3=>'مارس',4=>'أبريل',5=>'مايو',6=>'يونيو',7=>'يوليو',8=>'أغسطس',9=>'سبتمبر',10=>'أكتوبر',11=>'نوفمبر',12=>'ديسمبر'];
    $total = $lines->sum('amount');
@endphp

<div class="p-2">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h4 class="fw-bold mb-1">{{ $teacher->name ?? '—' }}</h4>
            <span class="text-muted">الفترة: {{ $monthsAr[$month] ?? $month }} {{ $year }}</span>
        </div>
        <div class="text-end">
            @if($form)
                <div class="fs-7 text-muted">الصافي</div>
                <div class="fs-3 fw-bold text-success">{{ number_format($form->net_amount, 2) }} ILS</div>
                @if($form->status === 'closed')
                    <span class="badge badge-light-success mt-1"><i class="bi bi-lock-fill me-1"></i>مغلق
                        @if($form->closed_at) — {{ $form->closed_at->format('Y-m-d') }}@endif
                    </span>
                @endif
            @endif
        </div>
    </div>

    @if($form)
    <div class="row g-3 mb-4 text-center">
        <div class="col"><div class="p-2 rounded bg-light-primary"><div class="fs-8 text-muted">المحاضرات</div><div class="fw-bold">{{ $form->lectures_count }}</div></div></div>
        <div class="col"><div class="p-2 rounded bg-light-info"><div class="fs-8 text-muted">أجر المحاضرة</div><div class="fw-bold">{{ number_format($form->lecture_rate,2) }}</div></div></div>
        <div class="col"><div class="p-2 rounded bg-light-dark"><div class="fs-8 text-muted">الإجمالي</div><div class="fw-bold">{{ number_format($form->gross_amount,2) }}</div></div></div>
        <div class="col"><div class="p-2 rounded bg-light-success"><div class="fs-8 text-muted">علاوة</div><div class="fw-bold">{{ number_format($form->bonus,2) }}</div></div></div>
        <div class="col"><div class="p-2 rounded bg-light-danger"><div class="fs-8 text-muted">خصم</div><div class="fw-bold">{{ number_format($form->deduction,2) }}</div></div></div>
    </div>
    @endif

    <h6 class="fw-bold mb-2"><i class="bi bi-list-check me-1"></i> المحاضرات المحتسبة ({{ $lines->count() }})</h6>
    <table class="table table-row-bordered align-middle fs-7 text-center">
        <thead class="bg-light-secondary"><tr class="fw-bold text-muted">
            <th>#</th><th>التاريخ</th><th>المجموعة</th><th>الأجر</th>
        </tr></thead>
        <tbody>
            @forelse($lines as $i => $ln)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($ln['date'])->format('Y-m-d') }}</td>
                    <td>{{ $groupNames[$ln['group_id']] ?? ('#'.$ln['group_id']) }}</td>
                    <td class="fw-bold">{{ number_format($ln['amount'], 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-muted py-4">لا توجد محاضرات.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="fw-bold"><td colspan="3" class="text-end">إجمالي المحاضرات</td><td class="text-success">{{ number_format($total, 2) }} ILS</td></tr>
        </tfoot>
    </table>
</div>
