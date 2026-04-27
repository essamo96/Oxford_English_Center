@extends('admin.layout.master')

@section('title', 'إدارة الطلاب المؤجلين')

@section('css')
<style>
    .swal2-show { border-radius: 20px; }
    .swal2-title { color: #f5a700; }
</style>
@endsection

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-muted">
    <a href="{{ route('students.view') }}" class="text-muted text-hover-info">الطلاب</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-dark">الطلاب المؤجلين</li>
@stop

@section('page-content')
@php $active_menu = 'students'; @endphp

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
                <div class="col-lg-4 col-md-6 mb-4">
                    <label class="form-label fw-semibold">الاسم أو رقم الجوال</label>
                    <input type="text" name="title" id="title" class="form-control form-control-solid searchable" placeholder="البحث بالاسم أو رقم الجوال...">
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <label class="form-label fw-semibold">حالة الطلاب</label>
                    <select class="form-select form-control-solid activeS" name="activeS" id="activeS" data-control="select2" data-hide-search="true">
                        <option value="1" selected>الفعالين</option>
                        <option value="0">الغير فعالين</option>
                    </select>
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
                <i class="ki-duotone ki-profile-circle fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> سجل الطلاب المؤجلين
            </span>
        </div>
        <div class="card-toolbar gap-2">
            @can('admin.students.add')
                <a href="{{ route('students.add') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> إضافة طالب
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
            <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center" id="delayed_students_table">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-6 text-uppercase gs-0">
                        <th class="w-50px text-center"> # </th>
                        <th class="min-w-150px text-center"> الإسم </th>
                        <th class="min-w-125px text-center"> رقم الجوال </th>
                        <th class="min-w-150px text-center"> سبب التأجيل/التنشيط </th>
                        <th class="min-w-125px text-center"> تاريخ التأجيل </th>
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
    var tableId = 'delayed_students_table';
    var customAjaxUrl = "{{ route('students.delay.list') }}";
    var columns = [
        { data: "id", name: "id", orderable: false, searchable: false, render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
        { data: "name", name: "name" },
        { data: "mobile", name: "mobile" },
        { data: "delay_cusess", name: "delay_cusess", className: "text-center" },
        { data: "updated_at", name: "updated_at", className: "text-center" },
        { data: "actions", name: "actions", orderable: false, searchable: false, className: "text-center" }
    ];

    var filterFields = ['#title', '#activeS'];

    $(document).ready(function() {
        $(document).on('click', '#reset_button', function(e) {
            e.preventDefault();
            $(this).closest('form')[0].reset();
            $('.activeS').val('1').trigger('change');
            table.ajax.reload();
        });

        // Delay Reason Modal
        window.showdelay_cusess = function(id) {
            Swal.fire({
                title: 'سبب التأجيل / التنشيط',
                input: 'textarea',
                inputPlaceholder: 'أدخل سبب التأجيل أو التنشيط هنا...',
                inputAttributes: { 'rows': 5, 'maxlength': 2000 },
                showCancelButton: true,
                confirmButtonText: 'حفظ',
                cancelButtonText: 'إلغاء',
                customClass: { confirmButton: 'btn btn-primary', cancelButton: 'btn btn-light' },
                buttonsStyling: false,
                preConfirm: (note) => {
                    if (!note) { Swal.showValidationMessage('يرجى إدخال السبب'); return false; }
                    return $.ajax({
                        type: 'POST',
                        url: '{{ route("DelayCusess.store") }}',
                        data: { note: note, id: id, _token: '{{ csrf_token() }}' }
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'تم الحفظ!', icon: 'success', timer: 1500, showConfirmButton: false });
                    table.ajax.reload(null, false);
                }
            });
        };

        // Note Modal
        window.addnote = function(id) {
            Swal.fire({
                title: 'إضافة ملاحظة للطالب',
                input: 'textarea',
                inputPlaceholder: 'أدخل الملاحظة هنا...',
                inputAttributes: { 'rows': 5, 'maxlength': 2000 },
                showCancelButton: true,
                confirmButtonText: 'إضافة',
                cancelButtonText: 'إلغاء',
                customClass: { confirmButton: 'btn btn-primary', cancelButton: 'btn btn-light' },
                buttonsStyling: false,
                preConfirm: (note) => {
                    if (!note) { Swal.showValidationMessage('يرجى إدخال الملاحظة'); return false; }
                    return $.ajax({
                        type: 'POST',
                        url: '{{ route("notes.store") }}',
                        data: { note: note, id: id, _token: '{{ csrf_token() }}' }
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'تمت الإضافة!', icon: 'success', timer: 1500, showConfirmButton: false });
                    table.ajax.reload(null, false);
                }
            });
        };
    });
</script>
@include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'students'])
@stop
