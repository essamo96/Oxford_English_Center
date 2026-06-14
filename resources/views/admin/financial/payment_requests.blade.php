@extends('admin.layout.master')
@section('title', 'طلبات الدفع من الطلاب')
@php $active_menu = 'student_payments'; @endphp

@section('css')
<link rel="stylesheet" href="{{ url('assets/admin/plugins/datatables/datatables.bundle.css') }}">
<style>
.filter-bar { background:#fff; border-radius:10px; padding:16px 20px; box-shadow:0 1px 6px rgba(0,0,0,.06); margin-bottom:20px; display:flex; gap:12px; flex-wrap:wrap; align-items:center; }
.filter-bar select, .filter-bar input { border-radius:8px; border:1.5px solid #e4e7ea; font-size:.87rem; padding:7px 12px; }
.receipt-thumb { cursor:pointer; transition:transform .15s; }
.receipt-thumb:hover { transform:scale(1.08); }
.modal-receipt-img { max-width:100%; max-height:70vh; border-radius:10px; }
</style>
@endsection

@section('content')
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li><a href="{{ url('admin/dashboard') }}">الرئيسية</a><i class="fa fa-circle"></i></li>
        <li><span>الإدارة المالية</span><i class="fa fa-circle"></i></li>
        <li><span>طلبات الدفع من الطلاب</span></li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="bi bi-credit-card-2-front text-primary fs-4"></i>
                    <span class="caption-subject font-blue bold uppercase ms-2">طلبات الدفع من الطلاب</span>
                </div>
            </div>
            <div class="portlet-body">

                {{-- Filters --}}
                <div class="filter-bar">
                    <select id="filterStatus" class="form-select" style="width:180px;">
                        <option value="pending">بانتظار المراجعة</option>
                        <option value="approved">مقبول</option>
                        <option value="rejected">مرفوض</option>
                        <option value="">الكل</option>
                    </select>
                    @if(!isset($isBranchScoped) || !$isBranchScoped)
                    <select id="filterBranch" class="form-select" style="width:180px;">
                        <option value="">كل الفروع</option>
                        @foreach(\App\Models\Branches::all() as $br)
                            <option value="{{ $br->id }}">{{ $br->name_ar }}</option>
                        @endforeach
                    </select>
                    @endif
                    <button id="filterBtn" class="btn btn-primary btn-sm"><i class="bi bi-funnel me-1"></i>تطبيق</button>
                </div>

                <table id="payRequestsTable" class="table table-striped table-hover dt-responsive" style="width:100%">
                    <thead>
                        <tr>
                            <th>الطالب</th>
                            <th>الفرع</th>
                            <th>المجموعة / البرنامج</th>
                            <th>المبلغ</th>
                            <th>الإيصال</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                            <th>الإجراء</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Approve Modal --}}
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-success"><i class="bi bi-check-circle me-2"></i>قبول الدفعة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>هل تريد قبول هذه الدفعة؟ سيتم تسجيلها في السجل المالي وإشعار الطالب.</p>
                <div class="mb-3">
                    <strong id="approveAmount" class="text-success fs-5"></strong>
                </div>
                <div>
                    <label class="form-label">ملاحظة (اختياري)</label>
                    <textarea id="approveNotes" class="form-control" rows="2" placeholder="ملاحظة للطالب..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                <button class="btn btn-success" id="confirmApprove"><i class="bi bi-check-lg me-1"></i>تأكيد القبول</button>
            </div>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger"><i class="bi bi-x-circle me-2"></i>رفض الدفعة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>يرجى تحديد سبب الرفض. سيصل الإشعار للطالب فور الرفض.</p>
                <div>
                    <label class="form-label fw-bold">سبب الرفض <span class="text-danger">*</span></label>
                    <textarea id="rejectNotes" class="form-control" rows="3" placeholder="أدخل سبب الرفض..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                <button class="btn btn-danger" id="confirmReject"><i class="bi bi-x-lg me-1"></i>تأكيد الرفض</button>
            </div>
        </div>
    </div>
</div>

