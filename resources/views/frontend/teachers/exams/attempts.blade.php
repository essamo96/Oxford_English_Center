@extends('frontend.layouts.dashboard')
@section('title', 'محاولات طلابي')
@section('page-title', 'محاولات طلابي')

@section('css')
@include('frontend.teachers.exams.parts.design')
@stop

@section('content')
@php
    $statusLabels = ['in_progress' => 'جارٍ', 'submitted' => 'بانتظار التصحيح', 'graded' => 'تم التصحيح', 'expired' => 'منتهي الوقت'];
    $statusPill = ['in_progress' => 'tex-pill--warning', 'submitted' => 'tex-pill--info', 'graded' => 'tex-pill--success', 'expired' => 'tex-pill--muted'];
    $graded = $attempts->whereNotNull('percentage');
    $avgScore = $graded->count() ? round($graded->avg('percentage'), 1) : null;
    $pendingCount = $attempts->where('status', 'submitted')->count();
    $totalViolations = $attempts->sum('violations_count');
@endphp

<div class="tex-page-head">
    <div class="tex-page-head__title">
        <span class="tex-page-head__icon"><i class="bi bi-clipboard-data"></i></span>
        <div>
            <h4>محاولات طلابي</h4>
            <p>كل محاولات طلابك في امتحانات مجموعاتك، بدرجاتها وحالتها.</p>
        </div>
    </div>
</div>

<div class="tex-stats">
    <div class="tex-stat">
        <span class="tex-stat__icon" style="background:var(--tex-info-bg); color:var(--tex-info);"><i class="bi bi-collection"></i></span>
        <div><div class="tex-stat__value">{{ $attempts->count() }}</div><div class="tex-stat__label">إجمالي المحاولات</div></div>
    </div>
    <div class="tex-stat">
        <span class="tex-stat__icon" style="background:var(--tex-success-bg); color:var(--tex-success);"><i class="bi bi-graph-up"></i></span>
        <div><div class="tex-stat__value">{{ $avgScore ?? '—' }}{{ $avgScore !== null ? '%' : '' }}</div><div class="tex-stat__label">متوسط الدرجات</div></div>
    </div>
    <div class="tex-stat">
        <span class="tex-stat__icon" style="background:var(--tex-warning-bg); color:var(--tex-warning);"><i class="bi bi-hourglass-split"></i></span>
        <div><div class="tex-stat__value">{{ $pendingCount }}</div><div class="tex-stat__label">بانتظار التصحيح</div></div>
    </div>
    <div class="tex-stat">
        <span class="tex-stat__icon" style="background:var(--tex-danger-bg); color:var(--tex-danger);"><i class="bi bi-shield-exclamation"></i></span>
        <div><div class="tex-stat__value">{{ $totalViolations }}</div><div class="tex-stat__label">إجمالي مخالفات الغش</div></div>
    </div>
</div>

