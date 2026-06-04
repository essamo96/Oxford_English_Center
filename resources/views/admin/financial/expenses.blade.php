@extends('admin.layout.master')

@section('title', 'المصروفات')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted"><a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a></li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark">المصروفات</li>
@stop

@section('page-content')
@php
    $monthsAr = [1=>'يناير',2=>'فبراير',3=>'مارس',4=>'أبريل',5=>'مايو',6=>'يونيو',7=>'يوليو',8=>'أغسطس',9=>'سبتمبر',10=>'أكتوبر',11=>'نوفمبر',12=>'ديسمبر'];
    $daysAr = ['Saturday'=>'السبت','Sunday'=>'الأحد','Monday'=>'الاثنين','Tuesday'=>'الثلاثاء','Wednesday'=>'الأربعاء','Thursday'=>'الخميس','Friday'=>'الجمعة'];
@endphp

<div class="card shadow-sm mb-5">
    <div class="card-header border-0 pt-6">
        <div class="card-title"><h3 class="fw-bold"><i class="ki-duotone ki-wallet fs-1 text-danger me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>المصروفات</h3></div>
        <div class="card-toolbar">
            <form method="get" class="d-flex gap-2 align-items-center flex-wrap">
                <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control form-control-sm" style="width:180px;" placeholder="🔍 بحث في البيان/الملاحظات">
                <select name="month" class="form-select form-select-sm" style="width:130px;">
                    @foreach($monthsAr as $m => $label)
                        <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="year" class="form-select form-select-sm" style="width:110px;">
                    @for($y = now()->year; $y >= now()->year - 4; $y--)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button class="btn btn-sm btn-light-primary"><i class="ki-duotone ki-magnifier fs-4"><span class="path1"></span><span class="path2"></span></i> عرض</button>
            </form>
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.masterLayouts.error')
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div class="d-flex gap-3 flex-wrap">
                <span class="badge badge-light-primary fs-7 p-3">الفترة: {{ $monthsAr[$month] }} {{ $year }}</span>
                <span class="badge badge-light-danger fs-7 p-3">إجمالي مصروفات الشهر: {{ number_format($month_total, 2) }} ILS</span>
                <span class="badge badge-light-dark fs-7 p-3">إجمالي كل المصروفات: {{ number_format($grand_total, 2) }} ILS</span>
            </div>
            <button type="button" class="btn btn-sm btn-danger" onclick="openExpenseModal()">
                <i class="ki-duotone ki-plus fs-3"></i> تسجيل مصروف جديد
            </button>
        </div>

        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-4 text-center">
                <thead>
                    <tr class="text-muted fw-bold fs-7 text-uppercase">
                        <th class="text-start">تاريخ الصرف</th>
                        <th>اليوم</th>
                        <th class="text-start">بيان الصرف</th>
                        <th>المبلغ</th>
                        <th class="text-start">الملاحظات</th>
                        <th>سجّله</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold text-gray-700">
                    @forelse($expenses as $e)
                        <tr>
                            <td class="text-start fw-bold">{{ $e->expense_date->format('Y-m-d') }}</td>
                            <td><span class="badge badge-light-info">{{ $daysAr[$e->expense_date->format('l')] ?? $e->expense_date->format('l') }}</span></td>
                            <td class="text-start">{{ $e->statement }}</td>
                            <td class="fw-bold text-danger">{{ number_format($e->amount, 2) }} ILS</td>
                            <td class="text-start text-muted">{{ $e->notes ?: '—' }}</td>
                            <td class="fs-8">{{ optional($e->createdBy)->name ?? '—' }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <button class="btn btn-sm btn-icon btn-light-warning" title="تعديل"
                                            onclick="editExpense(this)"
                                            data-id="{{ $e->id }}"
                                            data-date="{{ $e->expense_date->format('Y-m-d') }}"
                                            data-statement="{{ $e->statement }}"
                                            data-amount="{{ $e->amount }}"
                                            data-notes="{{ $e->notes }}">
                                        <i class="ki-duotone ki-pencil fs-5"><span class="path1"></span><span class="path2"></span></i>
                                    </button>
                                    <button class="btn btn-sm btn-icon btn-light-danger" title="حذف"
                                            onclick="deleteExpense({{ $e->id }})">
                                        <i class="ki-duotone ki-trash fs-5"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-muted py-6">لا توجد مصروفات مسجّلة لهذه الفترة.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add / Edit modal --}}
<div class="modal fade" id="expenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ki-duotone ki-wallet fs-2 text-danger me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i><span id="expenseModalTitle">تسجيل مصروف جديد</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="expenseForm" method="post" action="{{ route('admin.financial.expenses.store') }}">
                @csrf
                <input type="hidden" name="_method" id="ex_method" value="POST">
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">تاريخ الصرف</label>
                            <input type="date" name="expense_date" id="ex_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">المبلغ (ILS)</label>
                            <input type="number" step="0.01" min="0" name="amount" id="ex_amount" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold required">بيان الصرف</label>
                            <input type="text" name="statement" id="ex_statement" class="form-control" maxlength="255" placeholder="مثال: فاتورة كهرباء، صيانة، قرطاسية..." required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">ملاحظات الصرف</label>
                            <textarea name="notes" id="ex_notes" class="form-control" rows="3" maxlength="2000"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger"><i class="ki-duotone ki-check fs-3"></i> حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    var STORE_URL = '{{ route('admin.financial.expenses.store') }}';
    var UPDATE_BASE = '{{ url('admin/financial/expenses') }}';
    var CSRF = '{{ csrf_token() }}';

    window.openExpenseModal = function () {
        document.getElementById('expenseForm').reset();
        document.getElementById('expenseModalTitle').textContent = 'تسجيل مصروف جديد';
        document.getElementById('expenseForm').action = STORE_URL;
        document.getElementById('ex_method').value = 'POST';
        document.getElementById('ex_date').value = '{{ now()->format('Y-m-d') }}';
        new bootstrap.Modal(document.getElementById('expenseModal')).show();
    };

    window.editExpense = function (el) {
        var d = el.dataset;
        document.getElementById('expenseModalTitle').textContent = 'تعديل مصروف';
        document.getElementById('expenseForm').action = UPDATE_BASE + '/' + d.id;
        document.getElementById('ex_method').value = 'POST'; // route is POST update
        document.getElementById('ex_date').value = d.date;
        document.getElementById('ex_amount').value = d.amount;
        document.getElementById('ex_statement').value = d.statement;
        document.getElementById('ex_notes').value = d.notes || '';
        new bootstrap.Modal(document.getElementById('expenseModal')).show();
    };

    window.deleteExpense = function (id) {
        Swal.fire({
            title: 'حذف المصروف؟',
            text: 'لا يمكن التراجع عن هذا الإجراء.',
            icon: 'warning', showCancelButton: true,
            confirmButtonText: 'نعم، احذف', cancelButtonText: 'إلغاء', confirmButtonColor: '#d33',
        }).then(function (r) {
            if (!r.isConfirmed) return;
            var f = document.createElement('form');
            f.method = 'post';
            f.action = UPDATE_BASE + '/' + id;
            f.innerHTML = '<input type="hidden" name="_token" value="' + CSRF + '">' +
                          '<input type="hidden" name="_method" value="DELETE">';
            document.body.appendChild(f);
            f.submit();
        });
    };
</script>
@stop
