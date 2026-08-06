@extends('frontend.layouts.dashboard')
@section('title', 'تصحيح ومراجعة الإجابات')
@section('page-title', 'تصحيح ومراجعة الإجابات')

@section('css')
@include('frontend.teachers.exams.parts.design')
@stop

@section('content')
<div class="tex-page-head">
    <div class="tex-page-head__title">
        <span class="tex-page-head__icon"><i class="bi bi-check2-square"></i></span>
        <div>
            <h4>تصحيح ومراجعة الإجابات</h4>
            <p>صحّح إجابات طلابك المقالية/الصوتية، وراجع طلبات إعادة النظر بالنتائج.</p>
        </div>
    </div>
</div>

<div class="tex-stats">
    <div class="tex-stat">
        <span class="tex-stat__icon" style="background:var(--tex-warning-bg); color:var(--tex-warning);"><i class="bi bi-hourglass-split"></i></span>
        <div><div class="tex-stat__value">{{ $pendingAttempts->count() }}</div><div class="tex-stat__label">بانتظار التصحيح</div></div>
    </div>
    <div class="tex-stat">
        <span class="tex-stat__icon" style="background:var(--tex-info-bg); color:var(--tex-info);"><i class="bi bi-envelope-open"></i></span>
        <div><div class="tex-stat__value">{{ $reviewRequests->count() }}</div><div class="tex-stat__label">طلبات مراجعة معلّقة</div></div>
    </div>
</div>

<div class="tex-card">
    <div class="tex-card__header">
        <p class="tex-card__header-title"><i class="bi bi-hourglass-split"></i> محاولات بانتظار التصحيح</p>
        @if($pendingAttempts->count())<span class="tex-pill tex-pill--warning">{{ $pendingAttempts->count() }}</span>@endif
    </div>
    <div class="tex-card__body p-0">
        @if($pendingAttempts->count())
        <div class="table-responsive">
            <table class="table tex-table align-middle text-center mb-0">
                <thead>
                    <tr>
                        <th>#</th><th class="text-start">الطالب</th><th class="text-start">الامتحان</th>
                        <th>تاريخ التسليم</th><th>مخالفات الغش</th><th>العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingAttempts as $index => $attempt)
                        <tr>
                            <td class="text-muted">{{ $index + 1 }}</td>
                            <td class="text-start fw-semibold">{{ $attempt->student->name ?? '—' }}</td>
                            <td class="text-start">{{ $attempt->exam->title ?? '—' }}</td>
                            <td class="text-muted"><i class="bi bi-calendar-event"></i> {{ $attempt->submitted_at?->format('Y-m-d H:i') }}</td>
                            <td>
                                @if($attempt->violations_count > 0)
                                    <span class="tex-pill tex-pill--danger"><i class="bi bi-shield-exclamation"></i> {{ $attempt->violations_count }}</span>
                                @else
                                    <span class="tex-pill tex-pill--success">0</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('teacher.exam_reviews.grade', ['id' => Crypt::encrypt($attempt->id)]) }}" class="tex-btn-primary" style="padding:7px 16px; font-size:13px;">
                                    <i class="bi bi-pencil-square"></i> تصحيح
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="tex-empty">
            <i class="bi bi-emoji-smile"></i>
            <p class="fw-semibold mb-1">لا توجد محاولات بانتظار التصحيح</p>
            <p>أحسنت! كل الإجابات المقالية/الصوتية مصحَّحة حتى الآن.</p>
        </div>
        @endif
    </div>
</div>

<div class="tex-card">
    <div class="tex-card__header">
        <p class="tex-card__header-title"><i class="bi bi-envelope-open"></i> طلبات المراجعة</p>
        @if($reviewRequests->count())<span class="tex-pill tex-pill--info">{{ $reviewRequests->count() }}</span>@endif
    </div>
    <div class="tex-card__body p-0">
        @if($reviewRequests->count())
        <div class="table-responsive">
            <table class="table tex-table align-middle text-center mb-0">
                <thead>
                    <tr><th>#</th><th class="text-start">الطالب</th><th class="text-start">الامتحان</th><th class="text-start">الرسالة</th><th>العمليات</th></tr>
                </thead>
                <tbody>
                    @foreach($reviewRequests as $index => $review)
                        <tr id="review_row_{{ $review->id }}">
                            <td class="text-muted">{{ $index + 1 }}</td>
                            <td class="text-start fw-semibold">{{ $review->student->name ?? '—' }}</td>
                            <td class="text-start">{{ $review->attempt->exam->title ?? '—' }}</td>
                            <td class="text-start text-muted">{{ $review->message }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="tex-icon-btn tex-icon-btn--success approve-review" data-href="{{ Crypt::encrypt($review->id) }}" title="اعتماد">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <button class="tex-icon-btn tex-icon-btn--danger reject-review" data-href="{{ Crypt::encrypt($review->id) }}" title="رفض">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="tex-empty">
            <i class="bi bi-inbox"></i>
            <p class="fw-semibold mb-1">لا توجد طلبات مراجعة</p>
            <p>ستظهر هنا أي طلبات إعادة نظر بالنتائج يرسلها طلابك.</p>
        </div>
        @endif
    </div>
</div>
@stop

@section('js')
<script>
    function decideReview(id, decision) {
        var send = function (comment) {
            $.post("{{ route('teacher.exam_reviews.approve') }}", {id: id, decision: decision, teacher_comment: comment, _token: '{{ csrf_token() }}'}, function (data) {
                if (typeof toastr !== 'undefined') { toastr[data.status](data.message); }
                $('#review_row_' + id).fadeOut();
            });
        };
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: decision === 'approved' ? 'اعتماد طلب المراجعة' : 'رفض طلب المراجعة',
                input: 'text', inputPlaceholder: 'تعليق (اختياري)',
                showCancelButton: true, confirmButtonText: 'تأكيد', cancelButtonText: 'إلغاء'
            }).then(function (result) { if (result.isConfirmed) send(result.value || ''); });
        } else {
            send(prompt('تعليق (اختياري):', '') || '');
        }
    }
    $(document).on('click', '.approve-review', function () { decideReview($(this).data('href'), 'approved'); });
    $(document).on('click', '.reject-review', function () { decideReview($(this).data('href'), 'rejected'); });
</script>
@stop
