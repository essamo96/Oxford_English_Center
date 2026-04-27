@extends('admin.layout.master')

@section('title')
    تقييمات الطلاب - {{ $info->name }}
@stop

@section('page-title')
    تقييمات الطلاب: {{ $info->name }}
@stop

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('groups.view') }}" class="text-muted text-hover-info">إدارة المجموعات</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">{{ $info->name }}</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">تقييمات الطلاب</li>
@stop

@section('page-content')
    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                        <span class="path1"></span><span class="path2"></span>
                    </i>
                    <input type="text" id="name" name="name" class="form-control form-control-solid w-250px ps-13"
                        placeholder="بحث باسم الطالب..." />
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('groups.student.view', ['id' => Crypt::encrypt($id)]) }}" class="btn btn-light">
                        <i class="ki-duotone ki-arrow-left fs-2"><span class="path1"></span><span class="path2"></span></i>
                        رجوع
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body py-4">
            @include('admin.layout.masterLayouts.error')
            <div class="table-responsive">
                <table id="evaluations_table"
                    class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center">
                    <thead>
                        <tr class="text-center text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="text-center w-50px"> # </th>
                            <th class="text-center min-w-150px"> اسم الطالب </th>
                            <th class="text-center"> Writing </th>
                            <th class="text-center"> Reading </th>
                            <th class="text-center"> Speaking </th>
                            <th class="text-center"> Listening </th>
                            <th class="text-center"> Vocabulary </th>
                            <th class="text-center"> Grammar </th>
                            <th class="text-center min-w-150px"> Note </th>
                            <th class="text-center"> Action </th>
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
        var tableId = 'evaluations_table';
        var customAjaxUrl = "{{ route('groups.student.listevaluation', ['id' => Crypt::encrypt($id)]) }}";
        var columns = [{
                data: "id",
                name: "id",
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: "name",
                name: "name"
            },
            {
                data: "writing",
                name: "writing"
            },
            {
                data: "reading",
                name: "reading"
            },
            {
                data: "speaking",
                name: "speaking"
            },
            {
                data: "listening",
                name: "listening"
            },
            {
                data: "sh_vocab",
                name: "sh_vocab"
            },
            {
                data: "sh_grammar",
                name: "sh_grammar"
            },
            {
                data: "sh_note",
                name: "sh_note"
            },
            {
                data: "actions",
                name: "actions",
                orderable: false,
                searchable: false
            }
        ];

        var filterFields = ['#name'];
    </script>
    @include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'groups'])
@stop
