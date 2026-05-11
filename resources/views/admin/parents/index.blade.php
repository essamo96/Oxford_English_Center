@extends('admin.layout.master')

@section('title', 'إدارة أولياء الأمور')

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-dark">إدارة أولياء الأمور</li>
@stop

@section('page-content')
@php $active_menu = 'parents'; @endphp

<div class="card shadow-sm mb-8">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1 text-info">
                <i class="ki-duotone ki-filter fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span></i> فلاتر البحث
            </span>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-5">
            <div class="col-md-5">
                <label class="form-label fw-bold text-gray-700">البحث (الاسم، الجوال، الإيميل)</label>
                <input type="text" id="search_text" class="form-control" placeholder="ادخل اسم ولي الأمر أو الجوال...">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold text-gray-700">صلة القرابة</label>
                <select id="filter_relationship" class="form-select">
                    <option value="">الكل</option>
                    <option value="father">أب (Father)</option>
                    <option value="mother">أم (Mother)</option>
                    <option value="brother">أخ (Brother)</option>
                    <option value="sister">أخت (Sister)</option>
                    <option value="other">أخرى (Other)</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="button" class="btn btn-info w-100" onclick="table.ajax.reload()">
                    <i class="ki-duotone ki-magnifier fs-4 me-1"></i> بحث
                </button>
                <button type="button" class="btn btn-light w-100" onclick="resetFilters()">
                    إعادة ضبط
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1 text-info">
                <i class="ki-duotone ki-people fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span></i> سجل أولياء الأمور
            </span>
        </div>
        <div class="card-toolbar gap-2">
            <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-right me-1"></i> رجوع
            </a>
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.error')
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center" id="parents_table">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th class="w-30px text-start"> # </th>
                        <th class="min-w-150px"> ولي الأمر </th>
                        <th class="min-w-80px text-center">الأبناء</th>
                        <th class="min-w-100px text-center">الجوال</th>
                        <th class="min-w-150px text-center">البريد الإلكتروني</th>
                        <th class="min-w-100px text-center">صلة القرابة</th>
                        <th class="min-w-100px text-center">تاريخ التسجيل</th>
                        <th class="text-center min-w-80px pe-4"> العمليات </th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold text-center"></tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('modal')
    <div class="modal fade" id="children_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">الأبناء المسجلين</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <div id="children_content">
                        <div class="text-center">
                            <span class="spinner-border w-40px h-40px" role="status"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    var table;
    var tableId = 'parents_table';
    var columns = [
        { data: "id", name: "id", orderable: false, searchable: false, render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
        { data: "name", name: "name", orderable: true },
        { data: "students_count", name: "students_count", className: "text-center" },
        { data: "phone", name: "phone" },
        { data: "email", name: "email" },
        { data: "relationship", name: "relationship" },
        { data: "created_at", name: "created_at" },
        { data: "action", name: "action", orderable: false, searchable: false }
    ];

    function resetFilters() {
        $('#search_text, #filter_relationship').val('');
        table.ajax.reload();
    }

    $(document).ready(function() {
        table = $('#' + tableId).DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("parents.list") }}',
                data: function(d) {
                    d.search_text = $('#search_text').val();
                    d.relationship = $('#filter_relationship').val();
                }
            },
            columns: columns,
            language: { url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Arabic.json" }
        });

        $(document).on('click', '.view-children', function() {
            var id = $(this).data('id');
            $('#children_content').html('<div class="text-center"><span class="spinner-border w-40px h-40px" role="status"></span></div>');
            $('#children_modal').modal('show');
            
            $.ajax({
                url: '{{ route("parents.children") }}',
                type: 'GET',
                data: { id: id },
                success: function(response) {
                    $('#children_content').html(response);
                },
                error: function() {
                    $('#children_content').html('<div class="alert alert-danger">حدث خطأ أثناء تحميل البيانات</div>');
                }
            });
        });
        $(document).on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "سيتم حذف بيانات ولي الأمر، لن يؤثر هذا على سجلات الطلاب الحالية.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم، احذف!',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("parents.delete") }}',
                        type: 'POST',
                        data: { id: id, _token: '{{ csrf_token() }}' },
                        success: function(response) {
                            if (response.success) {
                                table.ajax.reload();
                                Swal.fire('تم الحذف!', 'تم حذف السجل بنجاح.', 'success');
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'parents'])
@stop
