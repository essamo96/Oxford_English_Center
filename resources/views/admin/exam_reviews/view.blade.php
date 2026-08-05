@extends('admin.layout.master')

@section('title', 'مراجعة الإجابات')

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-dark">مراجعة الإجابات</li>
@stop

@section('page-content')
<div class="card shadow-sm mb-7">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1 text-info">
                <i class="ki-duotone ki-check-square fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span></i>
                محاولات بانتظار التصحيح اليدوي
            </span>
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.masterLayouts.error')
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-6 text-uppercase gs-0">
                        <th class="text-center">#</th>
                        <th class="text-center">الطالب</th>
                        <th class="text-center">الامتحان</th>
                        <th class="text-center">تاريخ التسليم</th>
                        <th class="text-center">مخالفات الغش</th>
                        <th class="text-center">العمليات</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold text-center">
                    @forelse($pendingAttempts as $index => $attempt)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $attempt->student->name ?? '—' }}</td>
                            <td>{{ $attempt->exam->title ?? '—' }}</td>
                            <td>{{ $attempt->submitted_at?->format('Y-m-d H:i') }}</td>
                            <td>
                                @if($attempt->violations_count > 0)
                                    <span class="badge badge-light-danger"><i class="bi bi-shield-exclamation"></i> {{ $attempt->violations_count }}</span>
                                @else
                                    <span class="badge badge-light-success">0</span>
                                @endif
                            </td>
                            <td>
                                @can('admin.exam_reviews.grade')
                                <a href="{{ route('exam_reviews.grade', ['id' => Crypt::encrypt($attempt->id)]) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil-square"></i> تصحيح
                                </a>
                                @endcan
                            </td>
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
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1 text-warning">
                <i class="ki-duotone ki-message-question fs-3 text-warning me-2"><span class="path1"></span><span class="path2"></span></i>
                طلبات المراجعة من الطلاب
            </span>
        </div>
    </div>
    <div class="card-body py-4">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-6 text-uppercase gs-0">
                        <th class="text-center">#</th>
                        <th class="text-center">الطالب</th>
                        <th class="text-center">الامتحان</th>
                        <th class="text-center">الرسالة</th>
                        <th class="text-center">العمليات</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold text-center">
                    @forelse($reviewRequests as $index => $review)
                        <tr id="review_row_{{ $review->id }}">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $review->student->name ?? '—' }}</td>
                            <td>{{ $review->attempt->exam->title ?? '—' }}</td>
                            <td class="text-start">{{ $review->message }}</td>
                            <td>
                                @can('admin.exam_reviews.approve')
                                <button class="btn btn-sm btn-success approve-review" data-href="{{ Crypt::encrypt($review->id) }}">اعتماد</button>
                                <button class="btn btn-sm btn-danger reject-review" data-href="{{ Crypt::encrypt($review->id) }}">رفض</button>
                                @endcan
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
        $.post("{{ route('exam_reviews.approve') }}", {id: id, decision: decision, teacher_comment: comment, _token: '{{ csrf_token() }}'}, function (data) {
            toastr[data.status](data.message);
            $('#review_row_' + id).fadeOut();
        });
    }
    $(document).on('click', '.approve-review', function () { decideReview($(this).data('href'), 'approved'); });
    $(document).on('click', '.reject-review', function () { decideReview($(this).data('href'), 'rejected'); });
</script>
@stop
