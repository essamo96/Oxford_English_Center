@extends('admin.layout.master')

@section('title', 'السجل المالي للطالب')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">الإدارة المالية</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">السجل المالي</li>
@stop

@section('page-content')
<div class="d-flex flex-column flex-xl-row">
    {{-- Sidebar: Student Info --}}
    <div class="flex-column flex-lg-row-auto w-100 w-xl-350px mb-10">
        <div class="card mb-5 mb-xl-8 shadow-sm">
            <div class="card-body pt-15">
                <div class="d-flex flex-center flex-column mb-5">
                    <div class="symbol symbol-100px symbol-circle mb-7">
                        <img src="{{ asset('uploads/' . ($student->img ?? 'default-avatar.png')) }}" alt="image" />
                    </div>
                    <a href="#" class="fs-3 text-gray-800 text-hover-primary fw-bold mb-1">{{ $student->name }}</a>
                    <div class="fs-5 fw-semibold text-muted mb-6">{{ $student->email }}</div>
                    <div class="d-flex flex-wrap flex-center">
                        <div class="border border-gray-300 border-dashed rounded py-3 px-3 mb-3 text-center min-w-100px">
                            <div class="fs-4 fw-bold text-gray-700">{{ number_format($ledger['total_fee'], 2) }}</div>
                            <div class="fw-semibold text-muted">إجمالي الرسوم</div>
                        </div>
                        <div class="border border-gray-300 border-dashed rounded py-3 px-3 mx-4 mb-3 text-center min-w-100px">
                            <div class="fs-4 fw-bold text-success">{{ number_format($ledger['total_paid'], 2) }}</div>
                            <div class="fw-semibold text-muted">المسدد</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-stack fs-4 py-3">
                    <div class="fw-bold rotate collapsible" data-bs-toggle="collapse" href="#kt_user_view_details" role="button" aria-expanded="false" aria-controls="kt_user_view_details">بيانات البرنامج
                    <span class="ms-2 rotate-180">
                        <i class="ki-duotone ki-down fs-3"></i>
                    </span></div>
                </div>
                <div class="separator separator-dashed my-3"></div>
                <div id="kt_user_view_details" class="collapse show">
                    <div class="pb-5 fs-6">
                        <div class="fw-bold mt-5 text-gray-600">البرنامج</div>
                        <div class="text-gray-600">{{ $ledger['group_student']?->group?->program?->title ?? 'N/A' }}</div>
                        <div class="fw-bold mt-5 text-gray-600">المستوى / المجموعة</div>
                        <div class="text-gray-600">{{ $ledger['group_student']?->group?->name ?? 'N/A' }}</div>
                        <div class="fw-bold mt-5 text-gray-600">تاريخ البدء</div>
                        <div class="text-gray-600">{{ $ledger['group_student'] ? $ledger['group_student']->created_at->format('Y-m-d') : 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card shadow-sm {{ $ledger['remaining_balance'] > 0 ? 'bg-light-danger' : 'bg-light-success' }}">
            <div class="card-body text-center">
                <h3 class="fw-bold text-gray-800 mb-2">الرصيد المتبقي</h3>
                <div class="fs-1 fw-bold text-{{ $ledger['remaining_balance'] > 0 ? 'danger' : 'success' }}">{{ number_format($ledger['remaining_balance'], 2) }} <small>ILS</small></div>
            </div>
        </div>
    </div>

    {{-- Main Content: Timeline Ledger --}}
    <div class="flex-lg-row-fluid ms-lg-15">
        <div class="card shadow-sm">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <h3 class="fw-bold">التسلسل الزمني للعمليات المالية</h3>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="timeline-label">
                    @foreach($ledger['transactions'] as $tx)
                        <div class="timeline-item">
                            <div class="timeline-label fw-bold text-gray-800 fs-6 w-100px">{{ $tx->created_at->format('Y-m-d') }}</div>
                            <div class="timeline-badge">
                                <i class="fa fa-genderless text-{{ $tx->audit_status == 'verified' ? ($tx->transaction_type == 'refund' ? 'danger' : 'success') : 'warning' }} fs-1"></i>
                            </div>
                            <div class="fw-mormal timeline-content ps-3">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="fs-5 fw-bold text-gray-800 me-2">
                                        {{ $tx->transaction_type == 'payment' ? 'تحصيل دفعة' : ($tx->transaction_type == 'refund' ? 'إرجاع مبلغ' : 'تعديل مالي') }}
                                    </span>
                                    <span class="badge badge-light-{{ $tx->audit_status == 'verified' ? 'success' : 'warning' }} fs-8 fw-bold">
                                        {{ $tx->audit_status == 'verified' ? 'مؤكد' : 'قيد التدقيق' }}
                                    </span>
                                </div>
                                <div class="fs-6 text-gray-600 mb-2">
                                    المبلغ: <span class="fw-bold {{ $tx->transaction_type == 'refund' ? 'text-danger' : 'text-success' }}">
                                        {{ $tx->transaction_type == 'refund' ? '-' : '+' }}{{ number_format($tx->transaction_amount, 2) }} ILS
                                    </span>
                                    @if($tx->paymentMethod)
                                        <span class="text-muted ms-2">({{ $tx->paymentMethod->name }})</span>
                                    @endif
                                </div>
                                <div class="text-muted fs-7">{{ $tx->notes ?? 'لا توجد ملاحظات' }}</div>
                            </div>
                        </div>
                    @endforeach
                    
                    {{-- Initial Invoice Item --}}
                    @if($ledger['group_student'])
                    <div class="timeline-item">
                        <div class="timeline-label fw-bold text-gray-800 fs-6 w-100px">{{ $ledger['group_student']->created_at->format('Y-m-d') }}</div>
                        <div class="timeline-badge">
                            <i class="fa fa-genderless text-info fs-1"></i>
                        </div>
                        <div class="fw-mormal timeline-content ps-3">
                            <div class="fs-5 fw-bold text-gray-800 mb-1">إنشاء المطالبة المالية (الفاتورة)</div>
                            <div class="fs-6 text-gray-600">رسوم المستوى: {{ number_format($ledger['total_fee'], 2) }} ILS</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@stop
