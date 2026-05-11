@extends('admin.layout.master')

@section('title', 'إدارة طرق الدفع')

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-dark">إدارة طرق الدفع</li>
@stop

@section('page-content')
@php $active_menu = 'payment_methods'; @endphp

<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1 text-info">
                <i class="ki-duotone ki-credit-cart fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span></i> طرق الدفع المتاحة
            </span>
        </div>
        <div class="card-toolbar gap-2">
            <a href="{{ route('payment_methods.add') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> إضافة طريقة دفع
            </a>
            <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-right me-1"></i> رجوع
            </a>
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.error')
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center" id="payment_methods_table">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th class="w-30px text-start"> # </th>
                        <th class="min-w-200px"> طريقة الدفع </th>
                        <th class="min-w-250px"> بيانات الحساب </th>
                        <th class="min-w-100px text-center">الحالة</th>
                        <th class="text-center min-w-100px pe-4"> العمليات </th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold text-center"></tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    #payment_methods_table td {
        vertical-align: middle !important;
        height: 70px;
    }
    .badge-light-info {
        background-color: #f1faff !important;
        color: #009ef7 !important;
        border: 1px solid #dcf1ff;
    }
</style>
@stop

@section('js')
<script>
    var table;
    var tableId = 'payment_methods_table';
    var columns = [
        { data: "id", name: "id", orderable: false, searchable: false, render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
        { data: "name", name: "name", orderable: true },
        { data: "credentials_formatted", name: "credentials_formatted", orderable: false },
        { data: "is_active", name: "is_active", orderable: true, className: "text-center" },
        { data: "action", name: "action", orderable: false, searchable: false, className: "text-center" }
    ];

    $(document).ready(function() {
        $(document).on('change', '.status-toggle', function() {
            var id = $(this).data('id');
            var status = $(this).prop('checked') ? 1 : 0;
            $.ajax({
                url: '{{ route("payment_methods.status") }}',
                type: 'POST',
                data: { id: id, status: status, _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        toastr.success('تم تحديث الحالة بنجاح');
                    }
                }
            });
        });

        $(document).on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "سيتم حذف طريقة الدفع نهائياً.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم، احذف!',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("payment_methods.delete") }}',
                        type: 'POST',
                        data: { id: id, _token: '{{ csrf_token() }}' },
                        success: function(response) {
                            if (response.success) {
                                table.ajax.reload();
                                Swal.fire('تم الحذف!', 'تم حذف السجل بنجاح.', 'success');
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'payment_methods'])
@stop
