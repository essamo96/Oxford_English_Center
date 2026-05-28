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
        <div class="card-toolbar">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <label class="form-check form-switch form-check-custom form-check-solid bg-light-info px-3 py-2 rounded border border-info border-dashed">
                    <input class="form-check-input" type="checkbox" id="filter_placement_only" name="placement_test_only" value="1">
                    <span class="ms-2 fw-bold text-info fs-7"><i class="bi bi-clipboard-check me-1"></i> اختبار تحديد المستوى فقط</span>
                </label>
                <label class="form-check form-switch form-check-custom form-check-solid bg-light-success px-3 py-2 rounded border border-success border-dashed">
                    <input class="form-check-input" type="checkbox" id="filter_placement_graded" name="placement_graded" value="1">
                    <span class="ms-2 fw-bold text-success fs-7"><i class="bi bi-award me-1"></i> تم رصد العلامة</span>
                </label>
            </div>
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
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تأكيد الدفعة المالية وتفعيل الحساب</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="verifyForm">
                @csrf
                <input type="hidden" name="id" id="verify_id">
                <div class="modal-body">
                    {{-- Applicant + Guardian (shown only for kids/underage) --}}
                    <div id="applicant_details_box" class="mb-4 p-4 rounded border bg-light" style="display:none;">
                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-person-vcard me-2"></i>بيانات المتقدم</h6>
                        <div class="row g-3 fs-7">
                            <div class="col-md-6"><strong>الاسم:</strong> <span id="ap_name" class="text-dark"></span></div>
                            <div class="col-md-6"><strong>الجوال:</strong> <span id="ap_mobile" class="text-dark"></span></div>
                            <div class="col-md-6"><strong>البريد:</strong> <span id="ap_email" class="text-dark"></span></div>
                            <div class="col-md-6"><strong>العمر:</strong> <span id="ap_age" class="text-dark"></span></div>
                        </div>
                        <div id="guardian_box" class="mt-4 pt-3 border-top" style="display:none;">
                            <h6 class="fw-bold text-warning mb-3"><i class="bi bi-people-fill me-2"></i>بيانات ولي الأمر</h6>
                            <div class="row g-3 fs-7">
                                <div class="col-md-6"><strong>الاسم:</strong> <span id="gd_name" class="text-dark"></span></div>
                                <div class="col-md-6"><strong>الجوال:</strong> <span id="gd_phone" class="text-dark"></span></div>
                                <div class="col-md-6"><strong>البريد:</strong> <span id="gd_email" class="text-dark"></span></div>
                                <div class="col-md-6"><strong>صلة القرابة:</strong> <span id="gd_relationship" class="text-dark"></span></div>
                            </div>
                        </div>
                    </div>

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

                    {{-- Receipt preview — toggleable via button --}}
                    <div class="mb-3 d-flex align-items-center justify-content-between">
                        <span class="fw-bold text-dark"><i class="bi bi-receipt me-1 text-info"></i> إيصال الدفع</span>
                        <button type="button" id="toggle_receipt_btn" class="btn btn-sm btn-light-info" style="display:none;">
                            <i class="bi bi-eye-fill me-1"></i> <span id="toggle_receipt_label">معاينة الإيصال</span>
                        </button>
                    </div>
                    <div id="receipt_preview_box" class="mb-4 text-center" style="display:none;">
                        <div id="receipt_preview_content" class="p-3 rounded border bg-light"></div>
                    </div>

                    <div id="program_group_box">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label d-flex align-items-center justify-content-between">
                                    <span>البرنامج الدراسي <small class="text-muted">(Program)</small></span>
                                    <span id="default_program_badge" class="badge badge-light-success fs-9 fw-bold" style="display:none;"><i class="bi bi-check-circle me-1"></i> اختيار الطالب</span>
                                </label>
                                <select name="program_id" id="verify_program_id" class="form-select border-info" onchange="loadGroups(this.value); maybeShowChangeProgramNotice();">
                                    <option value="">-- اختر البرنامج --</option>
                                    @foreach($Programs as $p)
                                        <option value="{{ $p->id }}">{{ $p->title }}</option>
                                    @endforeach
                                </select>
                                <div id="change_program_notice" class="form-text text-warning fw-bold mt-2" style="display:none;">
                                    <i class="bi bi-arrow-repeat me-1"></i>
                                    لقد غيّرت برنامج الطالب — سيُحدَّث إجمالي المستحق حسب رسوم البرنامج الجديد.
                                </div>
                                <input type="hidden" name="change_program_to" id="change_program_to_input">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">المجموعة / المستوى (Group):</label>
                                <select name="group_id" id="verify_group_id" class="form-select border-info">
                                    <option value="">-- اختر المجموعة --</option>
                                </select>
                                <div class="form-text text-muted">تسكين الطالب في مجموعة يفعل حسابه تلقائياً</div>
                            </div>
                        </div>
                    </div>

                    <div id="placement_test_notice" class="alert alert-light-info border border-info border-dashed" style="display:none;">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        طلب لـ <strong>اختبار تحديد المستوى</strong> — لا حاجة لتسكين الطالب في برنامج/مجموعة الآن.
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
    var filterFields = ['#filter_placement_only', '#filter_placement_graded'];
    // map: front id → backend param
    var filterParamMap = {
        'filter_placement_only': 'placement_test_only',
        'filter_placement_graded': 'placement_graded',
    };
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
        // State for change-program tracking + receipt preview
        let originalProgramId = null;
        let receiptUrl = null;
        let receiptShown = false;

        // Function to open verification modal
        window.verifyPayment = function(id, claimed, total, programId, studentId, paidType) {
            paidType = paidType || '';
            $('#verify_id').val(id);
            $('#total_due_display').val(total + ' ILS');
            $('#claimed_amount').val(claimed + ' ILS');
            $('#verified_amount').val(claimed);

            // Reset boxes + state
            $('#applicant_details_box').hide();
            $('#guardian_box').hide();
            $('#receipt_preview_box').hide();
            $('#receipt_preview_content').empty();
            $('#toggle_receipt_btn').hide();
            $('#change_program_notice').hide();
            $('#change_program_to_input').val('');
            $('#default_program_badge').hide();
            receiptShown = false;
            receiptUrl = null;

            // Placement Test → no program/group assignment needed
            const isPlacementTest = /Placement\s*Test/i.test(paidType) || /اختبار/.test(paidType);
            if (isPlacementTest) {
                $('#program_group_box').hide();
                $('#placement_test_notice').show();
                $('#verify_program_id').val('');
                $('#verify_group_id').html('<option value="">-- اختر المجموعة --</option>');
                originalProgramId = null;
            } else {
                $('#program_group_box').show();
                $('#placement_test_notice').hide();
                originalProgramId = programId ? String(programId) : null;
                if (programId) {
                    $('#verify_program_id').val(programId);
                    $('#default_program_badge').show();
                    loadGroups(programId);
                } else {
                    $('#verify_program_id').val('');
                    $('#verify_group_id').html('<option value="">-- اختر المجموعة --</option>');
                }
            }

            // Pull the receipt link from the row — keep hidden behind a button
            try {
                const row = $('.btn-verify[data-id="' + id + '"]').closest('tr');
                const link = row.find('a[href*="uploads/"]').first();
                if (link.length) {
                    receiptUrl = link.attr('href');
                    $('#toggle_receipt_btn').show();
                    $('#toggle_receipt_label').text('معاينة الإيصال');
                    $('#toggle_receipt_btn').find('i').removeClass('bi-eye-slash-fill').addClass('bi-eye-fill');
                }
            } catch (e) { /* ignore */ }

            // Fetch applicant + guardian info
            if (studentId) {
                $.get('{{ route("admin.financial.pending.student", ":id") }}'.replace(':id', studentId), function(res) {
                    if (!res || !res.success) return;
                    $('#ap_name').text(res.student.name || '-');
                    $('#ap_mobile').text(res.student.mobile || '-');
                    $('#ap_email').text(res.student.email || '-');
                    $('#ap_age').text(res.student.age !== null ? (res.student.age + ' سنة') : '-');
                    $('#applicant_details_box').show();

                    if (res.student.is_child && res.parent) {
                        $('#gd_name').text(res.parent.name || '-');
                        $('#gd_phone').text(res.parent.phone || '-');
                        $('#gd_email').text(res.parent.email || '-');
                        $('#gd_relationship').text(res.parent.relationship || '-');
                        $('#guardian_box').show();
                    }
                });
            }

            $('#verifyModal').modal('show');
            updateBalancePreview(claimed, total);
        };

        // Toggle inline receipt preview
        $(document).on('click', '#toggle_receipt_btn', function () {
            if (!receiptUrl) return;
            if (!receiptShown) {
                const lower = (receiptUrl || '').toLowerCase();
                let html = '';
                if (/\.(jpe?g|png|gif|webp)(\?|$)/.test(lower)) {
                    html = '<img src="' + receiptUrl + '" alt="receipt" style="max-width:100%;max-height:420px;border-radius:8px;">';
                } else if (/\.pdf(\?|$)/.test(lower)) {
                    html = '<embed src="' + receiptUrl + '" type="application/pdf" width="100%" height="450" style="border-radius:8px;">';
                } else {
                    html = '<a href="' + receiptUrl + '" target="_blank" class="btn btn-light-info"><i class="bi bi-box-arrow-up-right me-1"></i> فتح الإيصال في تبويب جديد</a>';
                }
                $('#receipt_preview_content').html(html);
                $('#receipt_preview_box').slideDown(180);
                $('#toggle_receipt_label').text('إخفاء المعاينة');
                $('#toggle_receipt_btn').find('i').removeClass('bi-eye-fill').addClass('bi-eye-slash-fill');
                receiptShown = true;
            } else {
                $('#receipt_preview_box').slideUp(180);
                $('#toggle_receipt_label').text('معاينة الإيصال');
                $('#toggle_receipt_btn').find('i').removeClass('bi-eye-slash-fill').addClass('bi-eye-fill');
                receiptShown = false;
            }
        });

        // Detect program change and reflect in the hidden input + notice
        window.maybeShowChangeProgramNotice = function () {
            const chosen = $('#verify_program_id').val();
            if (!originalProgramId || !chosen) {
                $('#change_program_notice').hide();
                $('#change_program_to_input').val('');
                return;
            }
            if (String(chosen) !== String(originalProgramId)) {
                $('#change_program_notice').show();
                $('#change_program_to_input').val(chosen);
                $('#default_program_badge').hide();
            } else {
                $('#change_program_notice').hide();
                $('#change_program_to_input').val('');
                $('#default_program_badge').show();
            }
        };

        // Delegated click handler for verify buttons (uses data-attributes to avoid inline JS escaping)
        $(document).on('click', '.btn-verify', function(e) {
            const $btn = $(this);
            const id = $btn.data('id');
            const claimed = parseFloat($btn.data('claimed')) || 0;
            const total = parseFloat($btn.data('total')) || 0;
            const programId = $btn.data('program') || null;
            const studentId = $btn.data('student') || null;
            const paidType = $btn.data('type') || '';
            // call existing helper
            window.verifyPayment(id, claimed, total, programId, studentId, paidType);
        });

        // Delegated click for refund (keeps original refundPayment call behavior)
        $(document).on('click', '.btn-refund', function(e) {
            const id = $(this).data('id');
            if (id) refundPayment(id);
        });

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
