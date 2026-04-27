@extends('admin.layout.master')

@section('title')
    طلاب المجموعة - {{ $info->name }}
@stop

@section('page-title')
    طلاب المجموعة: {{ $info->name }}
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
    <li class="breadcrumb-item text-muted">طلاب المجموعة</li>
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
                    @can('admin.groups.add')
                        <a href="{{ route('groups.student.add', ['id' => Crypt::encrypt($id)]) }}" class="btn btn-primary">
                            <i class="ki-duotone ki-plus fs-2"></i> إضافة طالب للمجموعة
                        </a>
                    @endcan
                    <a href="{{ route('groups.view') }}" class="btn btn-light btn-active-light-primary">
                        <i class="ki-duotone ki-arrow-left fs-2"><span class="path1"></span><span class="path2"></span></i>
                        رجوع
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body py-4">
            @include('admin.layout.masterLayouts.error')
            <div class="table-responsive">
                <table id="group_students_table"
                    class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center">
                    <thead>
                        <tr class="text-center text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="text-center w-50px"> # </th>
                            <th class="text-center min-w-150px"> اسم الطالب </th>
                            <th class="text-center min-w-100px"> رقم الجوال </th>
                            <th class="text-center"> المدفوع </th>
                            <th class="text-center"> المتبقي </th>
                            <th class="text-center"> الكتب </th>
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
        var tableId = 'group_students_table';
        var customAjaxUrl = "{{ route('groups.student.list', ['id' => Crypt::encrypt($id)]) }}";
        var columns = [{
                data: 'id',
                name: 'id',
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'mobile',
                name: 'mobile'
            },
            {
                data: 'paid',
                name: 'paid'
            },
            {
                data: 'student_fee_total',
                name: 'student_fee_total'
            },
            {
                data: 'books',
                name: 'books'
            },
            {
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false
            }
        ];

        var filterFields = ['#name'];

        $(document).ready(function() {
            $(document).on('click', ".delete", function() {
                var delete_id = $("#delete_id").val();
                $.ajax({
                    type: "POST",
                    url: "{{ route('groups.student.delete', ['id' => Crypt::encrypt($id)]) }}",
                    data: {
                        'id': delete_id,
                        '_token': '{{ csrf_token() }}'
                    }
                }).done(function(data) {
                    Swal.fire({
                        text: data.message,
                        icon: data.status == 'success' ? 'success' : 'error',
                        buttonsStyling: false,
                        confirmButtonText: "موافق",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                    table.draw();
                });
            });
        });
    </script>
    @include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'groups'])
@stop
