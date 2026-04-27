@extends('admin.layout.master')

@section('title', 'إدارة حضور وغياب المدرسين')

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-dark">سجل حضور المدرسين</li>
@stop

@section('page-content')
@php $active_menu = 'absent_teacher'; @endphp

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
                <div class="col-lg-6 col-md-8 mb-4">
                    <label class="form-label fw-semibold">اسم المدرس</label>
                    <input type="text" name="title" id="title" class="form-control form-control-solid searchable" placeholder="ابحث باسم المدرس...">
                </div>
                <div class="col-lg-6 col-md-4 mb-4 d-flex align-items-end gap-2">
                    <button type="reset" id="reset_button" class="btn btn-light-info btn-icon w-40px h-40px shadow-sm" title="إعادة تعيين البحث">
                        <i class="ki-duotone ki-arrows-loop fs-3"><span class="path1"></span><span class="path2"></span></i>
                    </button>
                    <button type="button" class="btn btn-info btn-sm CEmail px-6 h-40px fw-bold" id="CEmail">
                        <i class="ki-duotone ki-lock-2 me-1 fs-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i> اغلاق راتب 
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
                <i class="ki-duotone ki-user-tick fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> سجل حضور وغياب المدرسين
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
            <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center" id="absent_teacher_table">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-6 text-uppercase gs-0">
                        <th class="min-w-50px text-center"> # </th>
                        <th class="w-10px pe-2 text-center">
                            <div class="d-flex align-items-center justify-content-center" style="height: 48px;">
                                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                    <input class="form-check-input h-20px w-20px" type="checkbox" id="selectAll" />
                                </div>
                            </div>
                        </th>
                        <th class="min-w-150px text-center"> اسم المدرس </th>
                        <th class="min-w-100px text-center"> عدد المجموعات </th>
                        <th class="min-w-125px text-center"> اجمالي المحاضرات </th>
                        <th class="min-w-100px text-center"> تفاصيل </th>
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
    var tableId = 'absent_teacher_table';
    var columns = [
        { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false },
        { data: "checkbox", name: "checkbox", orderable: false, searchable: false },
        { data: "teacher_id", name: "teacher_id", orderable: true },
        { data: "group_id", name: "group_id", orderable: true },
        { data: "dayes_number", name: "dayes_number", orderable: true },
        { data: "dayes", name: "dayes", orderable: false, searchable: false },
        { data: "actions", name: "actions", orderable: false, searchable: false, className: "text-center" }
    ];

    var filterFields = ['#title'];

    $(document).ready(function() {
        // Select All checkbox
        $(document).on('change', '#selectAll', function() {
            $('.checkboxes').prop('checked', $(this).is(':checked'));
        });

        $(document).on('click', '#reset_button', function(e) {
            e.preventDefault();
            $(this).closest('form')[0].reset();
            table.ajax.reload();
        });
    });
</script>
@include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'absent_teacher'])
@stop
