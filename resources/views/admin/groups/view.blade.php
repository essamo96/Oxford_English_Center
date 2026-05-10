@extends('admin.layout.master')

@section('title')
    إدارة المجموعات
@stop

@section('page-title')
    إدارة المجموعات
@stop

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">إدارة المجموعات</li>
@stop

@section('page-content')
    <div class="card shadow-sm mb-9">
        <div class="card-header">
            <h3 class="card-title">فلاتر البحث والمراجعة المتقدمة</h3>
            <div class="card-toolbar">
                <button type="button" class="btn btn-sm btn-light-danger" id="reset_button">
                    <i class="bi bi-arrow-clockwise fs-4 me-1"></i> تصفية الفلاتر
                </button>
            </div>
        </div>
        <div class="card-body">
            <form id="filter_form" class="row g-7">
                <!-- Group Name -->
                <div class="col-lg-3 col-md-6 fv-row">
                    <label class="form-label fw-bold">اسم المجموعة</label>
                    <input type="text" id="title" name="title" class="form-control form-control-solid" placeholder="بحث باسم المجموعة...">
                </div>

                <!-- Student Search -->
                <div class="col-lg-3 col-md-6 fv-row">
                    <label class="form-label fw-bold">بحث باسم الطالب أو الجوال</label>
                    <input type="text" id="student_name" name="student_name" class="form-control form-control-solid" placeholder="الاسم أو رقم الجوال...">
                </div>

                <!-- Program -->
                <div class="col-lg-3 col-md-6 fv-row">
                    <label class="form-label fw-bold">البرنامج التعليمي</label>
                    <select id="program_id" name="program_id" class="form-select form-select-solid" data-control="select2" data-placeholder="اختر البرنامج">
                        <option value=""></option>
                        @foreach ($programs as $item)
                            <option value="{{ $item->id }}">{{ $item->title }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Teacher Filter with Avatars -->
                <div class="col-lg-3 col-md-6 fv-row">
                    <label class="form-label fw-bold">المدرس</label>
                    <select id="teacher_id" name="teacher_id" class="form-select form-select-solid" data-control="select2" data-placeholder="اختر المدرس">
                        <option value=""></option>
                        @foreach ($teachers as $item)
                            <option value="{{ $item->id }}" data-kt-select2-image="{{ $item->image ? url($item->image) : asset('assets/media/avatars/blank.png') }}">
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Range & Time -->
                <div class="col-lg-6 col-md-12 fv-row">
                    <label class="form-label fw-bold">تاريخ البدء & الوقت</label>
                    <div class="d-flex align-items-center gap-3">
                        <input type="date" name="date_from" id="date_from" class="form-control form-control-solid" placeholder="من" />
                        <span class="text-gray-500">إلى</span>
                        <input type="date" name="date_to" id="date_to" class="form-control form-control-solid" placeholder="إلى" />
                        
                        <div class="flex-grow-1 ms-3">
                            <select id="date_id" name="date_id" class="form-select form-select-solid" data-control="select2" data-placeholder="اختر الوقت">
                                <option value=""></option>
                                @foreach ($times as $item)
                                    <option value="{{ $item->id }}">{{ $item->days }} | {{ $item->times }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Status and Quick Filters -->
                <div class="col-lg-6 col-md-12 d-flex align-items-end gap-3 flex-wrap">
                    <div class="w-150px">
                        <label class="form-label fw-bold">الحالة</label>
                        <select id="activeG" name="activeG" class="form-select form-select-solid" data-control="select2" data-placeholder="الحالة">
                            <option value=""></option>
                            <option value="1">نشط</option>
                            <option value="0">منتهي</option>
                        </select>
                    </div>

                    <label class="form-check form-check-custom form-check-solid bg-light-success rounded p-3 h-45px border border-success border-dashed shadow-sm cursor-pointer hover-elevate-up transition-3ms">
                        <input class="form-check-input h-20px w-20px me-3" type="checkbox" name="is_today" id="is_today" value="1" />
                        <div class="d-flex flex-column">
                            <span class="form-check-label fw-bold text-success fs-6 lh-1 mb-1">مجموعات اليوم</span>
                            <span class="text-dark fs-10">تصفية حسب جدول اليوم فقط</span>
                        </div>
                    </label>
                </div>

            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                        <span class="path1"></span><span class="path2"></span>
                    </i>
                    <input type="text" data-kt-user-table-filter="search"
                        class="form-control form-control-solid w-250px ps-13" placeholder="بحث سريع..." />
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end gap-3">
                    <button type="button" class="btn btn-light-success" id="CEmail">
                        <i class="ki-duotone ki-sms fs-2"><span class="path1"></span><span class="path2"></span></i>
                        إرسال إيميل للمحددين
                    </button>

                    <button type="button" class="btn btn-light-primary" id="sms">
                        <i class="ki-duotone ki-sms fs-2"><span class="path1"></span><span class="path2"></span></i>
                        إرسال SMS للمحددين
                    </button>

                    @can('admin.groups.add')
                        <a href="{{ route('groups.add') }}" class="btn btn-primary">
                            <i class="ki-duotone ki-plus fs-2"></i> إضافة مجموعة
                        </a>
                    @endcan
                    <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">
                        <i class="ki-duotone ki-arrow-left fs-2"><span class="path1"></span><span
                                class="path2"></span></i>
                        رجوع
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body py-4">
            @include('admin.layout.masterLayouts.error')
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center"
                    id="groups_table">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="w-25px text-center"> # </th>
                            <th class="w-25px text-center">
                                <div class="d-flex align-items-center justify-content-center" style="height: 48px;">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input h-20px w-20px" type="checkbox" id="select-all" />
                                    </div>
                                </div>
                            </th>
                            <th class="min-w-120px"> اسم المجموعة </th>
                            <th class="min-w-120px"> المدرس </th>
                            <th class="min-w-120px"> البرنامج </th>
                            <th class="min-w-120px"> الطلاب </th>
                            <th class="min-w-120px"> كود الانضمام </th>
                            <th class="min-w-120px"> الشهادات </th>
                            <th class="min-w-80px"> الحالة </th>
                            <th class="text-center min-w-200px"> العمليات </th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold"></tbody>
                </table>
            </div>
        </div>
    </div>

@stop

@section('modal')
    @include('admin.layout.masterLayouts.modal')

    <!-- Modern Details Modal -->
    <div class="modal fade" id="details_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-800px">
            <div class="modal-content">
                <div class="modal-header pb-0 border-0 justify-content-end">
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body scroll-y pt-0 pb-15" id="modal_content">
                    <div class="text-center py-10">
                        <span class="spinner-border w-50px h-50px" role="status"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <style>
        #groups_table .dropdown-menu { z-index: 1051 !important; position: fixed !important; }
        .table-responsive { overflow: visible !important; }
    </style>
    <script>
        var table;
        var tableId = 'groups_table';
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
                orderable: true
            },
            {
                data: "teacher_name",
                name: "teacher_name"
            },
            {
                data: "program_name",
                name: "program_name"
            },
            {
                data: "studens_no",
                name: "studens_no",
                className: "text-center"
            },
            {
                data: "code",
                name: "code",
                className: "text-center"
            },
            {
                data: "certifcate",
                name: "certifcate",
                className: "text-center"
            },
            {
                data: "status",
                name: "status",
                orderable: true,
                searchable: false
            },
            {
                data: "actions",
                name: "actions",
                orderable: false,
                searchable: false,
                className: "text-center"
            }
        ];

        var filterFields = ['#title', '#activeG', '#program_id', '#teacher_id', '#student_name', '#is_today', '#date_from', '#date_to', '#date_id'];

        $(document).ready(function() {
            // Select2 with Images Utility
            const formatSelect2Image = (item) => {
                if (!item.id || !$(item.element).data('kt-select2-image')) return item.text;
                var imgUrl = $(item.element).data('kt-select2-image');
                var span = $("<span><img src='" + imgUrl + "' class='rounded-circle me-2' style='height:20px;width:20px;object-fit:cover;'/>" + item.text + "</span>");
                return span;
            };

            $('#teacher_id, #date_id').select2({
                templateResult: formatSelect2Image,
                templateSelection: formatSelect2Image
            });

            $('#select-all').on('click', function() {
                $('.checkboxes').prop('checked', this.checked);
            });

            $('#filter_form').on('submit', function(e) {
                e.preventDefault();
                table.draw();
            });

            $(document).on('click', '#reset_button', function(e) {
                e.preventDefault();
                $('#filter_form')[0].reset();
                $('#filter_form select').val('').trigger('change');
                table.draw();
            });

            // Live filters
            $('#filter_form input').on('keyup change', function() {
                table.draw();
            });

            $('#filter_form select').on('change', function() {
                table.draw();
            });


            $('#sms').on('click', function() {
                var selectedGroups = [];
                $(".checkboxes:checked").each(function() {
                    selectedGroups.push($(this).data("id"));
                });
                if (selectedGroups.length > 0) {
                    Swal.fire({
                        title: 'إرسال رسالة SMS للمجموعات المحددة',
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
                        preConfirm: (message) => {
                            if (!message) {
                                Swal.showValidationMessage('يجب إدخال نص الرسالة');
                            }
                            return $.ajax({
                                type: 'POST',
                                url: '{{ route('send.groups.sms') }}',
                                data: {
                                    message: message,
                                    id: selectedGroups,
                                    _token: '{{ csrf_token() }}'
                                }
                            });
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                text: 'تم الإرسال بنجاح!',
                                icon: 'success',
                                buttonsStyling: false,
                                confirmButtonText: 'حسناً',
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'تنبيه!',
                        text: 'يجب اختيار مجموعة واحدة على الأقل!',
                        icon: 'warning',
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }
            });

            // Bulk Email logic using EmailCampaign Component
            $('#CEmail').on('click', function() {
                var selectedGroups = [];
                $(".checkboxes:checked").each(function() {
                    selectedGroups.push($(this).data("id"));
                });
                if (selectedGroups.length === 0) {
                    Swal.fire({ 
                        text: 'يجب اختيار مجموعة واحدة على الأقل!', 
                        icon: 'warning', 
                        buttonsStyling: false, 
                        confirmButtonText: 'حسناً', 
                        customClass: { confirmButton: 'btn btn-primary' } 
                    });
                    return;
                }
                
                if (window.EmailComposer) {
                    window.EmailComposer.send({
                        type: 'bulk',
                        recipients: selectedGroups,
                        url: '{{ route("groups.send.CEmail") }}',
                        title: 'رسالة بريد إلكتروني لطلاب المجموعات المحددة'
                    });
                } else {
                    Swal.fire('خطأ', 'مكون إرسال الإيميلات غير متوفر.', 'error');
                }
            });
        });

        function showGroupModal(id) {
            fetchModalContent('{{ route('groups.details') }}', { id: id });
        }

        function showTeacherModal(id) {
            fetchModalContent('{{ route('teachers.details') }}', { id: id });
        }

        function showProgramModal(id) {
            fetchModalContent('{{ route('programs.details.post') }}', { id: id });
        }

        function fetchModalContent(url, data) {
            $('#modal_content').html('<div class="text-center py-10"><span class="spinner-border w-50px h-50px" role="status"></span></div>');
            $('#details_modal').modal('show');
            $.ajax({
                url: url,
                type: 'POST',
                data: { ...data, _token: '{{ csrf_token() }}' },
                success: function(response) {
                    $('#modal_content').html(response);
                },
                error: function() {
                    $('#modal_content').html('<div class="alert alert-danger">حدث خطأ أثناء تحميل البيانات</div>');
                }
            });
        }

        function generatekey(id) {
            let randomNumber = Math.random().toString(36).substring(2, 11);
            Swal.fire({
                title: 'توليد كود الانضمام للمجموعة',
                html: '<div class="fv-row mb-7 text-start">' +
                    '<label class="fs-6 fw-semibold mb-2">كود العشوائي</label>' +
                    '<input id="number2" class="form-control form-control-solid mb-3" value ="' + randomNumber +
                    '" readonly>' +
                    '</div>' +
                    '<div class="fv-row text-start">' +
                    '<label class="fs-6 fw-semibold mb-2">تاريخ انتهاء الكود</label>' +
                    '<input id="datetime2" type="datetime-local" class="form-control form-control-solid" placeholder="تاريخ انتهاء الكود">' +
                    '</div>',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: 'توليد وإرسال',
                denyButtonText: 'توليد وحفظ',
                cancelButtonText: 'إلغاء',
                customClass: {
                    confirmButton: "btn btn-success",
                    denyButton: "btn btn-primary",
                    cancelButton: "btn btn-light"
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed || result.isDenied) {
                    let randomKey = $("#number2").val();
                    let dateTime = $("#datetime2").val();
                    let sendEmail = result.isConfirmed ? 1 : 0;
                    
                    $.ajax({
                        url: '{{ route('groups.add.code') }}',
                        method: 'post',
                        data: {
                            'id': id,
                            'code': randomKey,
                            'code_scope': dateTime,
                            'send_email': sendEmail,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function(response) {
                        Swal.fire({
                            text: response.message,
                            icon: 'success',
                            buttonsStyling: false,
                            confirmButtonText: 'حسناً',
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        }).then(() => {
                            if (response.campaign_id) {
                                if (window.EmailCampaignMonitor) {
                                    window.EmailCampaignMonitor.start(response.campaign_id, response.total_recipients, response.redirect_url);
                                } else {
                                    window.location.href = response.redirect_url;
                                }
                            }
                            table.ajax.reload(null, false);
                        });
                    });
                }
            });
        }

        function generateCertificateCode(rowId) {
            $.ajax({
                type: "POST",
                url: "{{ url('admin/groups/generate-certificate-code') }}/" + rowId,
                data: {
                    row_id: rowId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        text: response.message,
                        icon: 'success',
                        buttonsStyling: false,
                        confirmButtonText: 'حسناً',
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }
            });
        }
    </script>
    @include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'groups'])
@stop

