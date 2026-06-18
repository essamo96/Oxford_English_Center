@extends('admin.layout.master')

@section('title', 'أرشيف رسائل SMS')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-primary">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">إدارة خدمة tweetSMS</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">أرشيف الرسائل</li>
@stop

@section('page-content')
@php $active_menu = 'sms_archive'; @endphp

<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1 text-primary">
                <i class="ki-duotone ki-sms fs-4 text-primary me-2"><span class="path1"></span><span class="path2"></span></i>
                سجل الرسائل النصية
            </span>
        </div>
        <div class="card-toolbar gap-3">
            <button type="button" class="btn btn-light-primary btn-sm" data-bs-toggle="modal" data-bs-target="#smsShuttleModal">
                <i class="ki-duotone ki-send fs-4 me-1"><span class="path1"></span><span class="path2"></span></i> إرسال رسالة جديدة
            </button>
        </div>
    </div>
    <div class="card-body py-4">
        <!-- Filters -->
        <form id="filterForm" class="row g-3 mb-5 align-items-end">
            <div class="col-lg-3 col-md-4">
                <label class="form-label fw-semibold fs-7">البرنامج</label>
                <select name="program_id" id="filter_program" class="form-select form-select-solid form-select-sm" data-control="select2" data-placeholder="-- الكل --">
                    <option value=""></option>
                    @foreach($programs as $p)
                        <option value="{{ $p->id }}">{{ $p->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-3 col-md-4">
                <label class="form-label fw-semibold fs-7">المجموعة</label>
                <select name="group_id" id="filter_group" class="form-select form-select-solid form-select-sm" data-control="select2" data-placeholder="-- الكل --">
                    <option value=""></option>
                    @foreach($groups as $g)
                        <option value="{{ $g->id }}">{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-4">
                <label class="form-label fw-semibold fs-7">الحالة</label>
                <select name="status" id="filter_status" class="form-select form-select-solid form-select-sm">
                    <option value="">-- الكل --</option>
                    <option value="success">ناجح</option>
                    <option value="failed">فشل</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-6">
                <label class="form-label fw-semibold fs-7">من تاريخ</label>
                <input type="date" name="date_from" id="filter_date_from" class="form-control form-control-solid form-control-sm">
            </div>
            <div class="col-lg-2 col-md-6">
                <label class="form-label fw-semibold fs-7">إلى تاريخ</label>
                <input type="date" name="date_to" id="filter_date_to" class="form-control form-control-solid form-control-sm">
            </div>
            <div class="col-12 mt-2 text-end">
                <button type="reset" class="btn btn-sm btn-light-danger me-2" id="resetBtn"><i class="ki-duotone ki-arrows-loop"></i> إعادة ضبط</button>
                <button type="button" class="btn btn-sm btn-success" id="searchBtn"><i class="ki-duotone ki-magnifier"></i> بحث</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center" id="archiveTable">
                <thead>
                    <tr class="text-center text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th>#</th>
                        <th>المستلم</th>
                        <th>الجوال</th>
                        <th>الرسالة</th>
                        <th>الحالة</th>
                        <th>سبب الفشل</th>
                        <th>البرنامج / المجموعة</th>
                        <th>المرسل</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
$(document).ready(function() {
    var table = $('#archiveTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.sms.archive.data') }}",
            data: function (d) {
                d.program_id = $('#filter_program').val();
                d.group_id = $('#filter_group').val();
                d.status = $('#filter_status').val();
                d.date_from = $('#filter_date_from').val();
                d.date_to = $('#filter_date_to').val();
            }
        },
        columns: [
            {data: 'id', name: 'id', render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; }},
            {data: 'student_name', name: 'student_name', orderable: false, searchable: false, render: function(data, type, row) {
                if (row.student_id) {
                    return '<a href="javascript:void(0)" class="text-primary fw-bold text-hover-primary" onclick="showStudentDetails('+row.student_id+')"><i class="bi bi-person-badge"></i> '+data+'</a>';
                }
                return '<span class="text-dark fw-bold">'+data+'</span>';
            }},
            {data: 'mobile', name: 'mobile', render: function(d){ return '<span dir="ltr">'+d+'</span>';}},
            {data: 'message', name: 'message', render: function(d){ return '<div style="max-width:300px; white-space:normal; font-size:0.85rem;">'+d+'</div>';}},
            {data: 'status', name: 'status', render: function(d){
                if(d === 'success') return '<span class="badge badge-light-success">ناجح</span>';
                return '<span class="badge badge-light-danger">فشل</span>';
            }},
            {data: 'error_message', name: 'error_message', render: function(d){ return d ? '<span class="text-danger fs-8">'+d+'</span>' : '-';}},
            {data: 'program_group', name: 'program_group', orderable: false, searchable: false},
            {data: 'sender_name', name: 'sender_name', orderable: false, searchable: false},
            {data: 'created_at', name: 'created_at'}
        ],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/ar.json"
        }
    });

    $('#searchBtn').on('click', function(e) {
        e.preventDefault();
        table.draw();
    });

    $('#resetBtn').on('click', function(e) {
        setTimeout(function() {
            $('#filter_program').val(null).trigger('change');
            $('#filter_group').val(null).trigger('change');
            table.draw();
        }, 100);
    });
});

function showStudentDetails(id) {
    Swal.fire({
        title: 'جاري التحميل...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });
    $.post('{{ url("admin/students/modal") }}', { _token: '{{ csrf_token() }}', id: id }, function(res) {
        Swal.close();
        if($('#kt_modal_1').length === 0) {
            $('body').append('<div class="modal fade" tabindex="-1" id="kt_modal_1"><div class="modal-dialog modal-dialog-centered modal-xl"><div class="modal-content"><div class="modal-header"><h3 class="modal-title">بيانات الطالب</h3><div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close"><i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i></div></div><div class="modal-body" id="model_body"></div><div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">إغلاق</button></div></div></div></div>');
        }
        $('#model_body').html(res);
        $('#kt_modal_1').modal('show');
    }).fail(function() {
        Swal.close();
        Swal.fire('خطأ', 'لا يمكن جلب تفاصيل الطالب', 'error');
    });
}
</script>
@stop
