@extends('frontend.layouts.dashboard')
@section('title', 'تقرير المجموعات والامتحانات')
@section('page-title', 'تقرير المجموعات والامتحانات')

@section('css')
<style>
    .grp-card { background: #fff; border: 1px solid #e7eaf0; border-radius: 12px; margin-bottom: 22px; overflow: hidden; }
    .grp-card__header { background: #14213d; color: #fff; padding: 14px 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; }
    .grp-card__header h5 { margin: 0; font-weight: 700; }
    .grp-card__body { padding: 18px 20px; }
    .exam-block { border: 1px solid #e7eaf0; border-radius: 10px; margin-bottom: 16px; }
    .exam-block__header { background: #f7f9fc; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; border-bottom: 1px solid #e7eaf0; }
    .exam-block__header h6 { margin: 0; font-weight: 700; color: #14213d; }
    .exam-block__stats { display: flex; gap: 16px; flex-wrap: wrap; font-size: 13px; color: #7a8296; }
    .exam-block__stats b { color: #14213d; }
</style>
@stop

@section('content')
@forelse($groups as $group)
    <div class="grp-card">
        <div class="grp-card__header">
            <h5><i class="bi bi-people-fill"></i> {{ $group->name }}</h5>
            <span class="badge bg-light text-dark">{{ $group->exams->count() }} امتحان</span>
        </div>
        <div class="grp-card__body">
            @forelse($group->exams as $exam)
                @php
                    $attempts = $exam->attempts;
                    $gradedAttempts = $attempts->whereNotNull('percentage');
                    $avgScore = $gradedAttempts->count() ? round($gradedAttempts->avg('percentage'), 1) : null;
                    $passCount = $gradedAttempts->filter(fn($a) => $a->percentage >= $exam->passing_score)->count();
                    $totalViolations = $attempts->sum('violations_count');
                @endphp
                <div class="exam-block">
                    <div class="exam-block__header">
                        <h6><i class="bi bi-journal-text"></i> {{ $exam->title }}</h6>
                        <div class="exam-block__stats">
                            <span>عدد المحاولات: <b>{{ $attempts->count() }}</b></span>
                            <span>متوسط الدرجات: <b>{{ $avgScore ?? '—' }}{{ $avgScore !== null ? '%' : '' }}</b></span>
                            <span>الناجحون: <b>{{ $passCount }}</b> / {{ $gradedAttempts->count() }}</span>
                            <span class="{{ $totalViolations > 0 ? 'text-danger' : '' }}"><i class="bi bi-shield-exclamation"></i> إجمالي المخالفات: <b>{{ $totalViolations }}</b></span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle text-center mb-0">
                            <thead>
                                <tr>
                                    <th>الطالب</th>
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
                                @endphp
                                @forelse($attempts as $attempt)
                                    <tr>
                                        <td>{{ $attempt->student->name ?? '—' }}</td>
                                        <td>{{ $statusLabels[$attempt->status] ?? $attempt->status }}</td>
                                        <td>{{ $attempt->final_score ?? $attempt->auto_score ?? '—' }} / {{ $attempt->total_marks }}</td>
                                        <td>
                                            @if($attempt->percentage !== null)
                                                <span class="badge {{ $attempt->percentage >= $exam->passing_score ? 'bg-success' : 'bg-danger' }}">{{ $attempt->percentage }}%</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($attempt->violations_count > 0)
                                                <span class="badge bg-danger-subtle text-danger">{{ $attempt->violations_count }}</span>
                                            @else
                                                <span class="badge bg-success-subtle text-success">0</span>
                                            @endif
                                        </td>
                                        <td>{{ $attempt->submitted_at?->format('Y-m-d H:i') ?? '—' }}</td>
                                        <td>
                                            <a href="javascript:;" data-href="{{ Crypt::encrypt($attempt->id) }}" class="btn btn-icon btn-light-primary btn-sm view-answers" title="عرض الإجابات">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="javascript:;" data-href="{{ Crypt::encrypt($attempt->id) }}" class="btn btn-icon btn-light-danger btn-sm view-wrong-answers" title="الأخطاء فقط">
                                                <i class="bi bi-x-circle"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-muted">لم يقدّم أي طالب على هذا الامتحان بعد</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="text-muted text-center py-4">لا توجد امتحانات لهذه المجموعة بعد.</div>
            @endforelse
        </div>
    </div>
@empty
    <div class="alert alert-light border text-center">لا توجد مجموعات فعالة مسندة إليك بعد.</div>
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
        $('#attempt_answers_modal_content').html('<div class="text-center py-10"><span class="spinner-border" role="status"></span></div>');
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
