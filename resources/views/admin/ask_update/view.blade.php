@extends('admin.layout.master')

@section('title', 'إدارة طلبات تحديث البيانات')

@section('css')
<style>
    .swal2-show { border-radius: 20px; }
    .swal2-title { color: #f5a700; }
</style>
@endsection

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-dark">إدارة طلبات تحديث البيانات</li>
@stop

@section('page-content')
@php $active_menu = 'ask_update'; @endphp

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
                    <label class="form-label fw-semibold">الاسم أو الإيميل أو الجوال</label>
                    <input type="text" name="title" id="title" class="form-control form-control-solid searchable" placeholder="البحث بالاسم، الايميل، أو رقم الجوال...">
                </div>
                <div class="col-lg-3 col-md-4 mb-4 d-flex align-items-end gap-2">
                    <button type="reset" id="reset_button" class="btn btn-light-danger btn-icon w-40px h-40px shadow-sm" title="إعادة تعيين البحث">
                        <i class="bi bi-arrow-clockwise fs-3"></i>
                    </button>
                    <button type="button" class="btn btn-info btn-sm" id="CEmail">
                        <i class="bi bi-envelope me-1"></i> إرسال بريد
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
                <i class="ki-duotone ki-time fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> طلبات التحديث الحالية
            </span>
        </div>
        <div class="card-toolbar gap-2">
            @can('admin.students.add')
                <a href="{{ route('students.add') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> إضافة طالب
                </a>
            @endcan
            <a href="{{ URL::previous() }}" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-right me-1"></i> رجوع
            </a>
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.error')
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center" id="ask_update_table">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-6 text-uppercase gs-0">
                        <th class="w-50px text-center"> # </th>
                        <th class="w-50px text-center">
                            <div class="d-flex align-items-center justify-content-center" style="height: 48px;">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input h-20px w-20px" type="checkbox" id="select-all" />
                                </div>
                            </div>
                        </th>
                        <th class="min-w-150px text-center"> الإسم </th>
                        <th class="min-w-125px text-center"> الجوال </th>
                        <th class="min-w-125px text-center"> تاريخ الميلاد </th>
                        <th class="min-w-125px text-center"> الوظيفة </th>
                        <th class="min-w-150px text-center"> الايميل </th>
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
    @include('admin.layout.ajax')
@stop

@section('js')
<script>
    var table;
    var tableId = 'ask_update_table';
    var customAjaxUrl = '{{ route("ask_update.list") }}';
    var columns = [
        { data: "id", name: "id", orderable: false, searchable: false, render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
        { data: "checkbox", orderable: false, searchable: false },
        { data: "name", name: "name" },
        { data: "mobile", name: "mobile" },
        { data: "dob", name: "dob" },
        { data: "job", name: "job" },
        { data: "email", name: "email" },
        { data: "actions", orderable: false, searchable: false, className: "text-center" }
    ];

    var filterFields = ['#title'];

    $(document).ready(function() {
        $('#select-all').on('click', function() {
            $('.checkboxes').prop('checked', this.checked);
        });

        $(document).on('click', '#reset_button', function(e) {
            e.preventDefault();
            $(this).closest('form')[0].reset();
            table.ajax.reload();
        });

        $(document).on('click', '.accept', function () {
            var id = $(this).data('id');
            Swal.fire({
                title: 'تأكيد القبول',
                icon: 'question',
                html: 'هل توافق على السماح للطالب بتحديث بياناته؟',
                showCancelButton: true,
                confirmButtonText: 'سماح',
                cancelButtonText: 'إلغاء',
                customClass: { confirmButton: 'btn btn-primary', cancelButton: 'btn btn-light' },
                buttonsStyling: false
            }).then(function (result) {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '{{ route("accept.ask_update") }}',
                        data: { id: id, _token: '{{ csrf_token() }}' },
                        success: function (response) {
                            Swal.fire({ title: 'تم القبول!', text: response.message || 'تم قبول الطلب بنجاح', icon: 'success', timer: 2000, showConfirmButton: false });
                            table.ajax.reload(null, false);
                        },
                        error: function () {
                            Swal.fire({ title: 'خطأ', text: 'حدث خطأ غير متوقع', icon: 'error', timer: 2000, showConfirmButton: false });
                        }
                    });
                }
            });
        });

        $(document).on('click', '.refuse', function () {
            var id = $(this).data('id');
            Swal.fire({
                title: 'تأكيد الرفض',
                icon: 'warning',
                html: 'هل تريد رفض طلب التحديث الخاص بالطالب؟',
                showCancelButton: true,
                confirmButtonText: 'رفض',
                cancelButtonText: 'إلغاء',
                customClass: { confirmButton: 'btn btn-danger', cancelButton: 'btn btn-light' },
                buttonsStyling: false
            }).then(function (result) {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '{{ route("refuse.ask_update") }}',
                        data: { id: id, _token: '{{ csrf_token() }}' },
                        success: function (response) {
                            Swal.fire({ title: 'تم الرفض!', text: response.message || 'تم رفض الطلب بنجاح', icon: 'success', timer: 2000, showConfirmButton: false });
                            table.ajax.reload(null, false);
                        },
                        error: function () {
                            Swal.fire({ title: 'خطأ', text: 'حدث خطأ غير متوقع', icon: 'error', timer: 2000, showConfirmButton: false });
                        }
                    });
                }
            });
        });

        $(document).on('click', '.CEmail', function() {
            var emails = [];
            $(".checkboxes:checked").each(function() {
                var email = $(this).data("email");
                if (email) { emails.push(email); }
            });
            
            if (emails.length > 0) {
                Swal.fire({
                    title: 'إرسال بريد إلكتروني مخصص',
                    html: `
                        <div class="form-group mb-3 text-start">
                            <label class="form-label fw-bold mb-2">عنوان الرسالة:</label>
                            <input type="text" class="form-control form-control-solid" id="message-title" placeholder="أدخل عنوان الرسالة">
                        </div>
                        <div class="form-group mb-3 text-start">
                            <label class="form-label fw-bold mb-2">نص الرسالة:</label>
                            <textarea class="form-control form-control-solid" id="message-body" rows="5" placeholder="أدخل نص الرسالة"></textarea>
                        </div>
                        <div class="form-group mb-3 text-start">
                            <input type="file" class="form-control form-control-solid" id="message-file">
                        </div>`,
                    showCancelButton: true,
                    confirmButtonText: 'إرسال الآن',
                    cancelButtonText: 'إلغاء',
                    customClass: { confirmButton: 'btn btn-primary', cancelButton: 'btn btn-light' },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.isConfirmed) {
                        let title = $('#message-title').val();
                        let body = $('#message-body').val();
                        let file_data = $('#message-file').prop('files')[0];
                        let form_data = new FormData();
                        if(file_data) form_data.append('file', file_data);
                        $.each(emails, function(i, email) { form_data.append('emails[]', email); });
                        form_data.append('title', title);
                        form_data.append('message', body);
                        form_data.append('_token', '{{ csrf_token() }}');
                        
                        $.ajax({
                            type: 'POST',
                            url: '{{ route("send.CEmail") }}',
                            data: form_data,
                            contentType: false,
                            processData: false,
                            beforeSend: function() {
                                Swal.showLoading();
                            },
                            success: function(response) {
                                Swal.fire({ title: 'تم الإرسال!', text: response.message || 'تم إرسال الرسالة بنجاح', icon: 'success', customClass: { confirmButton: 'btn btn-primary' }});
                            },
                            error: function() {
                                Swal.fire({ title: 'خطأ!', text: 'حدث خطأ أثناء الإرسال', icon: 'error', customClass: { confirmButton: 'btn btn-danger' }});
                            }
                        });
                    }
                });
            } else {
                Swal.fire({ title: 'تنبيه!', text: 'يجب اختيار طالب واحد على الأقل!', icon: 'warning', customClass: { confirmButton: 'btn btn-primary' } });
            }
        });
    });
</script>
@include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'ask_update'])
@stop
