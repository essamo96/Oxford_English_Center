@extends('admin.layout.master')

@section('title', 'إعدادات أنواع الرسوم')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">الإدارة المالية</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">إعدادات أنواع الرسوم</li>
@stop

@section('page-content')
@php $active_menu = 'financial_fees'; @endphp

{{-- Flash Messages --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-5" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
    {{-- ===== LEFT: Add Fee Form ===== --}}
    <div class="col-xl-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary">
                <h3 class="card-title text-white">
                    <i class="bi bi-plus-circle-fill me-2 text-warning"></i>
                    إضافة / تحديث رسوم
                </h3>
            </div>
            <div class="card-body">
                @include('admin.layout.masterLayouts.error')
                <form action="{{ route('admin.financial.fees.update') }}" method="POST">
                    @csrf

                    {{-- Program --}}
                    <div class="mb-5">
                        <label class="form-label fw-bold required">البرنامج</label>
                        <select name="program_id" id="programSelect" class="form-select" required
                                onchange="loadGroupsByProgram(this.value); loadProgramMinPayment(this.value);"
                                data-programs="{{ json_encode($programs->map(fn($p) => ['id' => $p->id, 'min_payment_percent' => $p->min_payment_percent, 'min_payment_fixed' => $p->min_payment_fixed])) }}">
                            <option value="">-- اختر البرنامج --</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->id }}">{{ $program->title }}</option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted">اختر البرنامج الدراسي أولاً.</div>
                    </div>

                    {{-- Level / Group --}}
                    <div class="mb-5">
                        <label class="form-label fw-bold">المستوى (المجموعة) — اختياري</label>
                        <select name="level_name" id="levelSelect" class="form-select">
                            <option value="">-- رسوم عامة لكل المستويات --</option>
                        </select>
                        <div class="form-text text-muted">إذا اخترت مستوى، ستكون الرسوم خاصة به فقط.</div>
                    </div>

                    {{-- Fee Type --}}
                    <div class="mb-5">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold required mb-0">نوع الرسوم</label>
                            <button type="button" class="btn btn-sm btn-light-primary py-1 px-2 fs-8" data-bs-toggle="modal" data-bs-target="#manageTypesModal">
                                <i class="bi bi-gear-fill fs-9"></i> إدارة الأنواع
                            </button>
                        </div>
                        <select name="type" id="feeTypeSelect" class="form-select" required>
                            @foreach($fee_types as $ft)
                                <option value="{{ $ft->slug }}">{{ $ft->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Amount --}}
                    <div class="mb-5">
                        <label class="form-label fw-bold required">المبلغ</label>
                        <div class="input-group">
                            <input type="number" name="amount" class="form-control" placeholder="0.00" step="0.01" min="0" required>
                            <span class="input-group-text fw-bold">ILS</span>
                        </div>
                    </div>

                    {{-- Minimum payment controls (program-level) --}}
                    <div class="mb-5 p-4 rounded bg-light-warning border border-warning border-opacity-25">
                        <label class="form-label fw-bold mb-2">
                            <i class="bi bi-shield-check text-warning me-1"></i>
                            الحد الأدنى للدفعة الأولى — على مستوى البرنامج
                        </label>
                        <div class="text-muted fs-8 mb-3">
                            هذا الإعداد مرتبط بالبرنامج ككل وليس ببند رسوم بعينه. الحد الأدنى الفعلي هو الأعلى بين النسبة والمبلغ الثابت.
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fs-8 fw-bold">نسبة % من إجمالي الرسوم</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="min_payment_percent" class="form-control" placeholder="مثال: 30" step="0.01" min="0" max="100">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-8 fw-bold">مبلغ ثابت</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="min_payment_fixed" class="form-control" placeholder="مثال: 200" step="0.01" min="0">
                                    <span class="input-group-text">ILS</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-save2-fill me-2"></i> حفظ الإعدادات
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ===== RIGHT: Fees Table ===== --}}
    <div class="col-xl-8">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-table me-2 text-primary"></i>
                    قائمة أنواع الرسوم المعتمدة
                </h3>
                <div class="card-toolbar">
                    <span class="badge badge-light-primary fs-7 fw-bold">{{ $fees->count() }} نوع</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-5 gy-4 mb-0">
                        <thead class="bg-light">
                            <tr class="fw-bold text-muted text-uppercase fs-7">
                                <th class="min-w-150px">البرنامج</th>
                                <th class="min-w-120px">المستوى</th>
                                <th class="min-w-120px">نوع الرسوم</th>
                                <th class="min-w-80px text-center">المبلغ</th>
                                <th class="min-w-120px text-center">الحد الأدنى للدفعة</th>
                                <th class="min-w-60px text-center">العمليات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fees as $fee)
                                <tr>
                                    {{-- Program --}}
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-40px me-3">
                                                <span class="symbol-label bg-light-primary">
                                                    <i class="bi bi-book-fill text-primary fs-4"></i>
                                                </span>
                                            </div>
                                            <span class="text-dark fw-bold text-hover-primary fs-6">
                                                {{ $fee->program->title ?? '—' }}
                                            </span>
                                        </div>
                                    </td>
                                    {{-- Level --}}
                                    <td>
                                        @if($fee->level_name)
                                            <span class="badge badge-light-warning fw-bold">{{ $fee->level_name }}</span>
                                        @else
                                            <span class="text-muted fst-italic fs-7">جميع المستويات</span>
                                        @endif
                                    </td>
                                    {{-- Type --}}
                                    <td>
                                        @php
                                            $ft = $fee_types->where('slug', $fee->type)->first();
                                            $label = $ft ? $ft->name_ar : $fee->type;
                                            $class = $ft ? $ft->class : 'badge-light-secondary';
                                            $icon = $ft ? $ft->icon : 'bi-tag';
                                        @endphp
                                        <span class="badge {{ $class }} fw-bold">
                                            <i class="bi {{ $icon }} me-1"></i>{{ $label }}
                                        </span>
                                    </td>
                                    {{-- Amount --}}
                                    <td class="text-center">
                                        <span class="text-dark fw-bold fs-5">{{ number_format($fee->amount, 2) }}</span>
                                        <span class="text-muted fs-8 ms-1">ILS</span>
                                    </td>
                                    {{-- Minimum payment (program-level) --}}
                                    <td class="text-center">
                                        @php
                                            $pct   = optional($fee->program)->min_payment_percent;
                                            $fixed = optional($fee->program)->min_payment_fixed;
                                        @endphp
                                        @if(is_null($pct) && is_null($fixed))
                                            <span class="text-muted fs-8 fst-italic">— غير محدد —</span>
                                        @else
                                            @if(!is_null($pct))
                                                <span class="badge badge-light-info fw-bold me-1"><i class="bi bi-percent"></i> {{ rtrim(rtrim(number_format($pct, 2), '0'), '.') }}%</span>
                                            @endif
                                            @if(!is_null($fixed))
                                                <span class="badge badge-light-warning fw-bold">{{ number_format($fixed, 2) }} ILS</span>
                                            @endif
                                            <div class="fs-9 text-muted mt-1">على مستوى البرنامج</div>
                                        @endif
                                    </td>
                                    {{-- Actions --}}
                                    <td class="text-center">
                                        <form action="{{ route('admin.financial.fees.delete', $fee->id) }}" method="POST"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذه الرسوم؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon btn-light-danger"
                                                    title="حذف">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-10">
                                        <i class="bi bi-inbox fs-1 d-block mb-3 text-muted opacity-25"></i>
                                        لا توجد أنواع رسوم مضافة حالياً.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===== MODAL: Manage Fee Types ===== --}}
