@extends('frontend.layouts.dashboard')
@section('title', 'امتحانات مجموعاتي')
@section('page-title', 'امتحانات مجموعاتي')

@section('css')
@include('frontend.teachers.exams.parts.design')
@stop

@section('content')
@php
    $labels = ['draft' => 'مسودة', 'scheduled' => 'مجدول', 'published' => 'منشور', 'closed' => 'مغلق'];
    $pillClass = ['draft' => 'tex-pill--muted', 'scheduled' => 'tex-pill--warning', 'published' => 'tex-pill--success', 'closed' => 'tex-pill--danger'];
    $publishedCount = $exams->where('status', 'published')->count();
    $draftCount = $exams->where('status', 'draft')->count();
@endphp

<div class="tex-page-head">
    <div class="tex-page-head__title">
        <span class="tex-page-head__icon"><i class="bi bi-journal-check"></i></span>
        <div>
            <h4>امتحانات مجموعاتي</h4>
            <p>أنشئ وأدر امتحانات مجموعاتك، وتحكّم بحالة نشرها.</p>
        </div>
    </div>
    <a href="{{ route('teacher.exams.add') }}" class="tex-btn-primary">
        <i class="bi bi-plus-lg"></i> إضافة امتحان
    </a>
</div>

<div class="tex-stats">
    <div class="tex-stat">
        <span class="tex-stat__icon" style="background:var(--tex-info-bg); color:var(--tex-info);"><i class="bi bi-journal-text"></i></span>
        <div><div class="tex-stat__value">{{ $exams->count() }}</div><div class="tex-stat__label">إجمالي الامتحانات</div></div>
    </div>
    <div class="tex-stat">
        <span class="tex-stat__icon" style="background:var(--tex-success-bg); color:var(--tex-success);"><i class="bi bi-broadcast"></i></span>
        <div><div class="tex-stat__value">{{ $publishedCount }}</div><div class="tex-stat__label">منشورة الآن</div></div>
    </div>
    <div class="tex-stat">
        <span class="tex-stat__icon" style="background:#eef1f5; color:#4b5568;"><i class="bi bi-pencil"></i></span>
        <div><div class="tex-stat__value">{{ $draftCount }}</div><div class="tex-stat__label">مسودات</div></div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('danger'))
    <div class="alert alert-danger">{{ session('danger') }}</div>
@endif

<div class="tex-card">
    <div class="tex-card__header">
        <p class="tex-card__header-title"><i class="bi bi-table"></i> قائمة الامتحانات</p>
    </div>
    <div class="tex-card__body p-0">
        @if($exams->count())
        <div class="table-responsive">
            <table class="table tex-table align-middle text-center mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th class="text-start">العنوان</th>
                        <th>المجموعة</th>
                        <th>المدة</th>
                        <th>الحالة</th>
                        <th>العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($exams as $index => $exam)
                        <tr>
                            <td class="text-muted">{{ $index + 1 }}</td>
                            <td class="text-start fw-semibold">{{ $exam->title }}</td>
                            <td><span class="tex-pill tex-pill--info"><i class="bi bi-people"></i> {{ $exam->group->name ?? '—' }}</span></td>
                            <td><i class="bi bi-clock text-muted"></i> {{ $exam->duration_minutes }} د</td>
                            <td>
                                <button data-href="{{ Crypt::encrypt($exam->id) }}"
                                    class="tex-pill tex-pill-btn publish-toggle {{ $pillClass[$exam->status] ?? 'tex-pill--muted' }}"
                                    title="اضغط لتبديل حالة النشر">
                                    <i class="bi {{ $exam->status === 'published' ? 'bi-check-circle-fill' : 'bi-circle' }}"></i>
                                    {{ $labels[$exam->status] ?? $exam->status }}
                                </button>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('teacher.exams.edit', ['id' => Crypt::encrypt($exam->id)]) }}" class="tex-icon-btn" title="تعديل">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="javascript:;" data-href="{{ Crypt::encrypt($exam->id) }}" class="tex-icon-btn tex-icon-btn--danger delete-exam" title="حذف">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="tex-empty">
            <i class="bi bi-journal-x"></i>
            <p class="fw-semibold mb-1">لا توجد امتحانات بعد</p>
            <p>ابدأ بإنشاء أول امتحان لإحدى مجموعاتك.</p>
            <a href="{{ route('teacher.exams.add') }}" class="tex-btn-primary mt-3">
                <i class="bi bi-plus-lg"></i> إضافة امتحان
            </a>
        </div>
        @endif
    </div>
</div>
@stop

@section('js')
<script>
$(document).on('click', '.publish-toggle', function () {
    var id = $(this).data('href');
    $.ajax({
        type: 'POST', url: "{{ route('teacher.exams.publish') }}",
        data: {id: id, _token: '{{ csrf_token() }}'},
        success: function () { location.reload(); }
    });
});
$(document).on('click', '.delete-exam', function () {
    var id = $(this).data('href');
    var doDelete = function () {
        $.ajax({
            type: 'POST', url: "{{ route('teacher.exams.delete') }}",
            data: {id: id, _token: '{{ csrf_token() }}'},
            success: function () { location.reload(); }
        });
    };
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'حذف الامتحان؟', text: 'لا يمكن التراجع عن هذا الإجراء.', icon: 'warning',
            showCancelButton: true, confirmButtonText: 'نعم، احذف', cancelButtonText: 'إلغاء', confirmButtonColor: '#c0392b'
        }).then(function (result) { if (result.isConfirmed) doDelete(); });
    } else if (confirm('هل أنت متأكد من الحذف؟')) {
        doDelete();
    }
});
</script>
@stop
