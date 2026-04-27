@extends('admin.layout.master')

@section('title', 'المجموعات المنتهية')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('groups.view') }}" class="text-muted text-hover-info">إدارة المجموعات</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark">المجموعات المنتهية</li>
@stop

@section('page-content')
    <div class="card mb-7 shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <span class="card-label fw-bold fs-3 mb-1">
                    <i class="ki-duotone ki-magnifier fs-4 text-primary me-2"></i> فلاتر البحث في المجموعات المنتهية
                </span>
            </div>
            <div class="card-toolbar">
                <button type="button" id="reset_button" class="btn btn-sm btn-light-primary">
                    تصفية الفلاتر
                </button>
            </div>
        </div>
        <div class="card-body py-4">
            <form id="filter_form" class="row g-5">
                <div class="col-md-6 mb-4">
                    <label class="form-label fw-semibold">اسم المجموعة</label>
                    <input type="text" name="title" id="title" class="form-control form-control-solid"
                        placeholder="ابحث عن اسم المجموعة...">
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <span class="card-label fw-bold fs-3 mb-1">قائمة المجموعات المنتهية</span>
            </div>
            <div class="card-toolbar gap-3">
                @can('admin.groups.add')
                    <a href="{{ route('groups.add') }}" class="btn btn-primary btn-sm">
                        <i class="ki-duotone ki-plus fs-3"></i> إضافة مجموعة
                    </a>
                @endcan
                <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">
                    <i class="ki-duotone ki-arrow-left fs-2"><span class="path1"></span><span class="path2"></span></i> رجوع
                </a>
            </div>
        </div>
        <div class="card-body py-4">
            @include('admin.layout.masterLayouts.error')
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center"
                    id="ended_groups_table">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-6 text-uppercase gs-0">
                            <th class="w-50px text-center"> # </th>
                            <th class="min-w-150px text-center"> اسم المجموعة </th>
                            <th class="min-w-150px text-center"> المدرس </th>
                            <th class="min-w-150px text-center"> البرنامج </th>
                            <th class="w-100px text-center"> عدد الطلاب </th>
                            <th class="min-w-150px text-center"> الموعد </th>
                            <th class="min-w-100px text-center"> الحالة </th>
                            <th class="text-center min-w-125px"> العمليات </th>
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
        var tableId = 'ended_groups_table';
        var ajaxUrl = "program/groups/e";
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
                data: "teacher_name",
                name: "teacher_name"
            },
            {
                data: "program_name",
                name: "program_name"
            },
            {
                data: "studens_no",
                name: "studens_no",
                className: "text-center"
            },
            {
                data: "time_day",
                name: "time_day",
                className: "text-center"
            },
            {
                data: "status",
                name: "status",
                orderable: true,
                searchable: false
            },
            {
                data: "actions",
                name: "actions",
                orderable: false,
                searchable: false,
                className: "text-center"
            }
        ];

        var filterFields = ['#title'];

        $(document).ready(function() {
            $(document).on('click', '#reset_button', function(e) {
                e.preventDefault();
                $('#filter_form')[0].reset();
                table.ajax.reload();
            });
        });
    </script>
    @include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'groups'])
@stop
