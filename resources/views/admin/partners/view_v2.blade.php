@extends('admin.layout.master')

@section('title', 'إدارة الشركاء')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark">إدارة الشركاء</li>
@stop

@section('page-content')
    <div class="card mb-7 shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <span class="card-label fw-bold fs-3 mb-1">
                    <i class="ki-duotone ki-magnifier fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span></i> البحث والفلاتر
                </span>
            </div>
        </div>
        <div class="card-body py-4">
            <form role="form" class="form">
                <div class="row gx-5">
                    <div class="col-lg-6 col-md-8 mb-4">
                        <label class="form-label fw-semibold">الإسم</label>
                        <input type="text" name="name" id="name" class="form-control form-control-solid searchable" placeholder="ابحث بالإسم...">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <span class="card-label fw-bold fs-3 mb-1 text-info">
                    <i class="ki-duotone ki-grid fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span></i> عرض الشركاء
                </span>
            </div>
            <div class="card-toolbar">
                @can('admin.partners.add')
                    <a href="{{ route('partners.add') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg"></i> إضافة شريك جديد
                    </a>
                @endcan
            </div>
        </div>
        <div class="card-body py-4">
            @include('admin.layout.error')
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center" id="partners_table">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="w-50px"> # </th>
                            <th class="min-w-150px"> الإسم </th>
                            <th class="min-w-100px"> الحالة </th>
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
    @include('admin.layout.ajax')
@stop

@section('js')
<script type="text/javascript">
    $(document).ready(function () {
        var table = $('#partners_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('partners.list') }}",
                data: function (d) {
                    d.title = $('input[name="name"]').val();
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
                { data: "title" },
                { data: "status" },
                { data: "actions", orderable: false, searchable: false, className: "text-center" }
            ]
        });

        $('.searchable').on('input', function () {
            table.draw();
        });

        $(document).on('click', ".status", function () {
            var id = $(this).data('href');
            var item = $(this);
            $.ajax({
                type: "POST",
                url: "{{ route('partners.status') }}",
                data: {'id': id, '_token': '{{ csrf_token() }}'},
                success: function (data) {
                    toastr[data.status](data.message);
                    table.draw(false);
                }
            });
        });

        $(document).on('click', ".delete", function () {
            var id = $(this).data('href');
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "لن تتمكن من التراجع عن هذا!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم، احذف!',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('partners.delete') }}",
                        data: {'id': id, '_token': '{{ csrf_token() }}'},
                        success: function (data) {
                            toastr[data.status](data.message);
                            table.draw();
                        }
                    });
                }
            });
        });
    });
</script>
@stop

