@extends('admin.layout.master')

@section('title')
إدارة التصنيفات
@stop

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">إدارة التصنيفات</li>
@stop

@section('page-content')
@php $active_menu = 'categories'; @endphp
<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                    <span class="path1"></span><span class="path2"></span>
                </i>
                <input type="text" id="generalSearch" name="name" class="form-control form-control-solid w-250px ps-13" placeholder="البحث بالاسم" />
            </div>
        </div>
        <div class="card-toolbar">
            <div class="d-flex justify-content-end">
                @can('admin.categories.add')
                <a href="{{ route('categories.add') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg fs-2"></i> إضافة
                </a>
                @endcan
            </div>
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.masterLayouts.error')
        <table id="categories_table" class="table align-middle table-row-dashed fs-6 gy-5">
            <thead>
                <tr class="text-center text-muted fw-bold fs-7 text-uppercase gs-0">
                    <th class="text-center">#</th>
                    <th class="text-center">الإسم</th>
                    <th class="text-center">الحالة</th>
                    <th class="text-center">العمليات</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-semibold text-center"></tbody>
        </table>
    </div>
</div>

@include('admin.layout.masterLayouts.modal')
@stop

@section('js')
<script>
    var table;
    var tableId = 'categories_table';
    var columns = [
        { data: 'id', name: 'id', render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
        { data: 'name', name: 'name' },
        { data: 'status', name: 'status' },
        { data: 'actions', name: 'actions', orderable: false, searchable: false }
    ];

    var filterFields = ['#generalSearch'];
</script>
@include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'categories'])
@stop
