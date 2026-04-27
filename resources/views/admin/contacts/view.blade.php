@extends('admin.layout.master')

@section('title', 'إدارة اتصل بنا')

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-dark">إدارة اتصل بنا</li>
@stop

@section('page-content')
@php $active_menu = 'contacts'; @endphp

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
                <div class="col-lg-3 col-md-6 mb-4">
                    <label class="form-label fw-semibold">الاسم</label>
                    <input type="text" name="name" id="name" class="form-control form-control-solid searchable" placeholder="البحث بالاسم...">
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <label class="form-label fw-semibold">البريد الإلكتروني</label>
                    <input type="text" name="email" id="email" class="form-control form-control-solid searchable" placeholder="البحث بالبريد...">
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <label class="form-label fw-semibold">الجوال</label>
                    <input type="text" name="mobile" id="mobile" class="form-control form-control-solid searchable" placeholder="البحث بالجوال...">
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
                <i class="ki-duotone ki-element-plus fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> رسائل اتصل بنا
            </span>
        </div>
        <div class="card-toolbar gap-2">
            <a href="{{ URL::previous() }}" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-right me-1"></i> رجوع
            </a>
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.error')
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center" id="contacts_table">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-6 text-uppercase gs-0">
                        <th class="w-50px text-center"> # </th>
                        <th class="min-w-150px text-center"> الإسم </th>
                        <th class="min-w-150px text-center"> البريد الإلكتروني </th>
                        <th class="min-w-125px text-center"> الجوال </th>
                        <th class="min-w-150px text-center"> الموضوع </th>
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
    var tableId = 'contacts_table';
    var customAjaxUrl = "{{ route('contacts.list') }}";
    var columns = [
        { data: "id", name: "id", orderable: false, searchable: false, render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
        { data: "name", name: "name", className: "fw-bold" },
        { data: "email", name: "email" },
        { data: "mobile", name: "mobile" },
        { data: "subject", name: "subject" },
        { data: "status", name: "status", className: "text-center" },
        { data: "actions", name: "actions", orderable: false, searchable: false, className: "text-center" }
    ];

    var filterFields = ['#name', '#email', '#mobile'];

    $(document).ready(function() {
        $(document).on('click', '#reset_button', function(e) {
            e.preventDefault();
            $(this).closest('form')[0].reset();
            table.ajax.reload();
        });

        // Status toggle
        $(document).on('click', ".status", function () {
            var id = $(this).data('href');
            var item = $(this);
            $.ajax({
                type: "POST",
                url: "{{ route('contacts.status') }}",
                data: {'id': id, '_token': '{{ csrf_token() }}'},
                success: function (data) {
                    if (data.type == 'yes') {
                        item.removeClass("btn-light-danger").addClass("btn-light-success").html('<i class="bi bi-check-circle fs-5"></i> تم التواصل');
                    } else if (data.type == 'no') {
                        item.removeClass("btn-light-success").addClass("btn-light-danger").html('<i class="bi bi-x-circle fs-5"></i> قيد الإنتظار');
                    }
                    toastr[data.status](data.message);
                }
            });
        });

        // Delete handler
        $(document).on('click', ".delete-confirm", function () {
            var id = $("#delete_id").val();
            $.ajax({
                type: "POST",
                url: "{{ route('contacts.delete') }}",
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
@include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'contacts'])
@stop