<div class="tex-card">
    <div class="tex-card__header">
        <p class="tex-card__header-title"><i class="bi bi-table"></i> جميع المحاولات</p>
        <div class="tex-toolbar">
            <input type="text" id="attempts_search" class="form-control form-control-sm" placeholder="ابحث باسم الطالب أو الامتحان..." style="min-width:220px;">
            <select id="attempts_status_filter" class="form-select form-select-sm">
                <option value="">كل الحالات</option>
                @foreach($statusLabels as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="tex-card__body p-0">
        @if($attempts->count())
        <div class="table-responsive">
            <table class="table tex-table align-middle text-center mb-0" id="attempts_table">
                <thead>
                    <tr>
                        <th>#</th><th class="text-start">الطالب</th><th class="text-start">الامتحان</th><th>المجموعة</th>
                        <th>تاريخ التسليم</th><th>الوقت المنقضي</th><th>الدرجة</th><th>الحالة</th><th>مخالفات</th><th>العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attempts as $index => $attempt)
                        <tr class="attempt-row" data-status="{{ $attempt->status }}" data-search="{{ \Illuminate\Support\Str::lower(($attempt->student->name ?? '').' '.($attempt->exam->title ?? '')) }}">
                            <td class="text-muted">{{ $index + 1 }}</td>
                            <td class="text-start fw-semibold">{{ $attempt->student->name ?? '—' }}</td>
                            <td class="text-start">{{ $attempt->exam->title ?? '—' }}</td>
                            <td><span class="tex-pill tex-pill--info">{{ $attempt->exam->group->name ?? '—' }}</span></td>
                            <td class="text-muted small">{{ $attempt->submitted_at?->format('Y-m-d H:i') ?? '—' }}</td>
                            <td class="text-muted small">{{ $attempt->duration_taken }}</td>
                            <td>
                                @if($attempt->percentage !== null)
                                    <span class="tex-pill {{ $attempt->percentage >= ($attempt->exam->passing_score ?? 50) ? 'tex-pill--success' : 'tex-pill--danger' }}">
                                        {{ $attempt->percentage }}% ({{ $attempt->final_score ?? $attempt->auto_score }}/{{ $attempt->total_marks }})
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td><span class="tex-pill {{ $statusPill[$attempt->status] ?? 'tex-pill--muted' }}">{{ $statusLabels[$attempt->status] ?? $attempt->status }}</span></td>
                            <td>
                                @if($attempt->violations_count > 0)
                                    <span class="tex-pill tex-pill--danger"><i class="bi bi-shield-exclamation"></i> {{ $attempt->violations_count }}</span>
                                @else
                                    <span class="tex-pill tex-pill--success">0</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="javascript:;" data-href="{{ Crypt::encrypt($attempt->id) }}" class="tex-icon-btn view-answers" title="عرض جميع الإجابات">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="javascript:;" data-href="{{ Crypt::encrypt($attempt->id) }}" class="tex-icon-btn tex-icon-btn--danger view-wrong-answers" title="عرض الأسئلة الخاطئة">
                                        <i class="bi bi-x-circle"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="tex-empty d-none" id="attempts_no_match">
            <i class="bi bi-search"></i>
            <p class="fw-semibold mb-0">لا توجد نتائج مطابقة للبحث</p>
        </div>
        @else
        <div class="tex-empty">
            <i class="bi bi-inbox"></i>
            <p class="fw-semibold mb-1">لا توجد محاولات بعد</p>
            <p>ستظهر هنا محاولات طلابك بمجرد أن يبدؤوا بأداء امتحاناتك.</p>
        </div>
        @endif
    </div>
</div>

<div class="modal fade" id="attempt_answers_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="attempt_answers_modal_title">إجابات المحاولة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="attempt_answers_modal_content" style="max-height: 75vh; overflow-y:auto;"></div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    function showAttemptModal(url, id, title) {
        $('#attempt_answers_modal_title').text(title);
        $('#attempt_answers_modal_content').html('<div class="tex-spinner-wrap"><span class="spinner-border" role="status"></span></div>');
        $('#attempt_answers_modal').modal('show');
        $.ajax({
            url: url, type: 'POST', data: { id: id, _token: '{{ csrf_token() }}' },
            success: function (response) { $('#attempt_answers_modal_content').html(response); },
            error: function () { $('#attempt_answers_modal_content').html('<div class="alert alert-danger">حدث خطأ أثناء تحميل البيانات</div>'); }
        });
    }
    $(document).on('click', '.view-answers', function () {
        showAttemptModal("{{ route('teacher.exam_attempts.answers') }}", $(this).data('href'), 'جميع الإجابات');
    });
    $(document).on('click', '.view-wrong-answers', function () {
        showAttemptModal("{{ route('teacher.exam_attempts.wrong_answers') }}", $(this).data('href'), 'الأسئلة الخاطئة');
    });

    // client-side search + status filter, no backend round-trip
    function applyAttemptsFilter() {
        var keyword = $('#attempts_search').val().toLowerCase().trim();
        var status = $('#attempts_status_filter').val();
        var visible = 0;
        $('.attempt-row').each(function () {
            var $row = $(this);
            var matchesText = !keyword || $row.data('search').indexOf(keyword) !== -1;
            var matchesStatus = !status || $row.data('status') === status;
            var show = matchesText && matchesStatus;
            $row.toggle(show);
            if (show) visible++;
        });
        $('#attempts_no_match').toggleClass('d-none', visible !== 0);
    }
    $('#attempts_search').on('input keyup', applyAttemptsFilter);
    $('#attempts_status_filter').on('change', applyAttemptsFilter);
</script>
@stop
