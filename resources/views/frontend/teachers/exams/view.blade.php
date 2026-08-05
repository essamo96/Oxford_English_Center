@extends('frontend.layouts.dashboard')
@section('title', 'امتحانات مجموعاتي')
@section('page-title', 'امتحانات مجموعاتي')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">امتحانات مجموعاتي</h5>
        <a href="{{ route('teacher.exams.add') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> إضافة امتحان
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('danger'))
            <div class="alert alert-danger">{{ session('danger') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped align-middle text-center">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>العنوان</th>
                        <th>المجموعة</th>
                        <th>المدة (دقيقة)</th>
                        <th>الحالة</th>
                        <th>العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $index => $exam)
                        @php
                            $labels = ['draft' => 'مسودة', 'scheduled' => 'مجدول', 'published' => 'منشور', 'closed' => 'مغلق'];
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $exam->title }}</td>
                            <td>{{ $exam->group->name ?? '—' }}</td>
                            <td>{{ $exam->duration_minutes }}</td>
                            <td>
                                <button data-href="{{ Crypt::encrypt($exam->id) }}" class="btn btn-sm publish-toggle {{ $exam->status === 'published' ? 'btn-success' : 'btn-secondary' }}">
                                    {{ $labels[$exam->status] ?? $exam->status }}
                                </button>
                            </td>
                            <td>
                                <a href="{{ route('teacher.exams.edit', ['id' => Crypt::encrypt($exam->id)]) }}" class="btn btn-icon btn-light-primary btn-sm"><i class="bi bi-pencil-square"></i></a>
                                <a href="javascript:;" data-href="{{ Crypt::encrypt($exam->id) }}" class="btn btn-icon btn-light-danger btn-sm delete-exam"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">لا توجد امتحانات بعد</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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
    if (!confirm('هل أنت متأكد من الحذف؟')) return;
    var id = $(this).data('href');
    $.ajax({
        type: 'POST', url: "{{ route('teacher.exams.delete') }}",
        data: {id: id, _token: '{{ csrf_token() }}'},
        success: function () { location.reload(); }
    });
});
</script>
@stop
