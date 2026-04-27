@extends('admin.layout.master')

@section('title', 'عرض حضور وغياب المجموعة')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('groups.view') }}" class="text-muted text-hover-info">إدارة المجموعات</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark">حضور وغياب المجموعة</li>
@stop

@section('page-content')
    <div class="card mb-7 shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <span class="card-label fw-bold fs-3 mb-1">
                    <i class="ki-duotone ki-magnifier fs-4 text-primary me-2"></i> فلاتر البحث
                </span>
            </div>
        </div>
        <div class="card-body py-4">
            <form id="filter_form" class="row g-5">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">اسم الطالب</label>
                    <input type="text" name="name" id="name" class="form-control form-control-solid"
                        placeholder="ابحث عن اسم الطالب...">
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <span class="card-label fw-bold fs-3 mb-1 text-info">قائمة حضور وغياب المجموعة</span>
            </div>
            <div class="card-toolbar gap-3">
                <a href="{{ URL::previous() }}" class="btn btn-light btn-sm">
                    <i class="ki-duotone ki-arrow-left fs-2"><span class="path1"></span><span class="path2"></span></i> رجوع
                </a>
            </div>
        </div>
        <div class="card-body py-4">
            @include('admin.layout.error')
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center"
                    id="attendance_table">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-6 text-uppercase gs-0">
                            <th class="w-50px text-center"> # </th>
                            <th class="min-w-150px text-center"> اسم الطالب </th>
                            <th class="min-w-150px text-center"> عدد أيام الحضور </th>
                            <th class="min-w-150px text-center"> تفاصيل الأيام </th>
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
        var tableId = 'attendance_table';
        var customAjaxUrl = "{{ route('postt.student.attendance', ['teacher_id' => $teacher_id, 'group_id' => $group_id]) }}";
        var columns = [{
                data: "id",
                name: "id",
                orderable: false,
                searchable: false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: "name",
                name: "name",
                orderable: true
            },
            {
                data: "attendance_count",
                name: "attendance_count",
                className: "text-center"
            },
            {
                data: "days",
                name: "days",
                className: "text-center"
            }
        ];

        var filterFields = ['#name'];

        $(document).ready(function() {
            // Since customAjaxUrl is used, datatableMaster will handle it.
            // We need to set method to POST if the route requires it
            // But datatableMaster usually uses GET. Let's check how to handle POST.
        });
    </script>
    {{-- We might need a slightly custom initialization if it must be POST --}}
    {{-- But let's try with default first. If it fails, I'll update datatableMaster or this view --}}
    @include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'groups'])
@stop
