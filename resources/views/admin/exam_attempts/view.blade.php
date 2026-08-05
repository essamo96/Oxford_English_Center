@extends('admin.layout.master')

@section('title', 'محاولات الطلاب')

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-dark">محاولات الطلاب</li>
@stop

@section('page-content')
<div class="card mb-7 shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1">
                <i class="ki-duotone ki-magnifier fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> البحث والفلترة
            </span>
        </div>
    </div>
    <div class="card-body py-4">
        <form role="form" class="form">
            <div class="row gx-5">
                <div class="col-lg-3 col-md-6 mb-4">
                    <label class="form-label fw-semibold">اسم الطالب</label>
                    <input type="text" name="title" id="title" class="form-control form-control-solid searchable" placeholder="بحث...">
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <label class="form-label fw-semibold">الامتحان</label>
                    <select name="exam_id" id="exam_id" class="form-select form-select-solid searchable" data-control="select2">
                        <option value="">كل الامتحانات</option>
                        @foreach($exams as $exam)
                            <option value="{{ $exam->id }}">{{ $exam->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <label class="form-label fw-semibold">المجموعة</label>
                    <select name="group_id" id="group_id" class="form-select form-select-solid searchable" data-control="select2">
                        <option value="">كل المجموعات</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <label class="form-label fw-semibold">الحالة</label>
                    <select name="status" id="status" class="form-select form-select-solid searchable">
                        <option value="">الكل</option>
                        <option value="in_progress">جارٍ</option>
                        <option value="submitted">بانتظار التصحيح</option>
                        <option value="graded">تم التصحيح</option>
                        <option value="expired">منتهي الوقت</option>
                    </select>
                </div>
                <div class="col-lg-1 col-md-6 mb-4 d-flex align-items-end">
                    <button type="reset" id="reset_button" class="btn btn-light-danger btn-icon w-40px h-40px shadow-sm" title="إعادة تعيين">
                        <i class="bi bi-arrow-clockwise fs-3"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1 text-info">
                <i class="ki-duotone ki-profile-user fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> محاولات الطلاب
            </span>
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.masterLayouts.error')
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center" id="exam_attempts_table">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-6 text-uppercase gs-0">
                        <th class="w-50px text-center"> # </th>
                        <th class="min-w-150px text-center"> الطالب </th>
                        <th class="min-w-150px text-center"> الامتحان </th>
                        <th class="min-w-120px text-center"> المجموعة </th>
                        <th class="min-w-100px text-center"> تاريخ التسليم </th>
                        <th class="min-w-100px text-center"> الوقت المنقضي </th>
                        <th class="min-w-120px text-center"> الدرجة </th>
                        <th class="min-w-100px text-center"> الحالة </th>
                        <th class="min-w-80px text-center"> مخالفات </th>
                        <th class="text-center min-w-100px"> العمليات </th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold text-center"></tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('modal')
    @include('admin.layout.masterLayouts.modal')

    <div class="modal fade" id="attempt_answers_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-800px">
            <div class="modal-content">
                <div class="modal-header pb-0 border-0 justify-content-between">
                    <h5 class="modal-title" id="attempt_answers_modal_title">إجابات المحاولة</h5>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body scroll-y pt-3 pb-10" id="attempt_answers_modal_content" style="max-height: 75vh; overflow-y:auto;"></div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    var table;
    var tableId = 'exam_attempts_table';
    var customAjaxUrl = "{{ route('exam_attempts.list') }}";
    var columns = [
        { data: "id", name: "id", orderable: false, searchable: false, render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
        { data: "student", name: "student" },
        { data: "exam", name: "exam" },
        { data: "group", name: "group" },
        { data: "submitted_at", name: "submitted_at" },
        { data: "duration_taken", name: "duration_taken", orderable: false, searchable: false },
        { data: "score", name: "score" },
        { data: "status", name: "status" },
        { data: "violations_count", name: "violations_count" },
        { data: "actions", name: "actions", orderable: false, searchable: false }
    ];

    var filterFields = ['#title', '#exam_id', '#group_id', '#status'];

    $(document).ready(function() {
        $(document).on('click', '#reset_button', function(e) {
            e.preventDefault();
            $(this).closest('form')[0].reset();
            $('.searchable').trigger('change');
            table.ajax.reload();
        });

        function showAttemptModal(url, id, title) {
            $('#attempt_answers_modal_title').text(title);
            $('#attempt_answers_modal_content').html('<div class="text-center py-10"><span class="spinner-border w-50px h-50px" role="status"></span></div>');
            $('#attempt_answers_modal').modal('show');
            $.ajax({
                url: url,
                type: 'POST',
                data: { id: id, _token: '{{ csrf_token() }}' },
                success: function (response) {
                    $('#attempt_answers_modal_content').html(response);
                },
                error: function () {
                    $('#attempt_answers_modal_content').html('<div class="alert alert-danger">حدث خطأ أثناء تحميل البيانات</div>');
                }
            });
        }

        $(document).on('click', '.view-answers', function () {
            showAttemptModal("{{ route('exam_attempts.answers') }}", $(this).data('href'), 'جميع الإجابات');
        });

        $(document).on('click', '.view-wrong-answers', function () {
            showAttemptModal("{{ route('exam_attempts.wrong_answers') }}", $(this).data('href'), 'الأسئلة الخاطئة');
        });
    });
</script>
@include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'exam_attempts'])
@stop
