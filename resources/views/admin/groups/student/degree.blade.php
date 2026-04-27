@extends('admin.layout.master')

@section('title')
    درجات الطلاب - {{ $info->name }}
@stop

@section('page-title')
    درجات الطلاب: {{ $info->name }}
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
    <li class="breadcrumb-item text-muted">درجات الطلاب</li>
@stop

@section('page-content')
    <div class="card shadow-sm mb-7">
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
                    <button type="button" class="btn btn-light-success me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        <i class="ki-duotone ki-exit-up fs-2"><span class="path1"></span><span class="path2"></span></i>
                        تصدير
                    </button>
                    <div id="kt_datatable_example_export_menu" class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-200px py-4" data-kt-menu="true">
                        <div class="menu-item px-3">
                            <a href="#" class="menu-link px-3" data-kt-export="excel">Export as Excel</a>
                        </div>
                    </div>
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
                <table id="degrees_table"
                    class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center">
                    <thead>
                        <tr class="text-center text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="text-center w-50px"> # </th>
                            <th class="text-center min-w-150px"> اسم الطالب </th>
                            <th class="text-center"> Progress Test1<br>out of 15 Marks </th>
                            <th class="text-center"> Progress Test2<br>out of 15 Marks </th>
                            <th class="text-center"> Progress Test3<br>out of 15 Marks </th>
                            <th class="text-center"> Final Exam<br>out of 60 Marks </th>
                            <th class="text-center"> Coursework<br>out of 5 Marks </th>
                            <th class="text-center"> Workbook<br>out of 5 Marks </th>
                            <th class="text-center fw-bold text-primary"> Overall </th>
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
        var tableId = 'degrees_table';
        var customAjaxUrl = "{{ route('groups.student.listdegree', ['id' => Crypt::encrypt($id)]) }}";
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
                data: "exam1_degree",
                name: "exam1_degree"
            },
            {
                data: "exam2_degree",
                name: "exam2_degree"
            },
            {
                data: "exam3_degree",
                name: "exam3_degree"
            },
            {
                data: "exam4_degree",
                name: "exam4_degree"
            },
            {
                data: "activity_degree",
                name: "activity_degree"
            },
            {
                data: "workbook_degree",
                name: "workbook_degree"
            },
            {
                data: "total_degree",
                name: "total_degree",
                className: "fw-bold text-primary"
            }
        ];

        var filterFields = ['#name'];

        $(document).ready(function() {
            // Function to update total on the fly
            $(document).on('keyup', '.deg-input', function() {
                var id = $(this).attr("data_id");
                var e1 = parseFloat($('#exam1_degree_' + id).val()) || 0;
                var e2 = parseFloat($('#exam2_degree_' + id).val()) || 0;
                var e3 = parseFloat($('#exam3_degree_' + id).val()) || 0;
                var e4 = parseFloat($('#exam4_degree_' + id).val()) || 0;
                var act = parseFloat($('#activity_degree_' + id).val()) || 0;
                var wb = parseFloat($('#workbook_degree_' + id).val()) || 0;
                
                var total = e1 + e2 + e3 + e4 + act + wb;
                $('#total_lbl_' + id).text(total > 0 ? total : '');
            });
        });
    </script>
    @include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'groups'])
    
    <script>
        // Export functionality
        $(document).ready(function() {
            const documentTitle = '{{ $info->name }} - Student Degrees';
            var buttons = new $.fn.dataTable.Buttons(table, {
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: documentTitle,
                        exportOptions: {
                            columns: ':visible:not(.no-export)'
                        }
                    }
                ]
            }).container().appendTo($('#kt_datatable_example_export_menu'));

            $(document).on('click', '[data-kt-export]', function(e) {
                e.preventDefault();
                const exportValue = $(this).attr('data-kt-export');
                const target = $('.dt-buttons .buttons-' + exportValue);
                target.click();
            });
        });
    </script>
@stop
