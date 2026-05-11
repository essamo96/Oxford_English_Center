@extends('admin.layout.master')

@section('title', 'اختبارات تحديد المستوى')

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-dark">اختبارات تحديد المستوى</li>
@stop

@section('page-content')
@php $active_menu = 'placement_tests'; @endphp

<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1 text-info">
                <i class="ki-duotone ki-briefcase fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span></i> إدارة اختبارات تحديد المستوى
            </span>
        </div>
        <div class="card-toolbar gap-2">
            <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-right me-1"></i> رجوع
            </a>
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.error')
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center" id="placement_tests_table">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th class="w-30px text-start"> # </th>
                        <th class="min-w-150px"> الطالب </th>
                        <th class="min-w-100px"> تاريخ الاختبار </th>
                        <th class="min-w-100px"> الوقت </th>
                        <th class="min-w-100px"> الحالة </th>
                        <th class="min-w-100px"> طريقة الدفع </th>
                        <th class="min-w-120px"> إيصال الدفع </th>
                        <th class="min-w-80px"> العلامة </th>
                        <th class="min-w-100px"> رصد الدرجة </th>
                        <th class="text-center min-w-100px pe-4"> العمليات </th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold text-center"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Score Modal -->
<div class="modal fade" id="scoreModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">رصد نتيجة اختبار المستوى للطالب: <span id="student-name-modal" class="text-info"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="scoreForm">
                @csrf
                <input type="hidden" id="test-id-modal">
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label fw-bold">العلامة (Score) *</label>
                        <input type="text" name="score" class="form-control" required placeholder="مثال: 85/100">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">المستوى المقترح (Assigned Level) *</label>
                        <select name="assigned_level" class="form-control" required>
                            <option value="">اختر المستوى</option>
                            @php $levels = ['A0', 'A1', 'A2', 'A2+', 'B1', 'B1+', 'B2', 'C1']; @endphp
                            @foreach($levels as $lvl)
                            <option value="{{ $lvl }}">{{ $lvl }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary" id="save-score-btn">حفظ النتيجة</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    var table;
    var tableId = 'placement_tests_table';
    var columns = [
        { data: "id", name: "id", orderable: false, searchable: false, render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
        { data: "student.name", name: "student.name", orderable: true },
        { data: "test_date", name: "test_date", orderable: true },
        { data: "test_time", name: "test_time", orderable: true },
        { data: "status", name: "status", orderable: true },
        { data: "payment_method.name", name: "payment_method.name", defaultContent: "N/A" },
        { data: "payment_receipt", name: "payment_receipt", orderable: false, searchable: false },
        { data: "score", name: "score", defaultContent: "-" },
        { data: "record_score", name: "record_score", orderable: false, searchable: false },
        { data: "action", name: "action", orderable: false, searchable: false }
    ];

    $(document).ready(function() {
        $(document).on('click', '.confirm-payment-btn', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'تأكيد الدفع؟',
                text: "سيتم تغيير حالة الاختبار إلى 'دفع مؤكد'",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'نعم، أكد الدفع',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url("admin/placement_tests/confirm-payment") }}/' + id,
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(response) {
                            if (response.success) {
                                table.ajax.reload();
                                Swal.fire('تم!', response.message, 'success');
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', '.score-btn', function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            $('#test-id-modal').val(id);
            $('#student-name-modal').text(name);
            $('#scoreModal').modal('show');
        });

        $('#scoreForm').on('submit', function(e) {
            e.preventDefault();
            var id = $('#test-id-modal').val();
            var btn = $('#save-score-btn');
            btn.attr('disabled', true).text('جاري الحفظ...');

            $.ajax({
                url: '{{ url("admin/placement_tests/score") }}/' + id,
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $('#scoreModal').modal('hide');
                    if (response.success) {
                        table.ajax.reload();
                        Swal.fire('تم!', response.message, 'success');
                    }
                    btn.attr('disabled', false).text('حفظ النتيجة');
                },
                error: function() {
                    btn.attr('disabled', false).text('حفظ النتيجة');
                    Swal.fire('خطأ!', 'حدث خطأ أثناء حفظ البيانات', 'error');
                }
            });
        });

        $(document).on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "لن تتمكن من التراجع عن هذا!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم، احذف!',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("placement_tests.delete") }}',
                        type: 'POST',
                        data: { id: id, _token: '{{ csrf_token() }}' },
                        success: function(response) {
                            if (response.success) {
                                table.ajax.reload();
                                Swal.fire('تم الحذف!', 'تم حذف السجل بنجاح.', 'success');
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'placement_tests'])
@stop
