@extends('admin.layout.master')

@section('title', 'إدارة الصلاحيات')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark">إدارة الصلاحيات</li>
@stop

@section('page-content')
    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <input type="text" id="generalSearch" value="{{ old('name') }}"
                        class="form-control form-control-solid w-250px ps-13 generalSearch"
                        placeholder="@lang('app.search')" />
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                    <a href="{{ route($active_menu . '.add') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg"></i> @lang('app.add')
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body py-4">
            @include('admin.layout.error')
            <div class="table-responsive">
                <table id="kt_table" class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="w-50px"> # </th>
                            <th class="min-w-150px">@lang('app.name') </th>
                            <th class="min-w-150px">@lang('app.group')</th>
                            <th class="min-w-100px">@lang('app.guard_name')</th>
                            <th class="text-center min-w-100px">@lang('app.actions')</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold"></tbody>
                </table>
            </div>
        </div>
    </div>
    @include('admin.' . $active_menu . '.parts.modal')
@stop

@section('js')
    <script>
        $(document).ready(function() {
            var table = $('#kt_table').DataTable({
                responsive: true,
                processing: true,
                "bLengthChange": false,
                "bFilter": false,
                serverSide: true,
                ajax: {
                    url: "<?= route($active_menu . '.list') ?>",
                    data: function(d) {
                        d.name = $('#generalSearch').val();
                    }
                },
                columns: [
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    { data: 'name' },
                    { data: 'group_id' },
                    { data: 'guard_name' },
                    { data: 'actions', responsivePriority: -1, className: "text-center" },
                ]
            });

            $('.generalSearch').on('input', function() {
                table.ajax.reload();
            });

            $('#confirm').on('show.bs.modal', function(e) {
                var link = $(e.relatedTarget);
                var href = link.data('href');
                $('.delete').on('click', function() {
                    $.ajax({
                        url: '<?= route($active_menu . '.delete') ?>',
                        type: 'POST',
                        data: {
                            id: href,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(data) {
                            $('#confirm').modal('hide');
                            Swal.fire({
                                text: "تم الحذف بنجاح",
                                title: "نجاح",
                                icon: "success",
                                buttonsStyling: false,
                                showConfirmButton: false,
                                timer: 2000
                            });
                            table.ajax.reload();
                        }
                    });
                });
                $('#delete_id').val(href);
            });
        });
    </script>
@stop

