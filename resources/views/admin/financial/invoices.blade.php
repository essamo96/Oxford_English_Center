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

@section('page-content')
@php $active_menu = 'financial_invoices'; @endphp

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
            <div class="d-flex align-items-center position-relative my-1 me-5">
                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5"><span class="path1"></span><span class="path2"></span></i>
                <input type="text" id="invoice_search" class="form-control form-control-solid w-250px ps-13" placeholder="بحث عن طالب أو فاتورة..." />
            </div>
            <div class="d-flex align-items-center position-relative my-1">
                <select id="invoice_program_type" class="form-select form-select-solid w-150px">
                    <option value="">نوع البرنامج (الكل)</option>
                    <option value="adult">الكبار (Adult)</option>
                    <option value="kids">الأطفال (Kids)</option>
                </select>
            </div>
        </div>
        <div class="card-toolbar">
            <button type="button" class="btn btn-light-primary" onclick="window.print()">
                <i class="ki-duotone ki-exit-up fs-2"><span class="path1"></span><span class="path2"></span></i>تصدير / طباعة
            </button>
        </div>
    </div>
    <div class="card-body pt-0">
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_invoices_table">
            <thead>
                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                    <th class="min-w-150px">الطالب</th>
                    <th class="min-w-150px">البرنامج / المستوى</th>
                    <th class="min-w-100px">النوع</th>
                    <th class="min-w-100px">المسدد (مؤكد)</th>
                    <th class="min-w-100px">المتبقي</th>
                    <th class="min-w-100px">حالة التدقيق</th>
                    <th class="min-w-100px">التاريخ</th>
                    <th class="text-end min-w-70px">العمليات</th>
                </tr>
            </thead>
            <tbody class="fw-semibold text-gray-600">
                {{-- Loaded via DataTable --}}
            </tbody>
        </table>
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
                <div class="modal-header py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-receipt-cutoff text-warning me-2"></i>كل الفواتير</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                <div class="modal-header">
                    <h5 class="modal-title">تسديد دفعة مالية</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                    d.program_type = $('#invoice_program_type').val();
                    d.search_text = $('#invoice_search').val();
                }
            },
            columns: [
                { data: 'student', name: 'students.name' },
                { data: 'program_level', name: 'group.name', orderable: false },
                { data: 'student_paid_type', name: 'student_paid_type' },
                { data: 'admin_verified_amount', name: 'admin_verified_amount' },
                { data: 'remaining_amount', name: 'remaining_amount' },
                { data: 'audit_status', name: 'audit_status' },
                { data: 'created_at', name: 'created_at', render: function(data) {
                    return moment(data).format('YYYY-MM-DD HH:mm');
                }},
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' },
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Arabic.json"
            }
        });

        $('#invoice_search').on('keyup', function() {
            table.draw();
        });

        $('#invoice_program_type').on('change', function() {
            table.draw();
        });

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
