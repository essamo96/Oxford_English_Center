@extends('admin.layout.master')

@section('title', 'إدارة الطلاب')

@section('css')
@stop


@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-dark">إدارة الطلاب</li>
@stop

@section('page-content')
@php $active_menu = 'students'; @endphp

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
                <div class="col-lg-2 col-md-4 mb-4">
                    <label class="form-label fw-semibold text-gray-700">الاسم/رقم الجوال</label>
                    <input type="text" name="title" id="title" class="form-control form-control-solid searchable" placeholder="بحث...">
                </div>
                <div class="col-lg-2 col-md-4 mb-4">
                    <label class="form-label fw-semibold text-gray-700">المعلم</label>
                    <select class="form-select form-select-solid searchable" name="teacher_id" id="teacher_id" data-control="select2" data-placeholder="اختر المعلم">
                        <option value="">جميع المعلمين</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 mb-4">
                    <label class="form-label fw-semibold text-gray-700">المجموعة</label>
                    <select class="form-select form-select-solid searchable" name="group_id" id="group_id" data-control="select2" data-placeholder="اختر المجموعة">
                        <option value="">جميع المجموعات</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 mb-4">
                    <label class="form-label fw-semibold text-gray-700">من تاريخ</label>
                    <input type="date" name="date_from" id="date_from" class="form-control form-control-solid searchable">
                </div>
                <div class="col-lg-2 col-md-4 mb-4">
                    <label class="form-label fw-semibold text-gray-700">إلى تاريخ</label>
                    <input type="date" name="date_to" id="date_to" class="form-control form-control-solid searchable">
                </div>
                <div class="col-lg-2 col-md-4 mb-4">
                    <label class="form-label fw-semibold text-gray-700">الحالة</label>
                    <select class="form-select form-select-solid searchable" name="activeS" id="activeS">
                        <option value="">كل الحالات</option>
                        <option value="1">فعال</option>
                        <option value="0">غير فعال</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-4 mb-4">
                    <label class="form-label fw-semibold text-gray-700">الجنس</label>
                    <select class="form-select form-select-solid searchable" name="gender" id="gender">
                        <option value="">الكل</option>
                        <option value="1">ذكر</option>
                        <option value="0">أنثى</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 mb-4">
                    <label class="form-label fw-semibold text-gray-700">التأجيل</label>
                    <select class="form-select form-select-solid searchable" name="delaying" id="delaying">
                        <option value="">الكل</option>
                        <option value="1">مؤجل</option>
                        <option value="0">غير مؤجل</option>
                    </select>
                </div>
                
                <input type="hidden" name="is_today" id="is_today" class="searchable" value="">
                <input type="hidden" name="is_activated_today" id="is_activated_today" class="searchable" value="">

                <div class="col-12 mt-2 d-flex flex-wrap gap-2">
                    <button type="button" id="btn_today" class="btn btn-sm btn-light-primary fw-bold quick-filter">
                        <i class="ki-duotone ki-plus fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>طلاب اليوم
                    </button>
                    <button type="button" id="btn_activated" class="btn btn-sm btn-light-success fw-bold quick-filter">
                        <i class="ki-duotone ki-check-circle fs-3"><span class="path1"></span><span class="path2"></span></i>تم تفعيلهم اليوم
                    </button>
                    <button type="button" id="btn_all" class="btn btn-sm btn-light-info fw-bold">
                        <i class="ki-duotone ki-category fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>جميع الطلاب
                    </button>
                    <button type="reset" id="reset_button" class="btn btn-sm btn-light-danger fw-bold ms-auto">
                        <i class="ki-duotone ki-arrows-loop fs-3"><span class="path1"></span><span class="path2"></span></i>إسناد افتراضي
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
                <i class="ki-duotone ki-people fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i> إدارة سجلات الطلاب
            </span>
        </div>
        <div class="card-toolbar gap-2">
            @can('admin.students.add')
                <a href="{{ route('students.add') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> إضافة طالب
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
            <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center" id="categories_table">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th class="w-30px text-start"> # </th>
                        <th class="w-30px text-center px-0">
                            <div class="form-check form-check-custom form-check-solid justify-content-center">
                                <input class="form-check-input h-20px w-20px" type="checkbox" id="select-all" />
                            </div>
                        </th>
                        <th class="min-w-100px"> الطالب </th>
                        <th class="min-w-80px text-center">الجوال</th>
                        <th class="min-w-120px text-center">التخصص</th>
                        <th class="min-w-80px text-center"> الحالة </th>
                        <th class="min-w-80px text-center"> مؤجل </th>
                        <th class="min-w-100px text-center"> ملاحظة </th>
                        <th class="text-center min-w-150px pe-4"> العمليات </th>
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
    var tableId = 'categories_table';
    var customPageLength = 25;
    var columns = [
        { data: "id", name: "id", orderable: false, searchable: false, render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
        { data: "checkbox", name: "checkbox", orderable: false, searchable: false },
        { data: "name", name: "name", orderable: true },
        { data: "mobile", name: "mobile" },
        // { data: "email", name: "email" }, // Consolidated into Name column
        { data: "job", name: "job" },
        { data: "status", name: "status", orderable: true, searchable: false },
        { data: "delayed", name: "delayed", orderable: true, searchable: false },
        { data: "note", name: "note", className: "text-center" },
        { data: "actions", name: "actions", orderable: false, searchable: false, className: "text-center" }
    ];

    var filterFields = ['#title', '#activeS', '#gender', '#delaying', '#teacher_id', '#group_id', '#date_from', '#date_to', '#is_today', '#is_activated_today'];

    $(document).ready(function() {
        // Quick Filters logic
        $('#btn_today').on('click', function() {
            $('.quick-filter').removeClass('btn-primary btn-success btn-info').addClass('btn-light-primary btn-light-success btn-light-info');
            $(this).removeClass('btn-light-primary').addClass('btn-primary text-white');
            $('#is_today').val(1);
            $('#is_activated_today').val('');
            table.ajax.reload();
        });

        $('#btn_activated').on('click', function() {
            $('.quick-filter').removeClass('btn-primary btn-success btn-info').addClass('btn-light-primary btn-light-success btn-light-info');
            $(this).removeClass('btn-light-success').addClass('btn-success text-white');
            $('#is_today').val('');
            $('#is_activated_today').val(1);
            table.ajax.reload();
        });

        $('#btn_all').on('click', function() {
            $('.quick-filter').removeClass('btn-primary btn-success btn-info').addClass('btn-light-primary btn-light-success btn-light-info');
            $('#is_today').val('');
            $('#is_activated_today').val('');
            $('#reset_button').click();
        });
        $('#select-all').on('click', function() {
            $('.checkboxes').prop('checked', this.checked);
        });

        $(document).on('click', '#reset_button', function(e) {
            e.preventDefault();
            $(this).closest('form')[0].reset();
            table.ajax.reload();
        });

        $(document).on('click', ".delaying", function() {
            var id = $(this).data('href');
            var item = $(this);
            $.ajax({
                type: "POST",
                url: "{{ route('students.delaying') }}",
                data: { 'id': id, '_token': '{{ csrf_token() }}' }
            }).done(function(data) {
                table.ajax.reload(null, false);
                if (typeof toastr !== 'undefined') toastr[data.status](data.message);
            });
        });

        // SMS logic
        $('#sms').on('click', function() {
            var selectedMobiles = [];
            $(".checkboxes:checked").each(function() {
                selectedMobiles.push($(this).data("mob"));
            });

            if (selectedMobiles.length > 0) {
                Swal.fire({
                    title: 'ادخل نص الرسالة',
                    input: 'textarea',
                    inputAttributes: { rows: 5, maxlength: 2100 },
                    inputPlaceholder: 'ادخل محتوى الرسالة هنا',
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: 'إرسال',
                    cancelButtonText: 'إلغاء',
                    customClass: { confirmButton: 'btn btn-primary', cancelButton: 'btn btn-light' },
                    preConfirm: (note) => {
                        if (!note) { Swal.showValidationMessage('يجب إدخال نص الرسالة'); }
                        return $.ajax({
                            type: 'POST',
                            url: '{{ route("send.admin.sms") }}',
                            data: { note: note, selectedMobiles: selectedMobiles, _token: '{{ csrf_token() }}' }
                        });
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire('تم الارسال بنجاح!', '', 'success');
                    }
                });
            } else {
                Swal.fire({
                    text: 'يجب اختيار طالب واحد على الأقل!',
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'حسناً',
                    customClass: {
                        confirmButton: 'btn btn-danger'
                    }
                });
            }
        });

        // Bulk Email logic
        $('#CEmail').on('click', function() {
            var emails = window.EmailComposer.collectEmails();
            if (emails.length === 0) {
                Swal.fire({ text: 'اختر طالباً واحداً على الأقل!', icon: 'warning', buttonsStyling: false, confirmButtonText: 'حسناً', customClass: { confirmButton: 'btn btn-primary' } });
                return;
            }
            window.EmailComposer.send({
                type: 'bulk',
                recipients: emails,
                url: '{{ route("send.CEmail") }}',
                title: 'رسالة بريد إلكتروني للطلاب المحددين'
            });
        });

        // Single student Reply (email notification)
        $(document).on('click', '.Reply', function() {
            var id = $(this).data('id');
            window.EmailComposer.send({
                type: 'single',
                id: id,
                url: '{{ route("send.message") }}',
                title: 'إرسال إشعار بريدي للطالب'
            });
        });

    });

    function addnote(id) {
        Swal.fire({
            title: 'إضافة ملاحظة للطالب',
            input: 'textarea',
            inputAttributes: { rows: 5, maxlength: 2100 },
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: 'أضف الملاحظة',
            cancelButtonText: 'إلغاء',
            customClass: { confirmButton: 'btn btn-primary', cancelButton: 'btn btn-light' },
            preConfirm: (note) => {
                return $.ajax({
                    type: 'POST',
                    url: '{{ route("notes.store") }}',
                    data: { note: note, id: id, _token: '{{ csrf_token() }}' }
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    text: 'تم إضافة الملاحظة بنجاح!',
                    icon: 'success',
                    buttonsStyling: false,
                    confirmButtonText: 'حسناً',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                });
                table.ajax.reload(null, false);
            }
        });
    }
</script>
@include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'students'])
@stop
