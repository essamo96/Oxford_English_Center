@extends('admin.layout.master')

@section('title', 'إدارة مجموعة الطالب - ' . $student_name)

@section('page-title', 'إدارة مجموعة الطالب')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('students.view') }}" class="text-muted text-hover-info">إدارة الطلاب</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark">مجموعات الطالب</li>
@stop

@section('page-content')
    <div class="card mb-7 shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <span class="card-label fw-bold fs-3 mb-1">
                    <i class="ki-duotone ki-magnifier fs-4 text-primary me-2"></i> البحث في المجموعات
                </span>
            </div>
        </div>
        <div class="card-body py-4">
            <form id="filter_form" class="row g-5">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">اسم المجموعة</label>
                    <input type="text" name="name" id="searchInput" class="form-control form-control-solid" placeholder="بحث باسم المجموعة">
                </div>
                <div class="col-md-6 d-flex align-items-end gap-2">
                    <button type="button" id="resetFilter" class="btn btn-light-danger btn-sm">
                        <i class="ki-duotone ki-arrows-loop fs-4 me-1"><span class="path1"></span><span class="path2"></span></i> تصفية
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <span class="card-label fw-bold fs-3 mb-1 text-info">
                    مجموعات الطالب: <span class="text-dark">{{ $student_name }}</span>
                </span>
            </div>
            <div class="card-toolbar gap-3">
                @can('admin.groups.add')
                <a href="{{ route('students.groups.add', ['student_id' => Crypt::encrypt($student_id)]) }}" class="btn btn-primary btn-sm">
                    <i class="ki-duotone ki-plus fs-3"></i> إضافة مجموعة للطالب
                </a>
                @endcan
                <a href="{{ route('students.view') }}" class="btn btn-light btn-sm">
                    <i class="ki-duotone ki-arrow-left fs-2"><span class="path1"></span><span class="path2"></span></i> رجوع
                </a>
            </div>
        </div>
        <div class="card-body py-4">
            @include('admin.layout.masterLayouts.error')
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center" id="categories_table">
                    <thead>
                        <tr class="text-center text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="w-50px text-center"> # </th>
                            <th class="min-w-150px text-start"> المجموعة </th>
                            <th class="min-w-125px text-center"> البرنامج </th>
                            <th class="min-w-125px text-center"> حالة المجموعة </th>
                            <th class="min-w-125px text-center"> حالة الطالب </th>
                            <th class="min-w-125px text-center"> المعلم </th>
                            <th class="min-w-125px text-center"> الأيام </th>
                            <th class="min-w-125px text-center"> الوقت </th>
                            <th class="text-center min-w-100px"> العمليات </th>
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
    var tableId = 'categories_table';
    var customAjaxUrl = "{{ route('students.groups.list',[ 'id' => request()->id]) }}";
    
    var columns = [
        { data: null, orderable: false, searchable: false, render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
        { data: "name", name: "name", className: "text-start fw-bold" },
        { data: "programName", name: "programName" },
        { data: "statusOfGroup", name: "statusOfGroup" },
        { data: "status", name: "status" },
        { data: "teacherName", name: "teacherName" },
        { data: "calender", name: "calender" },
        { data: "time", name: "time" },
        { data: "actions", name: "actions", orderable: false, searchable: false, className: "text-center" }
    ];

    $(document).ready(function() {
        // Initialize DataTable
        table = $('#' + tableId).DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: customAjaxUrl,
                data: function (d) {
                    d.title = $('#searchInput').val();
                }
            },
            columns: columns,
            language: { url: "{{ url('assets/plugins/datatables/ar.json') }}" },
            "sDom": `<'table-responsive'tr>
                     <'row align-items-center justify-content-center'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>`,
        });

        // Search functionalily
        $('#searchInput').on('input', function() {
            table.draw();
        });

        // Reset filter
        $('#resetFilter').on('click', function() {
            $('#searchInput').val('');
            table.draw();
        });

        // Handle delete via modal
        $(document).on('click', '.delete', function() {
            var href = $(this).data('href');
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "لن تتمكن من التراجع عن هذا الإجراء!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'نعم، احذف!',
                cancelButtonText: 'إلغاء',
                customClass: { confirmButton: 'btn btn-danger', cancelButton: 'btn btn-light' }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('groups.student.deleted') }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: href
                        },
                        success: function (data) {
                            Swal.fire('تم الحذف!', data.message, 'success');
                            table.draw();
                        }
                    });
                }
            });
        });

        // Toggle Status
        $(document).on('click', ".status", function() {
            var id = $(this).data('href');
            $.ajax({
                type: "POST",
                url: "{{ route('students.status')}}",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                success: function(data) {
                    table.draw();
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message,
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            });
        });
    });
</script>
@stop
