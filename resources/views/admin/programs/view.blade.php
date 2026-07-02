@extends('admin.layout.master')

@section('title', 'إدارة البرامج')

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-dark">إدارة البرامج</li>
@stop

@section('page-content')
@php $active_menu = 'programs'; @endphp

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
                <div class="col-lg-3 col-md-4 mb-4">
                    <label class="form-label fw-semibold">اسم البرنامج</label>
                    <input type="text" name="title" id="title" class="form-control form-control-solid searchable" placeholder="ابحث باسم البرنامج...">
                </div>
                <div class="col-lg-3 col-md-4 mb-4">
                    <label class="form-label fw-semibold">اسم المجموعة</label>
                    <input type="text" name="group_name" id="group_name" class="form-control form-control-solid searchable" placeholder="ابحث باسم المجموعة...">
                </div>
                <div class="col-lg-3 col-md-4 mb-4">
                    <label class="form-label fw-semibold">الحالة</label>
                    <select name="status" id="status" class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="اختر الحالة">
                        <option value="all">الكل</option>
                        <option value="1">مفعل</option>
                        <option value="0">غير مفعل</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-4 mb-4 d-flex align-items-end gap-2">
                    <button type="button" onclick="table.ajax.reload();" class="btn btn-primary btn-icon w-40px h-40px shadow-sm" title="بحث">
                        <i class="bi bi-search fs-3"></i>
                    </button>
                    <button type="reset" id="reset_button" class="btn btn-light-danger btn-icon w-40px h-40px shadow-sm" title="إعادة تعيين البحث">
                        <i class="bi bi-arrow-clockwise fs-3"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1 text-info">
                <i class="ki-duotone ki-book-open fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> إدارة البرامج الدراسية
            </span>
        </div>
        <div class="card-toolbar gap-2">
            @can('admin.programs.add')
                <a href="{{ route('programs.add') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> إضافة برنامج 
                </a>
            @endcan
            <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-right me-1"></i> رجوع
            </a>
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.error')
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center" id="programs_table">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-6 text-uppercase gs-0">
                        <th class="w-50px text-center"> # </th>
                        <th class="min-w-200px text-center"> اسم البرنامج </th>
                        <th class="min-w-150px text-center"> المجموعات </th>
                        <th class="min-w-100px text-center"> إجمالي الطلاب </th>
                        <th class="min-w-160px text-center"> الحد الأدنى للدفع </th>
                        <th class="min-w-100px text-center"> الحالة </th>
                        <th class="min-w-100px text-center"> إخفاء (تحديد مستوى) </th>
                        <th class="text-center min-w-150px"> العمليات </th>
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
    
    <div class="modal bg-body fade" tabindex="-1" id="program_details_modal">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content shadow-none">
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fs-4 fw-bold text-primary" id="modal_program_title">تفاصيل البرنامج</h5>

                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                    <!--end::Close-->
                </div>

                <div class="modal-body bg-light">
                    <div id="program_details_content">
                        <!-- Content will be loaded here -->
                    </div>
                </div>

                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-light-primary fw-bold" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    var table;
    var tableId = 'programs_table';
    var columns = [
        { data: "id", name: "id", orderable: false, searchable: false, render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
        { data: "title", name: "title", orderable: true, className: "text-start" },
        { data: "grope_no", name: "grope_no", className: "text-center" },
        { data: "students_count", name: "students_count", className: "text-center" },
        { data: "min_payment", name: "min_payment", orderable: false, searchable: false, className: "text-center" },
        { data: "status", name: "status", orderable: true, searchable: false },
        { data: "is_placement_test", name: "is_placement_test", orderable: true, searchable: false, className: "text-center" },
        { data: "actions", name: "actions", orderable: false, searchable: false, className: "text-center" }
    ];

    var filterFields = ['#title', '#status', '#group_name'];

    $(document).ready(function() {
        $(document).on('click', '#reset_button', function(e) {
            e.preventDefault();
            $(this).closest('form')[0].reset();
            table.ajax.reload();
        });

        $(document).on('click', '.view-program-details', function(e) {
            var id = $(this).data('id');
            var url = "{{ route('programs.details', ':id') }}".replace(':id', id);
            
            // Show SweetAlert Loading 
            Swal.fire({
                title: 'جاري التحميل...',
                text: 'يرجى الانتظار أثناء جلب بيانات المجموعات',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    if (response.status == 'success') {
                        // Close loading alert
                        Swal.close();
                        
                        // Populate and open modal
                        $('#modal_program_title').text('مجموعات برنامج: ' + response.title);
                        $('#program_details_content').html(response.html);
                        $('#program_details_modal').modal('show');
                    } else if (response.status == 'empty') {
                        // Show info alert directly
                        Swal.fire({
                            text: response.message,
                            icon: "info",
                            buttonsStyling: false,
                            confirmButtonText: "موافق",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    } else {
                        // Show error alert
                        Swal.fire({
                            title: 'خطأ',
                            text: response.message || 'حدث خطأ أثناء تحميل البيانات',
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "موافق",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    }
                },
                error: function() {
                    $('#program_details_modal').modal('hide');
                    Swal.fire({
                        text: "حدث خطأ أثناء تحميل البيانات",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "موافق",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }
            });
        });

        // Add handler for placement-status toggle
        $(document).on('change', '.placement-status', function(e) {
            e.preventDefault();
            var id = $(this).data('href');
            var isChecked = $(this).is(':checked');
            var url = "{{ route('programs.placement_status') }}";

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    is_placement_test: isChecked ? 1 : 0
                },
                success: function(response) {
                    if (response.status == 'success') {
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                        table.ajax.reload(null, false);
                    }
                },
                error: function() {
                    toastr.error('حدث خطأ أثناء تغيير الحالة');
                    table.ajax.reload(null, false);
                }
            });
        });
    });
</script>
@include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'programs'])
@stop
