@extends('frontend.layouts.dashboard')
@section('title', 'محاولات طلابي')
@section('page-title', 'محاولات طلابي')

@section('content')
<div class="card shadow-sm">
    <div class="card-header"><h5 class="mb-0">محاولات طلابي في الامتحانات</h5></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle text-center">
                <thead>
                    <tr>
                        <th>#</th><th>الطالب</th><th>الامتحان</th><th>المجموعة</th>
                        <th>تاريخ التسليم</th><th>الوقت المنقضي</th><th>الدرجة</th><th>الحالة</th><th>مخالفات</th><th>العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $statusLabels = ['in_progress' => 'جارٍ', 'submitted' => 'بانتظار التصحيح', 'graded' => 'تم التصحيح', 'expired' => 'منتهي الوقت'];
                    @endphp
                    @forelse($attempts as $index => $attempt)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $attempt->student->name ?? '—' }}</td>
                            <td>{{ $attempt->exam->title ?? '—' }}</td>
                            <td>{{ $attempt->exam->group->name ?? '—' }}</td>
                            <td>{{ $attempt->submitted_at?->format('Y-m-d H:i') ?? '—' }}</td>
                            <td>{{ $attempt->duration_taken }}</td>
                            <td>
                                @if($attempt->percentage !== null)
                                    <span class="badge {{ $attempt->percentage >= ($attempt->exam->passing_score ?? 50) ? 'bg-success' : 'bg-danger' }}">
                                        {{ $attempt->percentage }}% ({{ $attempt->final_score ?? $attempt->auto_score }}/{{ $attempt->total_marks }})
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $statusLabels[$attempt->status] ?? $attempt->status }}</td>
                            <td>
                                @if($attempt->violations_count > 0)
                                    <span class="badge bg-danger-subtle text-danger"><i class="bi bi-shield-exclamation"></i> {{ $attempt->violations_count }}</span>
                                @else
                                    <span class="badge bg-success-subtle text-success">0</span>
                                @endif
                            </td>
                            <td>
                                <a href="javascript:;" data-href="{{ Crypt::encrypt($attempt->id) }}" class="btn btn-icon btn-light-primary btn-sm view-answers" title="عرض جميع الإجابات">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="javascript:;" data-href="{{ Crypt::encrypt($attempt->id) }}" class="btn btn-icon btn-light-danger btn-sm view-wrong-answers" title="عرض الأسئلة الخاطئة">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10">لا توجد محاولات بعد</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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
