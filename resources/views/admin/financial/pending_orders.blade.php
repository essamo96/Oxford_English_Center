@extends('admin.layout.master')

@section('title', 'الطلبات المالية العالقة')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">الإدارة المالية</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">الطلبات العالقة</li>
@stop

@section('page-content')
@php $active_menu = 'financial'; @endphp

<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <h3 class="fw-bold">مراجعة طلبات التسجيل والمدفوعات</h3>
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.masterLayouts.error')
        <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center" id="pending_financial_table">
            <thead>
                <tr class="text-center text-muted fw-bold fs-7 text-uppercase gs-0">
                    <th class="text-center w-50px"> # </th>
                    <th class="text-center"> الطالب </th>
                    <th class="text-center"> نوع الطلب </th>
                    <th class="text-center"> إجمالي المستحق </th>
                    <th class="text-center"> المبلغ المدفوع </th>
                    <th class="text-center"> إيصال الدفع </th>
                    <th class="text-center"> تاريخ الطلب </th>
                    <th class="text-center"> العمليات </th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-semibold text-center"></tbody>
        </table>
    </div>
</div>

<!-- Verification Modal -->
<div class="modal fade" id="verifyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تأكيد الدفعة المالية وتفعيل الحساب</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="verifyForm">
                @csrf
                <input type="hidden" name="id" id="verify_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">إجمالي المبلغ المستحق (Total Due):</label>
                        <input type="text" id="total_due_display" class="form-control bg-light fw-bold text-primary" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">المبلغ الذي أدخله الطالب (Claimed):</label>
                        <input type="text" id="claimed_amount" class="form-control bg-light" readonly>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold text-success">المبلغ الفعلي المستلم (Verified Amount):</label>
                        <input type="number" name="verified_amount" id="verified_amount" class="form-control border-success fs-4 fw-bold" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">البرنامج الدراسي (Program):</label>
                            <select name="program_id" id="verify_program_id" class="form-select border-info" onchange="loadGroups(this.value)">
                                <option value="">-- اختر البرنامج --</option>
                                @foreach($Programs as $p)
                                    <option value="{{ $p->id }}">{{ $p->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">المجموعة / المستوى (Group):</label>
                            <select name="group_id" id="verify_group_id" class="form-select border-info">
                                <option value="">-- اختر المجموعة --</option>
                            </select>
                            <div class="form-text text-muted">تسكين الطالب في مجموعة يفعل حسابه تلقائياً</div>
                        </div>
                    </div>

                    <div id="balance_preview" class="alert alert-light-info d-none">
                        المتبقي: <span id="remaining_val" class="fw-bold text-danger">0</span> ILS
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success">تأكيد وتفعيل</button>
                </div>
            </form>
        </div>
    </div>
</div>

@stop

@section('js')
<script>
    var table;
    var tableId = 'pending_financial_table';
    var columns = [
        { data: 'id', name: 'id', render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
        { data: "student", name: "student.name" },
        { data: "type", name: "student_paid_type" },
        { data: "total_due_amount", name: "total_due_amount", render: function(d){ return '<span class="fw-bold text-dark">'+d+' ILS</span>'; } },
        { data: "student_fee_paid", name: "student_fee_paid", render: function(d){ return '<span class="fw-bold text-success">'+d+' ILS</span>'; } },
        { data: "receipt", name: "receipt", orderable: false, searchable: false },
        { data: "created_at", name: "created_at" },
        { data: "actions", name: "actions", orderable: false, searchable: false }
    ];

    $(document).ready(function() {
        // Function to open verification modal
        window.verifyPayment = function(id, claimed, total, programId) {
            $('#verify_id').val(id);
            $('#total_due_display').val(total + ' ILS');
            $('#claimed_amount').val(claimed + ' ILS');
            $('#verified_amount').val(claimed);
            
            // Set program and load groups
            if (programId) {
                $('#verify_program_id').val(programId);
                loadGroups(programId);
            } else {
                $('#verify_program_id').val('');
                $('#verify_group_id').html('<option value="">-- اختر المجموعة --</option>');
            }

            $('#verifyModal').modal('show');
            updateBalancePreview(claimed, total);
        };

        window.loadGroups = function(programId) {
            if (!programId) {
                $('#verify_group_id').html('<option value="">-- اختر المجموعة --</option>');
                return;
            }
            
            $('#verify_group_id').html('<option value="">جاري التحميل...</option>');
            
            $.get('{{ route("admin.financial.groups_by_program", ":id") }}'.replace(':id', programId), function(data) {
                let html = '<option value="">-- اختر المجموعة --</option>';
                data.forEach(function(g) {
                    html += `<option value="${g.id}">${g.name} (${g.start_date || 'N/A'})</option>`;
                });
                $('#verify_group_id').html(html);
            });
        };

        $('#verified_amount').on('input', function() {
            const total = parseFloat($('#total_due_display').val());
            const verified = parseFloat($(this).val()) || 0;
            updateBalancePreview(verified, total);
        });

        function updateBalancePreview(verified, total) {
            const remaining = total - verified;
            $('#remaining_val').text(remaining.toFixed(2));
            $('#balance_preview').removeClass('d-none');
        }

        $('#verifyForm').on('submit', function(e) {
            e.preventDefault();
            const formData = $(this).serialize();
            
            Swal.fire({
                title: 'جاري المعالجة...',
                didOpen: () => { Swal.showLoading(); }
            });

            $.post('{{ route("admin.financial.verify") }}', formData, function(res) {
                if (res.status === 'success') {
                    Swal.fire('تم!', res.message, 'success');
                    $('#verifyModal').modal('hide');
                    $('#' + tableId).DataTable().ajax.reload();
                } else {
                    Swal.fire('خطأ', res.message, 'error');
                }
            });
        });
    });
</script>
@include('admin.layout.masterLayouts.datatableMaster', [
    'active_menu' => 'financial',
    'customAjaxUrl' => route('admin.financial.pending.list')
])
@stop
