@extends('admin.layout.master')

@section('title', 'إدارة الفروع')

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-dark">إدارة الفروع</li>
@stop

@section('page-content')
@php $active_menu = 'branches'; @endphp

<div class="card mb-7 shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1">
                <i class="ki-duotone ki-magnifier fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span></i> البحث والفلاتر
            </span>
        </div>
    </div>
    <div class="card-body py-4">
        <form role="form" class="form">
            <div class="row gx-5">
                <div class="col-lg-4 col-md-5 mb-4">
                    <label class="form-label fw-semibold">اسم الفرع</label>
                    <input type="text" name="name" id="name" class="form-control form-control-solid searchable" placeholder="ابحث باسم الفرع...">
                </div>
                <div class="col-lg-3 col-md-4 mb-4">
                    <label class="form-label fw-semibold">الحالة</label>
                    <select name="status" id="status" class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="اختر الحالة">
                        <option value="all">الكل</option>
                        <option value="1">مفعل</option>
                        <option value="0">غير مفعل</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-3 mb-4 d-flex align-items-end gap-2">
                    <button type="button" onclick="table.ajax.reload();" class="btn btn-primary btn-icon w-40px h-40px shadow-sm" title="بحث">
                        <i class="bi bi-search fs-3"></i>
                    </button>
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
                <i class="ki-duotone ki-geolocation fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span></i> إدارة الفروع
            </span>
        </div>
        <div class="card-toolbar gap-2">
            @can('admin.branches.add')
                <a href="{{ route('branches.add') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> إضافة فرع
                </a>
            @endcan
            <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-right me-1"></i> رجوع
            </a>
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.error')
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center" id="branches_table">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-6 text-uppercase gs-0">
                        <th class="w-50px text-center">#</th>
                        <th class="min-w-200px text-center">اسم الفرع (عربي)</th>
                        <th class="min-w-200px text-center">اسم الفرع (إنجليزي)</th>
                        <th class="min-w-100px text-center">عدد الطلاب</th>
                        <th class="min-w-100px text-center">الحالة</th>
                        <th class="text-center min-w-120px">العمليات</th>
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
    var tableId = 'branches_table';
    var columns = [
        { data: "id", name: "id", orderable: false, searchable: false, render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
        { data: "name_ar",        name: "name_ar",        orderable: true,  className: "text-start" },
        { data: "name_en",        name: "name_en",        orderable: true,  className: "text-start" },
        { data: "students_count", name: "students_count", orderable: false, searchable: false, className: "text-center" },
        { data: "status",         name: "status",         orderable: true,  searchable: false },
        { data: "actions",        name: "actions",        orderable: false, searchable: false, className: "text-center" }
    ];

    var filterFields = ['#name', '#status'];

    $(document).ready(function() {
        $(document).on('click', '#reset_button', function(e) {
            e.preventDefault();
            $(this).closest('form')[0].reset();
            table.ajax.reload();
        });
    });
</script>
@include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'branches'])
@stop
