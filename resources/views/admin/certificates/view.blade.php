@extends('admin.layout.master')

@section('title', 'إدارة شهادات الطلاب')

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-dark">شهادات الطلاب</li>
@stop

@section('page-content')
@php $active_menu = 'certificates'; @endphp

<div class="card mb-7 shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1">
                <i class="ki-duotone ki-magnifier fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span></i> البحث عن شهادة
            </span>
        </div>
    </div>
    <div class="card-body py-4">
        <form role="form" class="form">
            <div class="row gx-5">
                <div class="col-lg-6 col-md-8 mb-4">
                    <label class="form-label fw-semibold">اسم الطالب أو كود الشهادة</label>
                    <input type="text" name="title" id="title" class="form-control form-control-solid searchable" placeholder="البحث بالاسم أو الكود...">
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
                <i class="ki-duotone ki-shield-tick fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> سجل الشهادات الصادرة
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
            <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center" id="certificates_table">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-6 text-uppercase gs-0">
                        <th class="w-50px text-center"> # </th>
                        <th class="min-w-150px text-center"> اسم الطالب </th>
                        <th class="min-w-125px text-center"> اسم المدرس </th>
                        <th class="min-w-125px text-center"> اسم البرنامج </th>
                        <th class="min-w-125px text-center"> اسم المجموعة </th>
                        <th class="min-w-125px text-center"> كود الشهادة </th>
                        <th class="min-w-100px text-center"> العلامة </th>
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
    var tableId = 'certificates_table';
    var customAjaxUrl = "{{ route('certificates.list') }}";
    var columns = [
        { data: "id", name: "id", orderable: false, searchable: false, render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
        { data: "student_id", name: "student_id", className: "text-center fw-bold" },
        { data: "teacher_id", name: "teacher_id", className: "text-center" },
        { data: "program_id", name: "program_id", className: "text-center" },
        { data: "group_id", name: "group_id", className: "text-center" },
        { data: "Certificate", name: "Certificate", className: "text-center text-primary fw-bold" },
        { data: "total_degree", name: "total_degree", className: "text-center fw-bold" },
        { data: "actions", name: "actions", orderable: false, searchable: false, className: "text-center" }
    ];

    var filterFields = ['#title'];

    $(document).ready(function() {
        $(document).on('click', ".delete-confirm", function () {
            var id = $("#delete_id").val();
            $.ajax({
                type: "POST",
                url: "{{ route('certificates.delete') }}",
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
@include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'certificates'])
@stop