{{-- Receipt Modal --}}
<div class="modal fade" id="receiptModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h6 class="modal-title">الإيصال</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-2">
                <img id="receiptImgLg" src="" class="modal-receipt-img">
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ url('assets/admin/plugins/datatables/datatables.bundle.js') }}"></script>
<script>
$(function() {
    var currentId = null;

    var table = $('#payRequestsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.financial.student-payments.list") }}',
            data: function(d) {
                d.status     = $('#filterStatus').val();
                d.branch_id  = $('#filterBranch').val() || '';
            }
        },
        columns: [
            { data: 'student_info', name: 'student_info', orderable: false },
            { data: 'branch_col',   name: 'branch_col',   orderable: false },
            { data: 'group_col',    name: 'group_col',    orderable: false },
            { data: 'amount_col',   name: 'amount_col',   orderable: false },
            { data: 'receipt_col',  name: 'receipt_col',  orderable: false },
            { data: 'status_col',   name: 'status_col',   orderable: false },
            { data: 'date_col',     name: 'date_col',     orderable: false },
            { data: 'actions',      name: 'actions',      orderable: false },
        ],
        language: { url: '{{ url("assets/admin/plugins/datatables/lang/Arabic.json") }}' },
        pageLength: 15,
    });

    $('#filterBtn').on('click', function() { table.ajax.reload(); });

    // Receipt preview
    $(document).on('click', '.receipt-thumb', function() {
        var url  = $(this).data('receipt-url');
        var type = $(this).data('receipt-type');
        if (type === 'image') {
            $('#receiptImgLg').attr('src', url).show();
            new bootstrap.Modal(document.getElementById('receiptModal')).show();
        } else {
            window.open(url, '_blank');
        }
    });

    // Approve
    $(document).on('click', '.btn-approve', function() {
        currentId = $(this).data('id');
        $('#approveAmount').text('₪ ' + parseFloat($(this).data('amount')).toFixed(2));
        $('#approveNotes').val('');
        new bootstrap.Modal(document.getElementById('approveModal')).show();
    });

    $('#confirmApprove').on('click', function() {
        $(this).prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>جارٍ...');
        $.ajax({
            type: 'POST',
            url:  '/admin/financial/student-payments/' + currentId + '/approve',
            data: { _token: '{{ csrf_token() }}', admin_notes: $('#approveNotes').val() },
            success: function(res) {
                bootstrap.Modal.getInstance(document.getElementById('approveModal')).hide();
                table.ajax.reload();
                toastSuccess(res.message || 'تم القبول بنجاح.');
            },
            error: function(xhr) {
                toastError(xhr.responseJSON?.message || 'حدث خطأ.');
            },
            complete: function() {
                $('#confirmApprove').prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i>تأكيد القبول');
            }
        });
    });

    // Reject
    $(document).on('click', '.btn-reject', function() {
        currentId = $(this).data('id');
        $('#rejectNotes').val('');
        new bootstrap.Modal(document.getElementById('rejectModal')).show();
    });

    $('#confirmReject').on('click', function() {
        if (!$('#rejectNotes').val().trim()) {
            $('#rejectNotes').addClass('is-invalid');
            return;
        }
        $('#rejectNotes').removeClass('is-invalid');
        $(this).prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>جارٍ...');
        $.ajax({
            type: 'POST',
            url:  '/admin/financial/student-payments/' + currentId + '/reject',
            data: { _token: '{{ csrf_token() }}', admin_notes: $('#rejectNotes').val() },
            success: function(res) {
                bootstrap.Modal.getInstance(document.getElementById('rejectModal')).hide();
                table.ajax.reload();
                toastSuccess(res.message || 'تم الرفض وإشعار الطالب.');
            },
            error: function(xhr) {
                toastError(xhr.responseJSON?.message || 'حدث خطأ.');
            },
            complete: function() {
                $('#confirmReject').prop('disabled', false).html('<i class="bi bi-x-lg me-1"></i>تأكيد الرفض');
            }
        });
    });

    function toastSuccess(msg) {
        Swal.fire({ icon: 'success', title: msg, timer: 2500, showConfirmButton: false });
    }
    function toastError(msg) {
        Swal.fire({ icon: 'error', title: 'خطأ', text: msg });
    }

    // Real-time Pusher counter updates
    if (typeof Pusher !== 'undefined') {
        var pusher = new Pusher('{{ config("broadcasting.connections.pusher.key") }}', { cluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}' });
        @php $bid = auth()->user()->branch_id; @endphp
        var ch = pusher.subscribe('{{ $bid ? "admin-notifications-branch-$bid" : "admin-notifications" }}');
        ch.bind('counters.updated', function() { table.ajax.reload(null, false); });
    }
});
</script>
@endsection
