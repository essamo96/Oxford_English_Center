
    <div class="portlet box {{ $form_class }}">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-magnifier"></i>البحث
            </div>
        </div>

        <div class="portlet-body">
            <form role="form" class="form-horizontal">
                <div class="form-body">
                    <div class="form-group">
                        <div class="row-6"></div>
                        <label class="col-md-3 control-label">الإسم</label>
                        <div class="col-md-6">
                            <input type="text" name="name" id="name" class="form-control searchable"
                                placeholder="الإسم">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet box {{ $form_class }}">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-grid"></i>إدارة المجموعات
                    </div>
                    @can('admin.groups.add')
                        <div class="actions">
                            <a href="{{ route('groups.add') }}" class="btn btn-default btn-sm">
                                <i class="fa fa-plus" style="color: #ffc038"></i> <strong style="color: #ffffff"> اضافة
                                </strong> </a>
                        </div>
                    @endcan
                    <div class="actions ">
                        <a href="{{ URL::previous() }}" class="btn btn-default btn-sm" style="color: #ffffff">
                            <i class="fa fa-backward" style="color: #ffc038"></i> <strong style="color: #ffffff"> رجوع
                            </strong> </a>
                    </div>
                    <div class="actions" style="width: 160px; max-width: 100%;">
                        <select class="form-select form-select-lg" aria-label=".form-select-sm example"
                            aria-placeholder=">اختر مجموعة..."
                            style=" color: #030303;
                            width: 150px;
                            max-width: 100%;
                            border-radius: 8px;
                            border: 2px solid #ffc038;">
                            <option selected>اختر مجموعة...</option>
                            @foreach ($programs as $item)
                                        <option value="{{ $item->id }}">{{ $item->title }}
                                        </option>
                                    @endforeach
                        </select>
                    </div>
                </div>

                <div class="portlet-body">
                    @include('admin.layout.error')
                    <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover table-checkable order-column"
                        id="categories_table">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th> الإسم </th>
                                <th> المعلم </th>
                                <th> البرنامج </th>
                                <th> عدد الطلاب </th>
                                <th> الموعد </th>
                                <th> الحالة </th>
                                <th> تعديل </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    </div>
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>
@stop
@section('modal')
    @include('admin.layout.ajax')
@stop
@section('css')

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
                "sDom": '<"row view-filter"<"col-sm-12"<"pull-left"l><"clearfix">>><"table-scrollable"t><"row"<"col-md-5 col-sm-12"i><"col-md-7 col-sm-12"p>>r',
                "ajax": {
                    url: "{{ route('groups.list') }}",
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
                        "data": "teacher_name",
                        "title": "المعلم",
                        "orderable": true,
                        "searchable": false
                    },
                    {
                        "data": "program_name",
                        "title": "البرنامج",
                        "orderable": true,
                        "searchable": false
                    },
                    {
                        "data": "studens_no",
                        "title": "عدد الطلاب",
                        "orderable": true,
                        "searchable": false
                    },
                    {
                        "data": "time_day",
                        "title": "الموعد",
                        "orderable": true,
                        "searchable": false
                    },
                    {
                        "data": "status",
                        "title": "الحالة",
                        "orderable": true,
                        "searchable": false
                    },
                    {
                        "data": "actions",
                        "title": "تعديل",
                        "orderable": false,
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

            $(document).on('click', ".status", function() {
                var id = $(this).data('href');
                var item = $(this);
                $.ajax({
                    type: "POST",
                    url: "{{ route('groups.status') }}",
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
            $(document).on('click', ".delete", function() {
                var id = $("#delete_id").val();
                $.ajax({
                    type: "POST",
                    url: "{{ route('groups.delete') }}",
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
        <script>
            $(document).on('change', ".form-select-lg", function() {
                var program_id = $(this).val()
                $.ajax({
                    type: "GET",
                    url: "admin/groups",
                    data: {
                        'program_id': program_id,
                        "_token": "{{ csrf_token() }}"
                    }
    
                }).success(function(data) {
                    console.log(data);
                    $('#categories_table').html(data);
                });
            });
        </script>
@stop
