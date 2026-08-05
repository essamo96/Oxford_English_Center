@extends('admin.layout.master')

@section('title', 'تصنيفات الأسئلة')

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-dark">تصنيفات الأسئلة</li>
@stop

@section('page-content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1">
                <i class="ki-duotone ki-category fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> تصنيفات الأسئلة (المهارات)
            </span>
        </div>
        <div class="card-toolbar gap-2">
            @can('admin.exam_skills.add')
                <a href="{{ route('exam_skills.add') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> إضافة تصنيف
                </a>
            @endcan
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.masterLayouts.error')
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center" id="exam_skills_table">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-6 text-uppercase gs-0">
                        <th class="w-50px text-center"> # </th>
                        <th class="min-w-150px text-center"> الاسم (عربي) </th>
                        <th class="min-w-150px text-center"> الاسم (إنجليزي) </th>
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
@stop

@section('js')
<script>
    var table;
    var tableId = 'exam_skills_table';
    var customAjaxUrl = "{{ route('exam_skills.list') }}";
    var columns = [
        { data: "id", name: "id", orderable: false, searchable: false, render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
        { data: "name_ar", name: "name_ar", className: "fw-bold" },
        { data: "name_en", name: "name_en" },
        { data: "status", name: "status", className: "text-center" },
        { data: "actions", name: "actions", orderable: false, searchable: false, className: "text-center" }
    ];

    $(document).ready(function() {
        $(document).on('click', ".status", function () {
            var id = $(this).data('href');
            var item = $(this);
            $.ajax({
                type: "POST",
                url: "{{ route('exam_skills.status') }}",
                data: {'id': id, '_token': '{{ csrf_token() }}'},
                success: function (data) {
                    if (data.type == 'yes') {
                        item.removeClass("btn-light-danger").addClass("btn-light-success").html('<i class="bi bi-check-circle fs-5"></i> فعال');
                    } else if (data.type == 'no') {
                        item.removeClass("btn-light-success").addClass("btn-light-danger").html('<i class="bi bi-x-circle fs-5"></i> غير فعال');
                    }
                    toastr[data.status](data.message);
                }
            });
        });

        $(document).on('click', ".delete-confirm", function () {
            var id = $("#delete_id").val();
            $.ajax({
                type: "POST",
                url: "{{ route('exam_skills.delete') }}",
                data: {'id': id, '_token': '{{ csrf_token() }}'},
                success: function (data) {
                    $('#confirm').modal('hide');
                    toastr[data.status](data.message);
                    table.ajax.reload();
                }
            });
        });
    });
</script>
@include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'exam_skills'])
@stop
