@extends('admin.layout.master')

@section('title', 'إدارة طلبات العضوية')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark">إدارة طلبات العضوية</li>
@stop

@section('css')
<style>
    /* Force vertical centering for all table cells */
    #kt_table td, #kt_table th {
        vertical-align: middle !important;
    }
    /* Ensure form checks are also centered */
    .form-check.form-check-custom {
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
@stop

@section('page-content')
    <div class="card mb-7 shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <span class="card-label fw-bold fs-3 mb-1 text-info">
                    <i class="ki-duotone ki-magnifier fs-4 text-info me-2">
                    <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                    </i> البحث والفلاتر
                </span>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end gap-2 text-nowrap">
                    <button type="button" id="todayBtn" class="btn btn-sm btn-light-success">
                        <i class="ki-duotone ki-calendar fs-4 me-1"><span class="path1"></span><span class="path2"></span></i> طلاب اليوم
                    </button>
                    <button type="button" id="showAllBtn" class="btn btn-sm btn-light-primary">
                        <i class="ki-duotone ki-eye fs-4 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i> عرض الكل
                    </button>
                    <button type="reset" id="resetFilter" class="btn btn-sm btn-light-danger">
                        <i class="ki-duotone ki-arrows-loop fs-4 me-1"><span class="path1"></span><span class="path2"></span></i> تصفية
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body py-4">
            <form id="filter_form" class="row g-5">
                <input type="hidden" id="is_today" name="is_today" value="">
                <div class="col-lg-4 col-md-6">
                    <label class="form-label fw-semibold">الاسم / رقم الجوال / الإيميل</label>
                    <input type="text" id="search" name="search" class="form-control form-control-solid" placeholder="بحث باسم أو رقم جوال أو إيميل">
                </div>
                <div class="col-lg-2 col-md-3">
                    <label class="form-label fw-semibold">الجنس</label>
                    <select id="gender" name="gender" class="form-select form-select-solid" data-control="select2" data-placeholder="-- الكل --">
                        <option value=""></option>
                        <option value="male">ذكر</option>
                        <option value="female">أنثى</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold">من تاريخ</label>
                    <input type="text" id="date_from" name="date_from" class="form-control form-control-solid date-picker" placeholder="اختر ...">
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold">إلى تاريخ</label>
                    <input type="text" id="date_to" name="date_to" class="form-control form-control-solid date-picker" placeholder="اختر ...">
                </div>
                <div class="col-12 d-flex justify-content-end mt-2 gap-2">
                    <button type="button" class="btn btn-success BulkActivate">
                        <i class="ki-duotone ki-user-tick fs-4 me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i> تفعيل وإرسال البيانات للمحددين
                    </button>
                    <button type="button" class="btn btn-warning CEmail">
                        <i class="ki-duotone ki-sms fs-4 me-2"><span class="path1"></span><span class="path2"></span></i> إرسال بريد مخصص
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <span class="card-label fw-bold fs-3 mb-1 text-info">
                    <i class="ki-duotone ki-address-book fs-2 text-info me-2">
                        <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                    </i>
                    إدارة طلبات العضوية
                </span>
            </div>
            <div class="card-toolbar gap-3">
                <a href="{{ route('students.add') }}" class="btn btn-primary btn-sm">
                    <i class="ki-duotone ki-plus fs-3"></i> إضافة طالب
                </a>
                <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">
                    <i class="ki-duotone ki-arrow-left fs-2"><span class="path1"></span><span class="path2"></span></i> رجوع
                </a>
            </div>
        </div>
        <div class="card-body py-4">
            @include('admin.layout.masterLayouts.error')
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center" id="kt_table">
                    <thead>
                        <tr class="text-center text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="w-50px text-center align-middle"> # </th>
                            <th class="w-50px text-center align-middle">
                                <div class="d-flex align-items-center justify-content-center" style="height: 48px;">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input h-20px w-20px" type="checkbox" id="selectAll" />
                                    </div>
                                </div>
                            </th>
                            <th class="min-w-250px text-start align-middle"> الطالب </th>
                            <th class="min-w-125px text-center align-middle"> الجوال </th>
                            <th class="min-w-125px text-center align-middle"> تاريخ الميلاد </th>
                            <th class="min-w-100px text-center align-middle"> الحالة </th>
                            <th class="text-center min-w-125px align-middle"> العمليات </th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold text-center align-middle"></tbody>
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
<script>
    function showStudentModal(id) {
        fetchModalContent('{{ route('students.details') }}', { id: id });
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

    var table;
    var tableId = 'kt_table';
    var customAjaxUrl = '{{ route("membership.list") }}';
    var columns = [
        { data: "id", name: "id", orderable: false, searchable: false, render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
        { data: "checkbox", name: "checkbox", orderable: false, searchable: false, className: "text-center align-middle" },
        { data: "name", name: "name", className: "text-start align-middle" },
        { data: "mobile", name: "mobile", className: "text-center align-middle" },
        { data: "dob", name: "dob", className: "text-center align-middle" },
        { data: "status", name: "status", orderable: false, searchable: false, className: "text-center align-middle" },
        { data: "actions", name: "actions", orderable: false, searchable: false, className: "text-center align-middle" }
    ];

    var filterFields = ['#search', '#gender', '#date_from', '#date_to', '#is_today'];

    $(document).ready(function() {
        // Initialize flatpickr
        $('.date-picker').flatpickr({ dateFormat: 'Y-m-d' });

        // Select All handler
        $('#selectAll').on('change', function() {
            $('.checkboxes').prop('checked', this.checked);
        });

        // Today Button
        $('#todayBtn').on('click', function(e) {
            e.preventDefault();
            $('#filter_form')[0].reset();
            $('#filter_form select').val('').trigger('change');
            $('#is_today').val('1');
            table.draw();
        });

        // Show All Button
        $('#showAllBtn').on('click', function(e) {
            e.preventDefault();
            $('#filter_form')[0].reset();
            $('#filter_form select').val('').trigger('change');
            $('#is_today').val('all');
            table.draw();
        });

        // Reset Filter
        $('#resetFilter').on('click', function(e) {
            e.preventDefault();
            $('#filter_form')[0].reset();
            $('#filter_form select').val('').trigger('change');
            $('#is_today').val(''); 
            table.draw();
        });


        // Single student Reply email
        $(document).on('click', '.Reply', function() {
            var id = $(this).data('id');
            window.EmailComposer.send({
                type: 'single',
                id: id,
                url: '{{ route("send.message") }}',
                title: 'إرسال بريد إلكتروني للطالب'
            });
        });

        // Bulk Email (CEmail)
        $(document).on('click', '.CEmail', function() {
            var emails = window.EmailComposer.collectEmails();
            if (emails.length === 0) {
                Swal.fire({ text: 'يجب اختيار طالب واحد على الأقل!', icon: 'warning', buttonsStyling: false, confirmButtonText: 'حسناً', customClass: { confirmButton: 'btn btn-primary' } });
                return;
            }
            window.EmailComposer.send({
                type: 'bulk',
                recipients: emails,
                url: '{{ route("send.CEmail") }}',
                title: 'إرسال بريد إلكتروني للطلاب المحددين'
            });
        });

        // Bulk Activate and Send Credentials
        $(document).on('click', '.BulkActivate', function() {
            var selectedIds = [];
            $('.checkboxes:checked').each(function() {
                selectedIds.push($(this).attr('data-id'));
            });

            if (selectedIds.length === 0) {
                Swal.fire({ 
                    text: 'يجب اختيار طالب واحد على الأقل من القائمة!', 
                    icon: 'warning', 
                    buttonsStyling: false, 
                    confirmButtonText: 'حسناً', 
                    customClass: { confirmButton: 'btn btn-primary' } 
                });
                return;
            }

            Swal.fire({
                title: 'تأكيد التفعيل الجماعي',
                text: "هل أنت متأكد من تفعيل " + selectedIds.length + " من الحسابات المختارة وإرسال بيانات دخولهم آلياً؟",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'نعم، تفعيل الكل',
                cancelButtonText: 'إلغاء',
                customClass: { 
                    confirmButton: 'btn btn-success', 
                    cancelButton: 'btn btn-light' 
                },
                buttonsStyling: false,
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return $.ajax({
                        url: '{{ route("students.bulk.status.membership") }}',
                        method: 'POST',
                        data: { 
                            ids: selectedIds, 
                            _token: '{{ csrf_token() }}' 
                        }
                    }).then(response => {
                        return response;
                    }).catch(error => {
                        Swal.showValidationMessage(error.responseJSON ? error.responseJSON.message : 'حدث خطأ أثناء المعالجة');
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    var response = result.value;
                    Swal.fire({
                        text: response.message,
                        icon: 'success',
                        buttonsStyling: false,
                        confirmButtonText: 'حسناً',
                        customClass: { confirmButton: 'btn btn-primary' }
                    }).then(() => {
                        if (response.campaign_id && window.EmailCampaignMonitor) {
                            window.EmailCampaignMonitor.start(response.campaign_id, response.total_recipients, response.redirect_url);
                        }
                        if(table) table.ajax.reload(null, false);
                    });
                }
            });
        });

    });
</script>
@php $status_route = 'students.status.membership'; @endphp
@include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'membership'])
@stop
