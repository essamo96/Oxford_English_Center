@extends('admin.layout.master')

@section('title', 'إدارة رسوم الطلاب')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">إدارة رسوم الطلاب</li>
@stop

@section('page-content')
@php $active_menu = 'fees'; @endphp
<div class="card mb-7">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                    <span class="path1"></span><span class="path2"></span>
                </i>
                <input type="text" id="name" name="name" class="form-control form-control-solid w-250px ps-13" placeholder="البحث بالاسم" />
            </div>
        </div>
        <div class="card-toolbar">
            <div class="d-flex justify-content-end gap-2">
                @can('admin.groups.add')
                <a href="{{ route('fees.add') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg fs-2"></i> إضافة رسوم
                </a>
                @endcan
                <a href="{{ URL::previous() }}" class="btn btn-light">رجوع</a>
            </div>
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.masterLayouts.error')
        <table id="fees_table" class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center">
            <thead>
                <tr class="text-center text-muted fw-bold fs-7 text-uppercase gs-0">
                    <th class="text-center w-50px"> # </th>
                    <th class="text-center min-w-150px"> الإسم </th>
                    <th class="text-center min-w-150px"> المجموعة </th>
                    <th class="text-center"> الدفعة </th>
                    <th class="text-center"> نوع الدفعة </th>
                    <th class="text-center min-w-125px"> تاريخ الدفعة </th>
                    <th class="text-center min-w-100px"> العمليات </th>
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
    var tableId = 'fees_table';
    var columns = [
        { data: 'id', name: 'id', render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
        { data: 'name', name: 'name' },
        { data: 'group_name', name: 'group_name' },
        { data: 'student_fee_paid', name: 'student_fee_paid' },
        { data: 'fee_type', name: 'fee_type' },
        { data: 'created_at', name: 'created_at' },
        { data: 'actions', name: 'actions', orderable: false, searchable: false }
    ];

    var filterFields = ['#name'];
</script>
@include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'fees'])
@stop
