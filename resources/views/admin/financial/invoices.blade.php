@extends('admin.layout.master')

@section('title', 'كشف حساب الطلاب والفواتير')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">الإدارة المالية</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">كشف حساب الطلاب</li>
@stop

@section('css')
<style>
    /* Row-level highlights for special transaction types */
    #kt_invoices_table tbody tr.row-credit  td { background: rgba(0,158,247,0.06) !important; border-inline-start: 3px solid #009ef7 !important; }
    #kt_invoices_table tbody tr.row-refund  td { background: rgba(241,65,108,0.06) !important; border-inline-start: 3px solid #f1416c !important; }
    #kt_invoices_table tbody tr.row-payment td { background: transparent; }
</style>
@endsection

@section('page-content')
@php $active_menu = 'financial'; @endphp

{{-- ===== TOP STATS CARDS ===== --}}
<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
    <div class="col-md-4">
        <div class="card card-flush h-md-100 shadow-sm" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
            <div class="card-header pt-5">
                <div class="card-title d-flex flex-column">
                    <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">{{ number_format($total_collected, 2) }} <small class="fs-4">ILS</small></span>
                    <span class="text-white opacity-75 pt-1 fw-semibold fs-6">إجمالي المحصل (المؤكد)</span>
                </div>
            </div>
            <div class="card-body d-flex align-items-end pt-0">
                <i class="bi bi-cash-stack text-white fs-3hx opacity-25"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-flush h-md-100 shadow-sm" style="background: linear-gradient(135deg, #7f1d1d 0%, #ef4444 100%);">
            <div class="card-header pt-5">
                <div class="card-title d-flex flex-column">
                    <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">{{ number_format($total_remaining, 2) }} <small class="fs-4">ILS</small></span>
                    <span class="text-white opacity-75 pt-1 fw-semibold fs-6">إجمالي المستحقات (المتبقي)</span>
                </div>
            </div>
            <div class="card-body d-flex align-items-end pt-0">
                <i class="bi bi-exclamation-triangle text-white fs-3hx opacity-25"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-flush h-md-100 shadow-sm" style="background: linear-gradient(135deg, #065f46 0%, #10b981 100%);">
            <div class="card-header pt-5">
                <div class="card-title d-flex flex-column">
                    <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">{{ number_format($pending_amount, 2) }} <small class="fs-4">ILS</small></span>
                    <span class="text-white opacity-75 pt-1 fw-semibold fs-6">مبالغ قيد التدقيق</span>
                </div>
            </div>
            <div class="card-body d-flex align-items-end pt-0">
                <i class="bi bi-clock-history text-white fs-3hx opacity-25"></i>
            </div>
        </div>
    </div>
</div>

{{-- ===== LEDGER TABLE ===== --}}
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <style>
                /* Ensure invoice filters stay on a single horizontal row and align vertically */
                .invoice-filters { display:flex; flex-wrap:nowrap; gap:0.5rem; align-items:center; overflow-x:auto; }
                .invoice-filters .form-control, .invoice-filters .form-select { height:36px; min-height:36px; }
                .invoice-filters label.form-check { height:36px; display:flex; align-items:center; }
                .invoice-filters .form-check .form-check-input { margin-top:0; }
                @media (max-width: 720px) { .invoice-filters { gap:0.4rem; } }
            </style>
            <div class="d-flex invoice-filters" style="overflow-x:auto;">
                <div class="d-flex align-items-center position-relative flex-shrink-0">
                    <i class="ki-duotone ki-magnifier fs-4 position-absolute ms-4"><span class="path1"></span><span class="path2"></span></i>
                    <input type="text" id="invoice_search" class="form-control form-control-solid form-control-sm ps-11" style="width:180px; min-width:120px;" placeholder="بحث..." />
                </div>
                <select id="invoice_program_type" class="form-select form-select-solid form-select-sm flex-shrink-0" style="width:130px; min-width:100px;">
                    <option value="">النوع</option>
                    <option value="adult">كبار</option>
                    <option value="kids">أطفال</option>
                </select>
                <select id="invoice_program_id" class="form-select form-select-solid form-select-sm flex-shrink-0" style="width:160px; min-width:120px;">
                    <option value="">البرنامج</option>
                    @foreach($programs_filter ?? [] as $p)
                        <option value="{{ $p->id }}">{{ $p->title }}</option>
                    @endforeach
                </select>
                <select id="invoice_level" class="form-select form-select-solid form-select-sm flex-shrink-0" style="width:120px; min-width:100px;">
                    <option value="">المستوى</option>
                    @foreach($levels_filter ?? [] as $lv)
                        <option value="{{ $lv }}">{{ $lv }}</option>
                    @endforeach
                </select>
                <label class="form-check form-switch form-check-custom form-check-solid bg-light-danger px-2 py-1 rounded border border-danger border-dashed flex-shrink-0 mb-0" style="height:33px;">
                    <input class="form-check-input" type="checkbox" id="invoice_only_outstanding" value="1">
                    <span class="ms-2 fw-bold text-danger" style="font-size:0.78rem;"><i class="bi bi-exclamation-circle me-1"></i> مستحقات</span>
                </label>
            </div>
        </div>
        <div class="card-toolbar">
            <button type="button" class="btn btn-light-primary" onclick="window.print()">
                <i class="ki-duotone ki-exit-up fs-2"><span class="path1"></span><span class="path2"></span></i>تصدير / طباعة
            </button>
        </div>
    </div>
    <div class="card-body pt-0">
        <div class="table-responsive">
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_invoices_table">
            <thead>
                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                    <th class="min-w-175px">الطالب / النوع</th>
                    <th class="min-w-150px">البرنامج / المستوى</th>
                    <th class="min-w-100px">المسدد (مؤكد)</th>
                    <th class="min-w-100px">المتبقي</th>
                    <th class="min-w-100px">حالة التدقيق</th>
                    <th class="min-w-130px">التاريخ / أكّدها</th>
                    <th class="text-end min-w-70px">العمليات</th>
                </tr>
            </thead>
            <tbody class="fw-semibold text-gray-600">
                {{-- Loaded via DataTable --}}
            </tbody>
        </table>
        </div>
    </div>
</div>
@stop

@section('modal')
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

    <!-- All Student Invoices Modal -->
    <div class="modal fade" id="all_invoices_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable mw-1000px">
            <div class="modal-content">
                <div class="modal-header py-3 d-flex align-items-center justify-content-between">
                    <h5 class="modal-title fw-bold mb-0"><i class="bi bi-receipt-cutoff text-warning me-2"></i>كل الفواتير</h5>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="إغلاق">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body p-0" id="all_invoices_content">
                    <div class="text-center py-10">
                        <span class="spinner-border w-50px h-50px text-warning" role="status"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Record Payment Modal -->
    <div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h5 class="modal-title mb-0">تسديد دفعة مالية</h5>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="إغلاق">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <form id="recordPaymentForm">
                    @csrf
                    <input type="hidden" name="id" id="record_id">
                    <div class="modal-body">
                        <div class="mb-5">
                            <label class="form-label">المبلغ المتبقي الحالي:</label>
                            <input type="text" id="remaining_display" class="form-control bg-light" readonly>
                        </div>
                        <div class="mb-5">
                            <label class="form-label fw-bold">المبلغ المدفوع الآن:</label>
                            <input type="number" name="amount" id="pay_amount" class="form-control border-success fs-4 fw-bold" required min="1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success">تأكيد الدفع</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        var table = $('#kt_invoices_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.financial.invoices.list') }}",
                data: function(d) {
                    d.program_type     = $('#invoice_program_type').val();
                    d.program_id       = $('#invoice_program_id').val();
                    d.level            = $('#invoice_level').val();
                    d.only_outstanding = $('#invoice_only_outstanding').is(':checked') ? 1 : 0;
                    d.search_text      = $('#invoice_search').val();
                }
            },
            columns: [
                { data: 'student', name: 'students.name' },
                { data: 'program_level', name: 'group.name', orderable: false },
                { data: 'admin_verified_amount', name: 'admin_verified_amount' },
                { data: 'remaining_amount', name: 'remaining_amount' },
                { data: 'audit_status', name: 'audit_status' },
                { data: 'date_info', name: 'group_students_fees.created_at', searchable: false },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' },
            ],
            // Tint rows by transaction type so credit/refund are obvious in the list
            createdRow: function (row, data) {
                const t = (data && data.tx_type_class) || 'payment';
                $(row).addClass('row-' + t);
            },
            order: [[5, 'desc']], // newest first by created_at (date / confirmed-by column)
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/ar.json"
            }
        });

        $('#invoice_search').on('keyup', function() {
            table.draw();
        });

        $('#invoice_program_type, #invoice_program_id, #invoice_level').on('change', function() { table.draw(); });
        $('#invoice_only_outstanding').on('change', function() { table.draw(); });

        // Record Payment Logic
        window.recordPayment = function(id, remaining) {
            $('#record_id').val(id);
            $('#remaining_display').val(remaining + ' ILS');
            $('#pay_amount').val(remaining).attr('max', remaining);
            $('#recordPaymentModal').modal('show');
        };

        $('#recordPaymentForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            btn.attr('disabled', true).text('جاري المعالجة...');

            $.ajax({
                url: '{{ route("admin.financial.record_payment") }}',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $('#recordPaymentModal').modal('hide');
                    if (response.status === 'success') {
                        Swal.fire('تم!', response.message, 'success').then(() => {
                            location.reload(); // Reload to update stat cards
                        });
                    } else {
                        Swal.fire('خطأ!', response.message, 'error');
                    }
                    btn.attr('disabled', false).text('تأكيد الدفع');
                },
                error: function() {
                    btn.attr('disabled', false).text('تأكيد الدفع');
                    Swal.fire('خطأ!', 'حدث خطأ أثناء الاتصال بالسيرفر', 'error');
                }
            });
        });

        // Details Modal Logic
        window.showStudentModal = function(id) {
            fetchModalContent('{{ route('students.details') }}', { id: id });
        }

        // All Invoices Modal — opens per-student detailed ledger across every program/test
        window.showAllInvoices = function(studentId) {
            const url = '{{ url('admin/financial/invoices/student') }}/' + studentId;
            $('#all_invoices_content').html('<div class="text-center py-10"><span class="spinner-border w-50px h-50px text-warning" role="status"></span></div>');
            $('#all_invoices_modal').modal('show');
            $.ajax({
                url: url,
                type: 'GET',
                success: function(html) {
                    $('#all_invoices_content').html(html);
                },
                error: function() {
                    $('#all_invoices_content').html('<div class="alert alert-danger m-4">تعذّر تحميل بيانات الفواتير</div>');
                }
            });
        };

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
    });
</script>
@stop
