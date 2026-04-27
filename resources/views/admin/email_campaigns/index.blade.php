@extends('admin.layout.master')

@section('title', 'إدارة حملات البريد الإلكتروني')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark">حملات البريد</li>
@stop

@section('page-content')
    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <span class="card-label fw-bold fs-3 mb-1 text-info">
                    <i class="ki-duotone ki-sms fs-2 text-primary me-2"><span class="path1"></span><span class="path2"></span></i>
                    سجل حملات الإرسال
                </span>
            </div>
        </div>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center" id="campaigns_table">
                    <thead>
                        <tr class="text-center text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-50px">#</th>
                            <th class="min-w-150px">الموضوع</th>
                            <th class="min-w-100px">المرسل</th>
                            <th class="min-w-100px">إجمالي المستلمين</th>
                            <th class="min-w-150px">التقدم</th>
                            <th class="min-w-100px">الحالة</th>
                            <th class="min-w-100px">التاريخ</th>
                            <th class="min-w-100px">العمليات</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    var table = $('#campaigns_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.email_campaigns.list') }}",
        columns: [
            {data: 'id', name: 'id', orderable: false, searchable: false},
            {data: 'subject', name: 'subject'},
            {data: 'admin.name', name: 'admin.name', defaultContent: 'النظام'},
            {data: 'total_recipients', name: 'total_recipients'},
            {data: 'progress', name: 'progress', orderable: false, searchable: false},
            {data: 'status_label', name: 'status_label'},
            {data: 'created_at', name: 'created_at', render: function(data) {
                return moment(data).format('YYYY-MM-DD HH:mm');
            }},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Arabic.json"
        },
        order: [[6, 'desc']]
    });

    // Refresh progress every 5 seconds if there are active campaigns
    setInterval(function() {
        table.ajax.reload(null, false);
    }, 5000);

    function deleteCampaign(id) {
        Swal.fire({
            text: "هل أنت متأكد من حذف سجل هذه الحملة؟",
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: "نعم، احذف",
            cancelButtonText: "إلغاء",
            customClass: {
                confirmButton: "btn btn-danger",
                cancelButton: "btn btn-light"
            }
        }).then(function (result) {
            if (result.value) {
                $.post("{{ url('admin/email-campaigns/delete') }}/" + id, {
                    _token: "{{ csrf_token() }}"
                }, function(res) {
                    Swal.fire({ text: res.message, icon: "success", buttonsStyling: false, confirmButtonText: "حسناً", customClass: { confirmButton: "btn btn-primary" } });
                    table.ajax.reload();
                });
            }
        });
    }
</script>
@stop
