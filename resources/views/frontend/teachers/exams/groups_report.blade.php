@extends('frontend.layouts.dashboard')
@section('title', 'تقرير المجموعات والامتحانات')
@section('page-title', 'تقرير المجموعات والامتحانات')

@section('css')
@include('frontend.teachers.exams.parts.design')
<style>
    .grp-card__header { background: var(--tex-navy); color: #fff; padding: 14px 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; }
    .grp-card__header h5 { margin: 0; font-weight: 700; display: flex; align-items: center; gap: 10px; }
    .exam-block { border: 1px solid var(--tex-border); border-radius: 10px; margin-bottom: 16px; overflow: hidden; }
    .exam-block:last-child { margin-bottom: 0; }
    .exam-block__header { background: #f9fafc; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; border-bottom: 1px solid var(--tex-border); }
    .exam-block__header h6 { margin: 0; font-weight: 700; color: var(--tex-navy); display: flex; align-items: center; gap: 8px; }
    .exam-block__stats { display: flex; gap: 16px; flex-wrap: wrap; font-size: 12.5px; color: var(--tex-muted); }
    .exam-block__stats b { color: var(--tex-navy); }
</style>
@stop

@section('content')
@php
    $totalExams = $groups->sum(fn($g) => $g->exams->count());
    $allAttempts = $groups->flatMap(fn($g) => $g->exams->flatMap->attempts);
    $totalAttempts = $allAttempts->count();
    $totalViolations = $allAttempts->sum('violations_count');
@endphp

<div class="tex-page-head">
    <div class="tex-page-head__title">
        <span class="tex-page-head__icon"><i class="bi bi-bar-chart-line"></i></span>
        <div>
            <h4>تقرير المجموعات والامتحانات</h4>
            <p>نظرة شاملة على كل مجموعة، امتحاناتها، وأداء طلابها.</p>
        </div>
    </div>
</div>

<div class="tex-stats">
    <div class="tex-stat">
        <span class="tex-stat__icon" style="background:var(--tex-info-bg); color:var(--tex-info);"><i class="bi bi-people-fill"></i></span>
        <div><div class="tex-stat__value">{{ $groups->count() }}</div><div class="tex-stat__label">المجموعات</div></div>
    </div>
    <div class="tex-stat">
        <span class="tex-stat__icon" style="background:var(--tex-success-bg); color:var(--tex-success);"><i class="bi bi-journal-text"></i></span>
        <div><div class="tex-stat__value">{{ $totalExams }}</div><div class="tex-stat__label">الامتحانات</div></div>
    </div>
    <div class="tex-stat">
        <span class="tex-stat__icon" style="background:var(--tex-warning-bg); color:var(--tex-warning);"><i class="bi bi-collection"></i></span>
        <div><div class="tex-stat__value">{{ $totalAttempts }}</div><div class="tex-stat__label">المحاولات</div></div>
    </div>
    <div class="tex-stat">
        <span class="tex-stat__icon" style="background:var(--tex-danger-bg); color:var(--tex-danger);"><i class="bi bi-shield-exclamation"></i></span>
        <div><div class="tex-stat__value">{{ $totalViolations }}</div><div class="tex-stat__label">مخالفات الغش</div></div>
    </div>
</div>

@forelse($groups as $group)
    <div class="tex-card grp-card">
        <div class="grp-card__header">
            <h5><i class="bi bi-people-fill"></i> {{ $group->name }}</h5>
            <span class="tex-pill" style="background:rgba(255,255,255,.15); color:#fff;">{{ $group->exams->count() }} امتحان</span>
        </div>
        <div class="tex-card__body">
            @forelse($group->exams as $exam)
                @php
                    $attempts = $exam->attempts;
                    $gradedAttempts = $attempts->whereNotNull('percentage');
                    $avgScore = $gradedAttempts->count() ? round($gradedAttempts->avg('percentage'), 1) : null;
                    $passCount = $gradedAttempts->filter(fn($a) => $a->percentage >= $exam->passing_score)->count();
                    $totalExamViolations = $attempts->sum('violations_count');
                @endphp
                <div class="exam-block">
                    <div class="exam-block__header">
                        <h6><i class="bi bi-journal-text"></i> {{ $exam->title }}</h6>
                        <div class="exam-block__stats">
                            <span><i class="bi bi-collection"></i> المحاولات: <b>{{ $attempts->count() }}</b></span>
                            <span><i class="bi bi-graph-up"></i> المتوسط: <b>{{ $avgScore ?? '—' }}{{ $avgScore !== null ? '%' : '' }}</b></span>
                            <span><i class="bi bi-trophy"></i> الناجحون: <b>{{ $passCount }}</b> / {{ $gradedAttempts->count() }}</span>
                            <span class="{{ $totalExamViolations > 0 ? 'text-danger' : '' }}"><i class="bi bi-shield-exclamation"></i> المخالفات: <b>{{ $totalExamViolations }}</b></span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table tex-table table-sm align-middle text-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-start">الطالب</th>
                                    <th>الحالة</th>
                                    <th>الدرجة</th>
                                    <th>النسبة</th>
                                    <th>مخالفات الغش</th>
                                    <th>تاريخ التسليم</th>
                                    <th>العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $statusLabels = ['in_progress' => 'جارٍ', 'submitted' => 'بانتظار التصحيح', 'graded' => 'تم التصحيح', 'expired' => 'منتهي الوقت'];
                                    $statusPill = ['in_progress' => 'tex-pill--warning', 'submitted' => 'tex-pill--info', 'graded' => 'tex-pill--success', 'expired' => 'tex-pill--muted'];
                                @endphp
                                @forelse($attempts as $attempt)
                                    <tr>
                                        <td class="text-start fw-semibold">{{ $attempt->student->name ?? '—' }}</td>
                                        <td><span class="tex-pill {{ $statusPill[$attempt->status] ?? 'tex-pill--muted' }}">{{ $statusLabels[$attempt->status] ?? $attempt->status }}</span></td>
                                        <td>{{ $attempt->final_score ?? $attempt->auto_score ?? '—' }} / {{ $attempt->total_marks }}</td>
                                        <td>
                                            @if($attempt->percentage !== null)
                                                <span class="tex-pill {{ $attempt->percentage >= $exam->passing_score ? 'tex-pill--success' : 'tex-pill--danger' }}">{{ $attempt->percentage }}%</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($attempt->violations_count > 0)
                                                <span class="tex-pill tex-pill--danger">{{ $attempt->violations_count }}</span>
                                            @else
                                                <span class="tex-pill tex-pill--success">0</span>
                                            @endif
                                        </td>
                                        <td class="text-muted small">{{ $attempt->submitted_at?->format('Y-m-d H:i') ?? '—' }}</td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="javascript:;" data-href="{{ Crypt::encrypt($attempt->id) }}" class="tex-icon-btn view-answers" title="عرض الإجابات">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="javascript:;" data-href="{{ Crypt::encrypt($attempt->id) }}" class="tex-icon-btn tex-icon-btn--danger view-wrong-answers" title="الأخطاء فقط">
                                                    <i class="bi bi-x-circle"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-muted py-4">لم يقدّم أي طالب على هذا الامتحان بعد</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="tex-empty">
                    <i class="bi bi-journal-x"></i>
                    <p class="mb-0">لا توجد امتحانات لهذه المجموعة بعد.</p>
                </div>
            @endforelse
        </div>
    </div>
@empty
    <div class="tex-card">
        <div class="tex-empty">
            <i class="bi bi-people"></i>
            <p class="fw-semibold mb-1">لا توجد مجموعات فعالة مسندة إليك بعد</p>
        </div>
    </div>
@endforelse

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
</script>
@stop
