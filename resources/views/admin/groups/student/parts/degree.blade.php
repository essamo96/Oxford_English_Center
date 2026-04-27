@extends('admin.layout.master')
@section('title')
    عرض علامات طلاب المجموعة
@stop

@section('page-breadcrumb')
    <ul class="page-breadcrumb">
        <li>
            <i class="icon-home"></i>
            <a href="{{ route('dashboard.view') }}">الرئيسية</a>
            <i class="fa fa-angle-right"></i>
        </li>
        <li>
            <a href="{{ route('groups.view') }}">المجموعات</a>
            <i class="fa fa-angle-right"></i>
            </i>
        </li>
        <li>
            <a href="{{ route('groups.view') }}">{{ $info->name }}</a>
            <i class="fa fa-angle-right"></i>
            </i>
        </li>
        <li>
            <span>عرض علامات طلاب المجموعة</span>
        </li>
    </ul>
@stop

@section('page-title')
    <h1 class="page-title">المجموعات
        <small>عرض علامات طلاب المجموعة</small>
    </h1>
@stop

@section('page-content')
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet box {{ $form_class }}">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-grid"></i>عرض علامات طلاب المجموعة
                    </div>
                    <div class="actions">
                        <a href="{{ URL::previous() }}" class="btn btn-default btn-sm" style="color: #fffff">
                            <i class="fa fa-backward" style="color: #ffc038"></i> <strong style="color: #ffffff"> رجوع
                            </strong> </a>
                    </div>
                </div>
                <div class="portlet-body">
                    @include('admin.layout.error')
                    <table class="table table-striped table-bordered table-hover table-checkable order-column"
                        id="categories_table">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th> الإسم </th>
                                <th>
                                    Progress Test 1 <br>(Units 1-3)
                                </th>
                                <th>
                                     Progress Test 2 <br>(Units 4-6)
                                </th>
                                <th>
                                   Progress Test 3 <br>(Units 7-9)
                                </th>
                                <th>
                                    End of Course Test <br>(Units 1-12)
                                </th>
                                <th>
                                    Coursework<br>out of 5 Marks
                                </th>
                                <th>
                                    Workbook<br>out of 5 Marks
                                </th>
                                <th>
                                    Overall
                                </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>
@stop
@section('modal')
    @include('admin.layout.ajax')
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
            var oTable = $('#categories_table').DataTable({
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
                dom: 'Blfrtip',
                buttons: [{
                    extend: 'excel',
                    text: 'إكسل',
                    exportOptions: {
                        modifier: {
                            // DataTables core
                            order: 'index', // 'current', 'applied', 'index',  'original'
                            page: 'all', // 'all',     'current'
                            search: 'none' // 'none',    'applied', 'removed'
                        },
                        columns: [1, 2, 3, 4, 5, 6, 7]
                    }
                }, ],

                "ajax": {
                    url: "{{ route('groups.student.listdegree', ['id' => Crypt::encrypt($id) ] )}}",
                    data: function(d) {
                        d.title = $('input[name="name"]').val();
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
                        "data": "name",
                        "title": "الإسم",
                        "orderable": true,
                        "searchable": false
                    },
                    {
                        "data": "exam1_degree",
                        "title": "Progress Test1<br>out of 15 Marks",
                        "orderable": true,
                        "searchable": false
                    },
                    {
                        "data": "exam2_degree",
                        "title": "Progress Test2<br>out of 15 Marks",
                        "orderable": true,
                        "searchable": false
                    },
                    {
                        "data": "exam3_degree",
                        "title": "Progress Test3<br>out of 15 Marks",
                        "orderable": true,
                        "searchable": false
                    },
                    {
                        "data": "exam4_degree",
                        "title": "Final Exam<br>out of 60 Marks",
                        "orderable": true,
                        "searchable": false
                    },
                    {
                        "data": "activity_degree",
                        "title": "Coursework<br>out of 5 Marks",
                        "orderable": true,
                        "searchable": false
                    },
                    {
                        "data": "workbook_degree",
                        "title": "Workbook<br>out of 5 Marks",
                        "orderable": true,
                        "searchable": false
                    },
                    {
                        "data": "total_degree",
                        "title": "Overall",
                        "orderable": true,
                        "searchable": false
                    }
                ],
                "fnDrawCallback": function(oSettings) {
                    $('.tooltips').tooltip();

                    oTable.column(0).nodes().each(function(cell, i) {
                        cell.innerHTML = (parseInt(oTable.page.info().start)) + i + 1;
                    });
                }
            });

            $('.searchable').on('input', function(e) {
                e.preventDefault();
                oTable.draw();
            });

            $('button[type="reset"]').on('click', function(e) {
                e.preventDefault();
                $(this).closest('form').get(0).reset();
                oTable.draw();
            });

            ///////////////////////////////////////////////////
            $(document).on('click', ".delete", function() {
                var id = $("#delete_id").val();
                $.ajax({
                    type: "POST",
                    url: "{{ route('groups.student.delete', ['id' => Crypt::encrypt($id)]) }}",
                    data: {
                        'id': id
                    }
                }).success(function(data) {
                    toastr[data.status](data.message);
                    oTable.draw();
                });
            });
        });
    </script>
   
@stop
