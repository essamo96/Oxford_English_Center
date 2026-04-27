@extends('admin.layout.master')

@section('title', 'إدارة المواعيد والاوقات')

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-dark">إدارة المواعيد والاوقات</li>
@stop

@section('page-content')
@php $active_menu = 'times'; @endphp

<div class="card mb-7 shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1">
                <i class="ki-duotone ki-magnifier fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span></i> البحث في المواعيد
            </span>
        </div>
    </div>
    <div class="card-body py-4">
        <form role="form" class="form">
            <div class="row gx-5">
                <div class="col-lg-6 col-md-8 mb-4">
                    <label class="form-label fw-semibold">اسم اليوم أو الوقت</label>
                    <input type="text" name="title" id="title" class="form-control form-control-solid searchable" placeholder="البحث بالاسم...">
                </div>
                <div class="col-lg-1 col-md-2 mb-4 d-flex align-items-end">
                    <button type="reset" id="reset_button" class="btn btn-light-danger btn-icon w-40px h-40px shadow-sm" title="إعادة تعيين البحث">
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
                <i class="ki-duotone ki-book-open fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> قائمة المواعيد المتاحة
            </span>
        </div>
        <div class="card-toolbar gap-2">
            @can('admin.times.add')
                <a href="{{ route('times.add') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> إضافة موعد جديد
                </a>
            @endcan
            <a href="{{ URL::previous() }}" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-right me-1"></i> رجوع
            </a>
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.error')
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center" id="times_table">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-6 text-uppercase gs-0">
                        <th class="w-50px text-center"> # </th>
                        <th class="min-w-150px text-center"> الأيام </th>
                        <th class="min-w-150px text-center"> الوقت </th>
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
    var tableId = 'times_table';
    var customAjaxUrl = "{{ route('times.list') }}";
    var columns = [
        { data: "id", name: "id", orderable: false, searchable: false, render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
        { data: "days", name: "days", className: "text-center fw-bold text-dark" },
        { data: "times", name: "times", className: "text-center fw-bold text-primary" },
        { data: "status", name: "status", orderable: true, searchable: false, className: "text-center" },
        { data: "actions", name: "actions", orderable: false, searchable: false, className: "text-center" }
    ];

    var filterFields = ['#title'];

    $(document).ready(function() {
        // Status toggle logic is integrated in datatableMaster AJAX handlers or via .status class
        $(document).on('click', ".status", function () {
            var id = $(this).data('href');
            var item = $(this);
            $.ajax({
                type: "POST",
                url: "{{ route('times.status') }}",
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

        // Delete logic handler
        $(document).on('click', ".delete-confirm", function () {
            var id = $("#delete_id").val();
            $.ajax({
                type: "POST",
                url: "{{ route('times.delete') }}",
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
@include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'times'])
@stop
