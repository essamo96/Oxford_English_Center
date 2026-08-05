@extends('admin.layout.master')

@section('title', $category === 'placement' ? 'اختبارات تحديد المستوى' : 'امتحانات المجموعات')

@php
    $isPlacement = $category === 'placement';
    $viewRoute = $isPlacement ? 'exam_placement_tests.view' : 'group_exams.view';
    $listRoute = $isPlacement ? 'exam_placement_tests.list' : 'group_exams.list';
    $addRoute = $isPlacement ? 'exam_placement_tests.add' : 'group_exams.add';
    $editRouteName = $isPlacement ? 'exam_placement_tests.edit' : 'group_exams.edit';
    $statusRoute = $isPlacement ? 'exam_placement_tests.status' : 'group_exams.status';
    $deleteRoute = $isPlacement ? 'exam_placement_tests.delete' : 'group_exams.delete';
    $previewRoute = $isPlacement ? 'exam_placement_tests.preview' : 'group_exams.preview';
    $questionsRoute = $isPlacement ? 'exam_placement_tests.questions' : 'group_exams.questions';
    $permPrefix = $isPlacement ? 'exam_placement_tests' : 'group_exams';
@endphp

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-dark">{{ $isPlacement ? 'اختبارات تحديد المستوى' : 'امتحانات المجموعات' }}</li>
@stop

@section('page-content')
<div class="card mb-7 shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1">
                <i class="ki-duotone ki-magnifier fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> البحث
            </span>
        </div>
    </div>
    <div class="card-body py-4">
        <form role="form" class="form">
            <div class="row gx-5">
                <div class="col-lg-4 col-md-6 mb-4">
                    <label class="form-label fw-semibold">اسم الامتحان</label>
                    <input type="text" name="title" id="title" class="form-control form-control-solid searchable" placeholder="بحث...">
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <label class="form-label fw-semibold">الحالة</label>
                    <select name="status" id="status" class="form-select form-select-solid searchable">
                        <option value="">الكل</option>
                        <option value="draft">مسودة</option>
                        <option value="scheduled">مجدول</option>
                        <option value="published">منشور</option>
                        <option value="closed">مغلق</option>
                    </select>
                </div>
                @if(!$isPlacement)
                <div class="col-lg-3 col-md-6 mb-4">
                    <label class="form-label fw-semibold">البرنامج</label>
                    <select name="program_id" id="program_id" class="form-select form-select-solid searchable" data-control="select2">
                        <option value="">الكل</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}">{{ $program->title }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
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
                <i class="ki-duotone ki-teacher fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                {{ $isPlacement ? 'اختبارات تحديد المستوى' : 'امتحانات المجموعات' }}
            </span>
        </div>
        <div class="card-toolbar gap-2">
            @can('admin.' . $permPrefix . '.add')
                <a href="{{ route($addRoute) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> إضافة امتحان
                </a>
            @endcan
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.masterLayouts.error')
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center" id="exams_table">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-6 text-uppercase gs-0">
                        <th class="w-50px text-center"> # </th>
                        <th class="min-w-200px text-center"> العنوان </th>
                        @if(!$isPlacement)
                        <th class="min-w-150px text-center"> المجموعة </th>
                        @endif
                        <th class="min-w-100px text-center"> المدة (دقيقة) </th>
                        <th class="min-w-100px text-center"> الحالة </th>
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

    <div class="modal fade" id="exam_preview_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-800px">
            <div class="modal-content">
                <div class="modal-header pb-0 border-0 justify-content-between">
                    <h5 class="modal-title" id="exam_preview_modal_title">معاينة</h5>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body scroll-y pt-3 pb-10" id="exam_preview_modal_content" style="max-height: 75vh; overflow-y:auto;"></div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    var table;
    var tableId = 'exams_table';
    var customAjaxUrl = "{{ route($listRoute) }}";
    var columns = [
        { data: "id", name: "id", orderable: false, searchable: false, render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
        { data: "title", name: "title", className: "fw-bold text-start" },
        @if(!$isPlacement)
        { data: "group", name: "group" },
        @endif
        { data: "duration_minutes", name: "duration_minutes" },
        { data: "status", name: "status" },
        { data: "actions", name: "actions", orderable: false, searchable: false }
    ];

    var filterFields = ['#title', '#status'@if(!$isPlacement), '#program_id'@endif];

    $(document).ready(function() {
        $(document).on('click', '#reset_button', function(e) {
            e.preventDefault();
            $(this).closest('form')[0].reset();
            $('.searchable').trigger('change');
            table.ajax.reload();
        });

        $(document).on('click', ".status", function () {
            var id = $(this).data('href');
            $.ajax({
                type: "POST",
                url: "{{ route($statusRoute) }}",
                data: {'id': id, '_token': '{{ csrf_token() }}'},
                success: function (data) {
                    toastr[data.status](data.message);
                    table.ajax.reload();
                }
            });
        });

        $(document).on('click', ".delete-confirm", function () {
            var id = $("#delete_id").val();
            $.ajax({
                type: "POST",
                url: "{{ route($deleteRoute) }}",
                data: {'id': id, '_token': '{{ csrf_token() }}'},
                success: function (data) {
                    $('#confirm').modal('hide');
                    toastr[data.status](data.message);
                    table.ajax.reload();
                }
            });
        });

        $(document).on('click', '.preview-exam', function () {
            showExamModal("{{ route($previewRoute) }}", $(this).data('href'), 'معاينة الامتحان كما سيراه الطالب');
        });

        $(document).on('click', '.exam-questions', function () {
            showExamModal("{{ route($questionsRoute) }}", $(this).data('href'), 'الأسئلة المرتبطة بالامتحان');
        });
    });

    function showExamModal(url, id, title) {
        $('#exam_preview_modal_title').text(title);
        $('#exam_preview_modal_content').html('<div class="text-center py-10"><span class="spinner-border w-50px h-50px" role="status"></span></div>');
        $('#exam_preview_modal').modal('show');
        $.ajax({
            url: url,
            type: 'POST',
            data: { id: id, _token: '{{ csrf_token() }}' },
            success: function (response) {
                $('#exam_preview_modal_content').html(response);
            },
            error: function () {
                $('#exam_preview_modal_content').html('<div class="alert alert-danger">حدث خطأ أثناء تحميل البيانات</div>');
            }
        });
    }
</script>
@include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => $active_menu])
@stop
