@extends('admin.layout.master')

@section('title')
    إدارة استعلامات الطلاب
@stop

@section('css')
    <style>
        .grid-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .grid-item {
            display: flex;
            flex-direction: column;
        }

        .control-label {
            margin-bottom: 5px;
        }

        .profile-info {
            display: flex;
            align-items: center;
        }

        .profile-info-grid {
            display: grid;
            grid-template-columns: 100px 1fr;
            gap: 10px;
            /* Add spacing between the image and the name */
        }

        .profile-userpic {
            width: 100px;
        }

        .form-horizontal .control-label {
            text-align: right;
            margin-bottom: 0;
            padding-top: 7px;
            
        }

        /* Add your custom styles for profile picture and user info here */
    </style>
@stop

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">
            <i class="ki-duotone ki-home fs-4 me-1"></i> الرئيسية
        </a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">التقارير</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark">استعلامات الطلاب</li>
@stop

@section('page-content')
    <!--begin::Filters Card-->
    <div class="card card-flush shadow-sm mb-5 border-0">
        <div class="card-header pt-7">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold text-gray-800">فلاتر البحث</span>
                <span class="text-gray-400 mt-1 fw-semibold fs-6">استخدم خيارات البحث أدناه للعثور على النتائج المطلوبة</span>
            </h3>
        </div>
        <div class="card-body">
            <div class="row g-5">
                <div class="col-md-6 col-lg-4">
                    <label class="form-label text-gray-700 fw-bold fs-6">اسم الطالب / رقم الجوال</label>
                    <div class="position-relative">
                        <i class="ki-duotone ki-magnifier fs-2 position-absolute top-50 translate-middle-y ms-4"><span class="path1"></span><span class="path2"></span></i>
                        <input type="text" name="title" id="title" class="form-control form-control-solid ps-12 searchable" placeholder="مثال: محمد علي، أو 059..." />
                    </div>
                </div>
                <!-- You can add more filters here later -->
            </div>
        </div>
    </div>
    <!--end::Filters Card-->

    <!--begin::Student Info Display-->
    <div id="target" class="card card-flush mb-5 shadow-sm border-0" style="display: none;">
        <div class="card-body pt-9 pb-0" id="topinfos">
            <!-- Content loaded via AJAX from info.blade.php -->
        </div>
    </div>
    <!--end::Student Info Display-->

    <div class="card card-flush shadow-sm border-0">
        <div class="card-header align-items-center py-5 gap-2 gap-md-5">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <input type="text" data-kt-table-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="بحث في الجدول..." />
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                    <!-- Export buttons can go here -->
                </div>
            </div>
        </div>
        <div class="card-body pt-0">
            @include('admin.layout.error')
            <div class="table-responsive">
                <table class="table table-row-bordered table-row-gray-300 align-middle gs-0 gy-4" id="pages_table">
                    <thead>
                        <tr class="fw-bold fs-7 text-gray-400 border-bottom-2 border-gray-200 text-uppercase gs-0">
                            <th class="w-25px text-start">#</th>
                            <th class="min-w-200px">Student Info</th>
                            <th class="min-w-125px">Group</th>
                            <th class="min-w-70px text-center">Test 1</th>
                            <th class="min-w-70px text-center">Test 2</th>
                            <th class="min-w-70px text-center">Test 3</th>
                            <th class="min-w-85px text-center">Final</th>
                            <th class="min-w-70px text-center">Act.</th>
                            <th class="min-w-70px text-center">WB</th>
                            <th class="min-w-100px text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-600"></tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            ////////////////////////////////////////////////////
            $('#confirm').on('show.bs.modal', function(e) {
                $("#delete_id").val($(e.relatedTarget).data('href'));
            });
            var oTable = $('#pages_table').DataTable({
                "processing": true,
                "serverSide": true,
                "language": {
                    "sProcessing": "Processing...",
                    "sLengthMenu": "Show _MENU_ entries",
                    "sZeroRecords": "No matching records found",
                    "sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "sInfoEmpty": "Showing 0 to 0 of 0 entries",
                    "sInfoFiltered": "(filtered from _MAX_ total entries)",
                    "sInfoPostFix": "",
                    "sSearch": "Search=>:",
                    "sUrl": "",
                    "oPaginate": {
                        "sFirst": "First",
                        "sPrevious": "Previous",
                        "sNext": "Next",
                        "sLast": "Last"
                    }
                },
                "pageLength": 25,
                "bJQueryUI": false,
                "sDom": '<"row view-filter"<"col-sm-12"<"pull-left"l><"clearfix">>><"table-scrollable"t><"row"<"col-md-5 col-sm-12"i><"col-md-7 col-sm-12"p>>r',
                "ajax": {
                    url: "{{ route('students_report.list') }}",
                    data: function(d) {
                        d.info = $('input[name="title"]').val() ;
                    }
                },
                "order": [
                    [1, 'asc']
                ],
                "columnDefs": [{
                    "targets": "_all",
                    "defaultContent": ""
                }],
                "columns": [{
                        "data": "",
                        "title": "#",
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "data": "student_id",
                        "orderable": true,
                        "searchable": false
                    },
                    {
                        "data": "group_id",
                        "orderable": true,
                        "searchable": false
                    },
                    {
                        "data": "exam1_degree",

                        "orderable": true,
                        "searchable": false
                    },
                    {
                        "data": "exam2_degree",

                        "orderable": true,
                        "searchable": false
                    },
                    {
                        "data": "exam3_degree",

                        "orderable": true,
                        "searchable": false
                    },
                    {
                        "data": "exam4_degree",

                        "orderable": true,
                        "searchable": false
                    },
                    {
                        "data": "activity_degree",

                        "orderable": true,
                        "searchable": false
                    },
                    {
                        "data": "workbook_degree",

                        "orderable": true,
                        "searchable": false
                    },
                    {
                        "data": "total_degree",

                        "orderable": true,
                        "searchable": false
                    },
                ],
                "fnDrawCallback": function(oSettings) {
                    $('.tooltips').tooltip();

                    oTable.column(0).nodes().each(function(cell, i) {
                        cell.innerHTML = (parseInt(oTable.page.info().start)) + i + 1;
                    });
                }
            });

            $('.searchable').on('input', function(e) {
                var searchVal = $(this).val();
                oTable.draw();

                if (searchVal.length > 2) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('students_report.showtopinfos') }}",
                        data: {
                            'info': searchVal
                        },
                        success: function(data) {
                            if (data.trim() !== '') {
                                $('#target').fadeIn();
                                $('#topinfos').html(data);
                            } else {
                                $('#target').fadeOut();
                            }
                        },
                        error: function() {
                            $('#target').fadeOut();
                        }
                    });
                } else {
                    $('#target').fadeOut();
                }
            });

            $('button[type="reset"]').on('click', function(e) {
                e.preventDefault();
                $(this).closest('form').get(0).reset();
                oTable.draw();
            });

            $(document).on('click', ".status", function() {
                var id = $(this).data('href');
                var item = $(this);
                $.ajax({
                    type: "POST",
                    url: "{{ route('pages.status') }}",
                    data: {
                        'id': id
                    }
                }).success(function(data) {
                    if (data.type == 'yes') {
                        item.removeClass("red");
                        item.addClass("green-dark");
                        item.html('<i class="fa fa-check"></i> تفعيل');
                    } else if (data.type == 'no') {
                        item.removeClass("green-dark");
                        item.addClass("red");
                        item.html('<i class="fa fa-times"></i> تعطيل ');
                    }
                    toastr[data.status](data.message);
                });
            });
            ///////////////////////////////////////////////////
        });
    </script>
@stop

@section('modals')
    @include('admin.layout.ajax')
@stop
