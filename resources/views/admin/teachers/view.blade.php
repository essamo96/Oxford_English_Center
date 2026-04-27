@extends('admin.layout.master')

@section('title', 'إدارة المدرسين')

@section('page-title')
    إدارة المدرسين
@stop

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">إدارة المدرسين</li>
@stop

@section('page-content')
    <div class="card mb-7 shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <span class="card-label fw-bold fs-3 mb-1">
                    <i class="ki-duotone ki-magnifier fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span></i> فلاتر البحث
                </span>
            </div>
            <div class="card-toolbar">
                <button type="button" id="reset_button" class="btn btn-sm btn-light-primary">
                    تصفية الفلاتر
                </button>
            </div>
        </div>
        <div class="card-body py-4">
            <form id="filter_form" class="row g-5">
                <div class="col-md-6 mb-4">
                    <label class="form-label fw-semibold">الاسم</label>
                    <input type="text" name="title" id="title" class="form-control form-control-solid"
                        placeholder="ابحث عن اسم المدرس...">
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label fw-semibold">الحالة</label>
                    <select name="activeT" id="activeT" class="form-select form-select-solid" data-control="select2"
                        data-placeholder="اختر الحالة">
                        <option value=""></option>
                        <option value="1">مفعل</option>
                        <option value="2">غير مفعل</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <span class="card-label fw-bold fs-3 mb-1 text-info">
                    <i class="ki-duotone ki-book-open fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> قائمة المدرسين
                </span>
            </div>
            <div class="card-toolbar gap-3">
                <button type="button" class="btn btn-light-warning btn-sm" id="sms">
                    <i class="ki-duotone ki-sms fs-4 me-1"><span class="path1"></span><span class="path2"></span></i> إرسال
                    SMS
                </button>
                @can('admin.teachers.add')
                    <a href="{{ route('teachers.add') }}" class="btn btn-primary btn-sm">
                        <i class="ki-duotone ki-plus fs-3"></i> إضافة مدرس
                    </a>
                @endcan
                <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">
                    <i class="ki-duotone ki-arrow-left fs-2"><span class="path1"></span><span class="path2"></span></i> رجوع
                </a>
            </div>
        </div>
        <div class="card-body py-4">
            @include('admin.layout.error')
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center"
                    id="teachers_table">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-6 text-uppercase gs-0">
                            <th class="w-50px text-center"> # </th>
                            <th class="w-50px text-center">
                                <div class="d-flex align-items-center justify-content-center" style="height: 48px;">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                        <input class="form-check-input h-20px w-20px" type="checkbox" id="select-all" />
                                    </div>
                                </div>
                            </th>
                            <th class="min-w-150px text-center"> الاسم </th>
                            <th class="min-w-125px text-center"> رقم الجوال </th>
                            <th class="min-w-150px text-center"> البريد الإلكتروني </th>
                            <th class="min-w-100px text-center"> الحالة </th>
                            <th class="min-w-100px text-center"> التقييمات </th>
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
@stop

@section('js')
    <script>
        var table;
        var tableId = 'teachers_table';
        var customAjaxUrl = "{{ route('teachers.list') }}";
        var columns = [{
                data: "id",
                name: "id",
                orderable: false,
                searchable: false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: "checkbox",
                name: "checkbox",
                orderable: false,
                searchable: false
            },
            {
                data: "name",
                name: "name",
                className: "fw-bold"
            },
            {
                data: "mobile",
                name: "mobile"
            },
            {
                data: "email",
                name: "email"
            },
            {
                data: "status",
                name: "status",
                className: "text-center"
            },
            {
                data: "evaluations",
                name: "evaluations",
                className: "text-center"
            },
            {
                data: "actions",
                name: "actions",
                orderable: false,
                searchable: false,
                className: "text-center"
            }
        ];

        var filterFields = ['#title', '#activeT'];

        $(document).ready(function() {
            $('#select-all').on('click', function() {
                $('.checkboxes').prop('checked', this.checked);
            });

            $(document).on('click', '#reset_button', function(e) {
                e.preventDefault();
                $('#filter_form')[0].reset();
                $('#filter_form select').val('').trigger('change');
                table.ajax.reload();
            });

            // Status AJAX handler is already handled by parts/status.blade.php usually,
            // but for Teachers it seems custom here. I'll use datatableMaster or keep it.
        });

        // Toggle Evaluations Logic
        $(document).on('click', ".evaluations", function() {
            var id = $(this).data('href');
            var item = $(this);
            $.ajax({
                type: "POST",
                url: "{{ route('teachers.evaluations') }}",
                data: {
                    'id': id,
                    '_token': '{{ csrf_token() }}'
                },
                success: function(data) {
                    table.draw();
                    toastr[data.status](data.message);
                }
            });
        });

        // SMS Logic
        $('#sms').on('click', function() {
            var selectedMobiles = [];
            $(".checkboxes:checked").each(function() {
                selectedMobiles.push($(this).data("mob"));
            });

            if (selectedMobiles.length === 0) {
                Swal.fire({
                    text: 'يجب اختيار مدرس واحد على الأقل!',
                    icon: 'warning',
                    buttonsStyling: false,
                    confirmButtonText: 'حسناً',
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });
                return;
            }

            Swal.fire({
                title: 'إرسال رسالة نصية (SMS)',
                input: 'textarea',
                inputPlaceholder: 'اكتب نص الرسالة هنا...',
                showCancelButton: true,
                confirmButtonText: 'إرسال',
                cancelButtonText: 'إلغاء',
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-light"
                },
                buttonsStyling: false,
                showLoaderOnConfirm: true,
                preConfirm: (note) => {
                    if (!note) {
                        Swal.showValidationMessage('يجب إدخال نص الرسالة');
                        return false;
                    }
                    return $.ajax({
                        type: 'POST',
                        url: '{{ route('send.admin.sms') }}',
                        data: {
                            note: note,
                            selectedMobiles: selectedMobiles,
                            _token: '{{ csrf_token() }}'
                        }
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        text: 'تم إرسال الرسائل بنجاح.',
                        icon: 'success',
                        buttonsStyling: false,
                        confirmButtonText: 'حسناً',
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }
            });
        });
    </script>
    @include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'teachers'])
@stop
