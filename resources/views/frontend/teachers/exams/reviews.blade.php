@extends('frontend.layouts.dashboard')
@section('title', 'مراجعة إجابات طلابي')
@section('page-title', 'مراجعة إجابات طلابي')

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-header"><h5 class="mb-0">محاولات بانتظار التصحيح</h5></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle text-center">
                <thead><tr><th>#</th><th>الطالب</th><th>الامتحان</th><th>تاريخ التسليم</th><th>مخالفات الغش</th><th>العمليات</th></tr></thead>
                <tbody>
                    @forelse($pendingAttempts as $index => $attempt)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $attempt->student->name ?? '—' }}</td>
                            <td>{{ $attempt->exam->title ?? '—' }}</td>
                            <td>{{ $attempt->submitted_at?->format('Y-m-d H:i') }}</td>
                            <td>
                                @if($attempt->violations_count > 0)
                                    <span class="badge bg-danger-subtle text-danger"><i class="bi bi-shield-exclamation"></i> {{ $attempt->violations_count }}</span>
                                @else
                                    <span class="badge bg-success-subtle text-success">0</span>
                                @endif
                            </td>
                            <td><a href="{{ route('teacher.exam_reviews.grade', ['id' => Crypt::encrypt($attempt->id)]) }}" class="btn btn-sm btn-primary">تصحيح</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6">لا توجد محاولات بانتظار التصحيح</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header"><h5 class="mb-0">طلبات المراجعة</h5></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle text-center">
                <thead><tr><th>#</th><th>الطالب</th><th>الامتحان</th><th>الرسالة</th><th>العمليات</th></tr></thead>
                <tbody>
                    @forelse($reviewRequests as $index => $review)
                        <tr id="review_row_{{ $review->id }}">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $review->student->name ?? '—' }}</td>
                            <td>{{ $review->attempt->exam->title ?? '—' }}</td>
                            <td>{{ $review->message }}</td>
                            <td>
                                <button class="btn btn-sm btn-success approve-review" data-href="{{ Crypt::encrypt($review->id) }}">اعتماد</button>
                                <button class="btn btn-sm btn-danger reject-review" data-href="{{ Crypt::encrypt($review->id) }}">رفض</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">لا توجد طلبات مراجعة</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    function decideReview(id, decision) {
        var comment = prompt('تعليق (اختياري):', '');
        $.post("{{ route('teacher.exam_reviews.approve') }}", {id: id, decision: decision, teacher_comment: comment, _token: '{{ csrf_token() }}'}, function (data) {
            toastr[data.status](data.message);
            $('#review_row_' + id).fadeOut();
        });
    }
    $(document).on('click', '.approve-review', function () { decideReview($(this).data('href'), 'approved'); });
    $(document).on('click', '.reject-review', function () { decideReview($(this).data('href'), 'rejected'); });
</script>
@stop
