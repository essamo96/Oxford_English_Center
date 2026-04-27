@extends('admin.layout.master')

@section('title', 'إدارة طلبات العضوية')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">العضوية</li>
@stop

@section('page-content')
@php $active_menu = 'membership'; @endphp

<div class="card mb-7">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                    <span class="path1"></span><span class="path2"></span>
                </i>
                <input type="text" id="name" name="name" class="form-control form-control-solid w-250px ps-13" placeholder="البحث بالاسم" />
            </div>
            <div class="d-flex align-items-center position-relative my-1 ms-3">
                <input type="date" id="dateFrom" name="dateFrom" class="form-control form-control-solid w-150px" placeholder="من تاريخ" />
            </div>
            <div class="d-flex align-items-center position-relative my-1 ms-3">
                <input type="date" id="dateTo" name="dateTo" class="form-control form-control-solid w-150px" placeholder="إلى تاريخ" />
            </div>
        </div>
        <div class="card-toolbar gap-2">
            @can('admin.membership.add')
                <a class="btn btn-info btn-sm CEmail">
                    <i class="bi bi-envelope"></i> إرسال بريد للكل
                </a>
                <a href="{{ route('membership.add') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> إضافة
                </a>
            @endcan
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body py-4">
        @include('admin.layout.masterLayouts.error')
        <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center" id="membership_table">
            <thead>
                <tr class="text-center text-muted fw-bold fs-7 text-uppercase gs-0">
                    <th class="text-center w-50px"> # </th>
                    <th class="w-10px pe-2 text-center">
                        <input class="form-check-input" type="checkbox" id="selectAll" />
                    </th>
                    <th class="text-center min-w-150px"> الإسم </th>
                    <th class="text-center"> الجوال </th>
                    <th class="text-center"> تاريخ الميلاد </th>
                    <th class="text-center">الوظيفة</th>
                    <th class="text-center min-w-150px"> الايميل </th>
                    <th class="text-center"> الحالة </th>
                    <th class="text-center"> تاريخ التسجيل </th>
                    <th class="text-center"> العمليات </th>
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
    var tableId = 'membership_table';
    var columns = [
        { data: 'id', name: 'id', render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
        { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
        { data: "name", name: "name" },
        { data: "mobile", name: "mobile" },
        { data: "dob", name: "dob" },
        { data: "job", name: "job" },
        { data: "email", name: "email" },
        { data: "status", name: "status" },
        { data: "created_at", name: "created_at" },
        { data: "actions", name: "actions", orderable: false, searchable: false }
    ];

    var filterFields = ['#name', '#dateFrom', '#dateTo'];

    $(document).ready(function() {
        $(document).on('change', '#selectAll', function() {
            $('.select-checkbox, .checkboxes').prop('checked', $(this).is(':checked'));
        });

        // Single student Reply email
        $(document).on('click', '.Reply', function() {
            var id = $(this).data('id');
            window.EmailComposer.send({
                type: 'single',
                id: id,
                url: '{{ route("send.message") }}',
                title: 'إرسال بريد إلكتروني للطالب'
            });
        });

        // Bulk Email (CEmail)
        $(document).on('click', '.CEmail', function() {
            var emails = window.EmailComposer.collectEmails();
            if (emails.length === 0) {
                Swal.fire({ text: 'يجب اختيار طالب واحد على الأقل!', icon: 'warning', buttonsStyling: false, confirmButtonText: 'حسناً', customClass: { confirmButton: 'btn btn-primary' } });
                return;
            }
            window.EmailComposer.send({
                type: 'bulk',
                recipients: emails,
                url: '{{ route("send.CEmail") }}',
                title: 'إرسال بريد إلكتروني للطلاب المحددين'
            });
        });
    });
</script>
@include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'membership'])
@stop

