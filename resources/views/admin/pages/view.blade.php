@extends('admin.layout.master')

@section('title', 'إدارة الصفحات الثابتة')

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-dark">إدارة الصفحات الثابتة</li>
@stop

@section('page-content')
@php $active_menu = 'pages'; @endphp

<div class="card mb-7 shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1">
                <i class="ki-duotone ki-magnifier fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> البحث والفلاتر
            </span>
        </div>
    </div>
    <div class="card-body py-4">
        <form role="form" class="form">
            <div class="row gx-5">
                <div class="col-lg-6 col-md-8 mb-4">
                    <label class="form-label fw-semibold">عنوان الصفحة</label>
                    <input type="text" name="title" id="title" class="form-control form-control-solid searchable" placeholder="ابحث بعنوان الصفحة...">
                </div>
                <div class="col-lg-3 col-md-4 mb-4 d-flex align-items-end">
                    <button type="reset" id="reset_button" class="btn btn-light-info btn-icon w-40px h-40px shadow-sm" title="إعادة تعيين البحث">
                        <i class="ki-duotone ki-arrows-loop fs-3"><span class="path1"></span><span class="path2"></span></i>
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
                <i class="ki-duotone ki-element-plus fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> إدارة الصفحات الثابتة
            </span>
        </div>
        <div class="card-toolbar gap-2">
            <a href="{{ url()->previous() }}" class="btn btn-light-info btn-sm fw-bold">
                <i class="ki-duotone ki-black-right me-1 fs-5"></i> رجوع
            </a>
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.error')
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center" id="pages_table">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-6 text-uppercase gs-0">
                        <th class="w-50px text-center"> # </th>
                        <th class="min-w-200px text-center"> عنوان الصفحة </th>
                        <th class="min-w-100px text-center"> الحالة </th>
                        <th class="text-center min-w-150px"> العمليات </th>
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
    var tableId = 'pages_table';
    var columns = [
        { data: "id", name: "id", orderable: false, searchable: false, render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
        { data: "title", name: "title", orderable: true },
        { data: "status", name: "status", orderable: true, searchable: false },
        { data: "actions", name: "actions", orderable: false, searchable: false, className: "text-center" }
    ];

    var filterFields = ['#title'];

    $(document).ready(function() {
        $(document).on('click', '#reset_button', function(e) {
            e.preventDefault();
            $(this).closest('form')[0].reset();
            table.ajax.reload();
        });
    });
</script>
@include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'pages'])
@stop
