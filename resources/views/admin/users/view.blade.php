@extends('admin.layout.master')

@section('title', 'إدارة المشتركين')

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-dark">إدارة المشتركين</li>
@stop

@section('page-content')
@php $active_menu = 'users'; @endphp

<div class="card mb-7 shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1">
                <i class="ki-duotone ki-magnifier fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> البحث والفلاتر
            </span>
        </div>
    </div>
    <div class="card-body py-4">
        <form role="form" class="form" id="search_form">
            <div class="row gx-5">
                <div class="col-lg-3 col-md-6 mb-4">
                    <label class="form-label fw-semibold">اسم المستخدم / البحث العام</label>
                    <input type="text" name="username" id="username" class="form-control form-control-solid searchable" placeholder="اسم المستخدم...">
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <label class="form-label fw-semibold">البريد الإلكتروني</label>
                    <input type="text" name="email" id="email" class="form-control form-control-solid searchable" placeholder="البريد الإلكتروني...">
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <label class="form-label fw-semibold">الحالة</label>
                    <select name="status" id="status" class="form-select form-select-solid searchable" data-control="select2" data-hide-search="true">
                        <option value="">الكل</option>
                        <option value="1">نشط</option>
                        <option value="0">غير نشط</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 d-flex align-items-end">
                    <button type="reset" id="reset_button" class="btn btn-light-danger btn-icon w-40px h-40px shadow-sm me-2" title="إعادة تعيين البحث">
                        <i class="bi bi-arrow-clockwise fs-3"></i>
                    </button>
                    <button type="button" id="search_button" class="btn btn-primary btn-sm px-6 shadow-sm">
                        <i class="bi bi-search me-1"></i> بحث
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
                <i class="ki-duotone ki-people fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> إدارة المستخدمين
            </span>
        </div>
        <div class="card-toolbar gap-2">
            @can('admin.users.add')
                <a href="{{ route('users.add') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> إضافة مشترك
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
            <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center" id="users_table">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-6 text-uppercase gs-0">
                        <th class="w-50px text-center"> # </th>
                        <th class="min-w-200px"> اسم المستخدم </th>
                        <th class="min-w-150px"> الإسم الكامل </th>
                        <th class="min-w-150px"> آخر ظهور </th>
                        <th class="min-w-100px"> الحالة </th>
                        <th class="text-center min-w-150px"> العمليات </th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold"></tbody>
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
    var tableId = 'users_table';
    var columns = [
        { data: "id", name: "id", orderable: false, searchable: false, render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
        { data: "username", name: "username", orderable: true },
        { data: "name", name: "name", orderable: true },
        { data: "last_appearance", name: "last_appearance", orderable: false, searchable: false },
        { data: "status", name: "status", orderable: true, searchable: false },
        { data: "actions", name: "actions", orderable: false, searchable: false, className: "text-center" }
    ];

    var filterFields = ['#username', '#email', '#status'];


    $(document).ready(function() {
        $(document).on('click', '#reset_button', function(e) {
            e.preventDefault();
            $(this).closest('form')[0].reset();
            table.ajax.reload();
        });
    });
</script>
@include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'users'])
@stop