<div class="modal fade" id="manageTypesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إدارة مسميات أنواع الرسوم</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Add/Edit Form --}}
                <div class="bg-light p-4 rounded mb-5">
                    <h6 class="fw-bold mb-3">إضافة / تعديل مسمى</h6>
                    <form id="typeForm" class="row g-3">
                        <input type="hidden" id="type_id" name="id">
                        <div class="col-md-5">
                            <label class="form-label fs-8 fw-bold">الاسم بالعربية</label>
                            <input type="text" id="name_ar" name="name_ar" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fs-8 fw-bold">المعرف (Slug)</label>
                            <input type="text" id="slug" name="slug" class="form-control form-control-sm" required placeholder="مثلاً: registration">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" onclick="saveFeeType()" class="btn btn-sm btn-primary w-100">حفظ المسمى</button>
                        </div>
                    </form>
                </div>

                {{-- Types List --}}
                <div class="table-responsive">
                    <table class="table table-sm table-row-bordered align-middle gs-3">
                        <thead>
                            <tr class="fw-bold text-muted fs-8">
                                <th>المسمى</th>
                                <th>المعرف</th>
                                <th class="text-center">العمليات</th>
                            </tr>
                        </thead>
                        <tbody id="typesTableBody">
                            {{-- AJAX Content --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('js')
<script>
function loadGroupsByProgram(programId) {
    const select = document.getElementById('levelSelect');
    select.innerHTML = '<option value="">جاري التحميل...</option>';

    if (!programId) {
        select.innerHTML = '<option value="">-- رسوم عامة لكل المستويات --</option>';
        return;
    }

    fetch(`/admin/financial/fees/groups/${programId}`)
        .then(r => r.json())
        .then(groups => {
            select.innerHTML = '<option value="">-- رسوم عامة لكل المستويات --</option>';
            groups.forEach(g => {
                const opt = document.createElement('option');
                opt.value = g.level;
                opt.textContent = g.level;
                select.appendChild(opt);
            });
        })
        .catch(() => {
            select.innerHTML = '<option value="">-- رسوم عامة لكل المستويات --</option>';
        });
}

function loadProgramMinPayment(programId) {
    const pctInput   = document.querySelector('input[name="min_payment_percent"]');
    const fixedInput = document.querySelector('input[name="min_payment_fixed"]');
    if (!pctInput || !fixedInput) return;

    if (!programId) {
        pctInput.value = '';
        fixedInput.value = '';
        return;
    }

    fetch(`/api/program-min-payment/${programId}`, { headers: { 'Accept': 'application/json' } })
        .then(r => r.ok ? r.json() : null)
        .then(data => {
            if (!data || !data.success) return;
            pctInput.value   = data.min_payment_percent !== null ? data.min_payment_percent : '';
            fixedInput.value = data.min_payment_fixed   !== null ? data.min_payment_fixed   : '';
        })
        .catch(() => {});
}

function refreshTypesTable() {
    fetch('{{ route("admin.financial.fee_types.list") }}')
        .then(r => r.json())
        .then(types => {
            const body = document.getElementById('typesTableBody');
            const mainSelect = document.getElementById('feeTypeSelect');
            body.innerHTML = '';
            mainSelect.innerHTML = '';

            types.forEach(t => {
                // Table row
                body.innerHTML += `
                    <tr>
                        <td><span class="badge ${t.class}"><i class="bi ${t.icon} me-1"></i> ${t.name_ar}</span></td>
                        <td><code>${t.slug}</code></td>
                        <td class="text-center">
                            <button onclick="editType(${JSON.stringify(t).replace(/"/g, '&quot;')})" class="btn btn-sm btn-icon btn-light-primary"><i class="bi bi-pencil-fill"></i></button>
                            <button onclick="deleteType(${t.id})" class="btn btn-sm btn-icon btn-light-danger"><i class="bi bi-trash-fill"></i></button>
                        </td>
                    </tr>
                `;
                // Main form select
                mainSelect.innerHTML += `<option value="${t.slug}">${t.name_ar}</option>`;
            });
        });
}

function editType(t) {
    document.getElementById('type_id').value = t.id;
    document.getElementById('name_ar').value = t.name_ar;
    document.getElementById('slug').value = t.slug;
}

function saveFeeType() {
    const data = new FormData(document.getElementById('typeForm'));
    fetch('{{ route("admin.financial.fee_types.store") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: data
    })
    .then(r => r.json())
    .then(res => {
        if(res.status === 'success') {
            document.getElementById('typeForm').reset();
            document.getElementById('type_id').value = '';
            refreshTypesTable();
            Swal.fire('تم!', res.message, 'success');
        } else {
            Swal.fire('خطأ', res.message, 'error');
        }
    });
}

function deleteType(id) {
    if(!confirm('هل أنت متأكد من حذف هذا النوع؟')) return;
    fetch(`/admin/financial/fee-types/delete/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(res => {
        if(res.status === 'success') {
            refreshTypesTable();
        } else {
            Swal.fire('عذراً', res.message, 'warning');
        }
    });
}

// Initial load for types table
document.addEventListener('DOMContentLoaded', refreshTypesTable);
</script>
@stop
