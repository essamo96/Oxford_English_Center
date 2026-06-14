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

{{-- Nav Tabs --}}
<ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-5 fs-6 fw-bold border-0" id="pendingTabs">
    <li class="nav-item">
        <a class="nav-link active text-gray-800" data-bs-toggle="tab" href="#tab_financial">
            <i class="bi bi-cash-coin me-2 text-success"></i> الطلبات المالية العالقة
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-gray-800" data-bs-toggle="tab" href="#tab_membership">
            <i class="bi bi-person-plus-fill me-2 text-primary"></i> طلبات العضوية الجديدة
            <span class="badge badge-light-primary ms-2" id="membership_count_badge">...</span>
        </a>
    </li>
</ul>

<div class="tab-content">
{{-- Tab 1: Financial Pending Orders --}}
<div class="tab-pane fade show active" id="tab_financial">
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
                @if(!($isBranchScoped ?? false))
                <div class="d-flex align-items-center gap-2 bg-light-primary px-3 py-2 rounded border border-primary border-dashed">
                    <i class="bi bi-geo-fill text-primary fs-7"></i>
                    <select id="branch_id_filter" class="form-select form-select-sm form-select-solid border-0 bg-transparent fw-bold text-primary" style="min-width:150px;" data-control="select2">
                        <option value="">كل الفروع</option>
                        @foreach($allBranches ?? [] as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name_ar }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
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
                    <th class="text-center"> الفرع </th>
                    <th class="text-center"> نوع الطلب </th>
                    <th class="text-center"> البرنامج / المجموعة </th>
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
</div>

{{-- Tab 2: New Membership Requests --}}
<div class="tab-pane fade" id="tab_membership">
<div class="card mb-7 shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1 text-info">
                <i class="ki-duotone ki-magnifier fs-4 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                البحث والفلاتر
            </span>
        </div>
        <div class="card-toolbar">
            <div class="d-flex flex-wrap justify-content-end gap-2 text-nowrap">
                <button type="button" id="mem_todayBtn" class="btn btn-sm btn-light-success">
                    <i class="ki-duotone ki-calendar fs-4 me-1"><span class="path1"></span><span class="path2"></span></i> طلاب اليوم
                </button>
                <button type="button" id="mem_showAllBtn" class="btn btn-sm btn-light-primary">
                    <i class="ki-duotone ki-eye fs-4 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i> عرض الكل
                </button>
                <button type="reset" id="mem_resetFilter" class="btn btn-sm btn-light-danger">
                    <i class="ki-duotone ki-arrows-loop fs-4 me-1"><span class="path1"></span><span class="path2"></span></i> تصفية
                </button>
                <button type="button" class="btn btn-sm btn-success mem_BulkActivate">
                    <i class="ki-duotone ki-user-tick fs-4 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i> تفعيل وإرسال
                </button>
            </div>
        </div>
    </div>
    <div class="card-body py-4">
        <form id="mem_filter_form" class="row g-3 align-items-end">
            <input type="hidden" id="mem_is_today" name="is_today" value="">
            <div class="col-lg-3 col-md-6">
                <label class="form-label fw-semibold fs-7">الاسم / الجوال / الإيميل</label>
                <input type="text" id="mem_search" name="search" class="form-control form-control-solid form-control-sm" placeholder="بحث ...">
            </div>
            <div class="col-lg-2 col-md-3">
                <label class="form-label fw-semibold fs-7">نوع البرنامج</label>
                <select id="mem_program_type" name="program_type" class="form-select form-select-solid form-select-sm" data-control="select2" data-placeholder="-- الكل --">
                    <option value=""></option>
                    <option value="adult">الكبار (Adult)</option>
                    <option value="kids">الأطفال (Kids)</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-3">
                <label class="form-label fw-semibold fs-7">نوع التسجيل</label>
                <select id="mem_enrollment_type" name="enrollment_type" class="form-select form-select-solid form-select-sm" data-control="select2" data-placeholder="-- الكل --">
                    <option value=""></option>
                    <option value="test">اختبار تحديد المستوى</option>
                    <option value="course">تسجيل مباشر</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-6">
                <label class="form-label fw-semibold fs-7">من تاريخ</label>
                <input type="text" id="mem_date_from" name="date_from" class="form-control form-control-solid form-control-sm mem-date-picker" placeholder="اختر ...">
            </div>
            <div class="col-lg-2 col-md-6">
                <label class="form-label fw-semibold fs-7">إلى تاريخ</label>
                <input type="text" id="mem_date_to" name="date_to" class="form-control form-control-solid form-control-sm mem-date-picker" placeholder="اختر ...">
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1 text-primary">
                <i class="bi bi-person-plus-fill fs-4 text-primary me-2"></i>
                طلبات العضوية الجديدة
            </span>
        </div>
        <div class="card-toolbar gap-3">
            @php
                $membershipRouteUrl = \Route::has('dashboard.view.membership')
                    ? route('dashboard.view.membership')
                    : (\Route::has('membership.view') ? route('membership.view') : null);
            @endphp
            @if($membershipRouteUrl)
            <a href="{{ $membershipRouteUrl }}" class="btn btn-light-primary btn-sm">
                <i class="bi bi-box-arrow-up-right me-1"></i> فتح الشاشة الكاملة
            </a>
            @endif
        </div>
    </div>
    <div class="card-body py-4">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center" id="mem_kt_table">
                <thead>
                    <tr class="text-center text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th class="w-50px text-center align-middle"> # </th>
                        <th class="w-50px text-center align-middle">
                            <div class="form-check form-check-custom form-check-solid d-flex justify-content-center">
                                <input class="form-check-input h-20px w-20px" type="checkbox" id="mem_selectAll" />
                            </div>
                        </th>
                        <th class="min-w-250px text-start align-middle"> الطالب </th>
                        <th class="min-w-125px text-center align-middle"> الجوال </th>
                        <th class="min-w-140px text-center align-middle"> تاريخ الميلاد / العمر </th>
                        <th class="min-w-140px text-center align-middle"> تاريخ الانضمام </th>
                        <th class="min-w-100px text-center align-middle"> الحالة </th>
                        <th class="text-center min-w-125px align-middle"> العمليات </th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold text-center align-middle"></tbody>
            </table>
        </div>
    </div>
</div>
</div>
</div>{{-- end tab-content --}}

<!-- Verification Modal -->
<div class="modal fade" id="verifyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تأكيد الدفعة المالية وتفعيل الحساب</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="verifyForm" enctype="multipart/form-data">
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

                    {{-- Prior-payment / credit breakdown — shown when student has existing verified payments --}}
                    {{-- Two modes: (A) actual credit-balance overpayment, (B) completing a partial payment --}}
                    <div id="credit_breakdown_box" class="mb-4 p-4 rounded bg-light-primary border border-primary border-dashed" style="display:none;">
                        <div class="fw-bold text-primary mb-3" id="cb_box_title"><i class="bi bi-piggy-bank-fill me-1"></i> تسوية من الرصيد الدائن</div>
                        <div class="row g-2 fs-8 text-center">
                            <div class="col-4"><div class="p-2 rounded bg-body"><div class="text-muted">إجمالي الرسوم</div><div class="fw-bold text-gray-900" id="cb_total">0</div></div></div>
                            <div class="col-4" id="cb_credit_col"><div class="p-2 rounded bg-body"><div class="text-muted" id="cb_credit_lbl">مخصوم من الرصيد</div><div class="fw-bold text-info" id="cb_credit">0</div></div></div>
                            <div class="col-4" id="cb_min_col"><div class="p-2 rounded bg-body"><div class="text-muted">الحد الأدنى للدفعة</div><div class="fw-bold text-gray-900" id="cb_min">0</div></div></div>
                        </div>
                        <div id="cb_note" class="mb-0 mt-3 py-2 px-3 fs-8 rounded d-flex align-items-start gap-2"></div>
                    </div>

                    <div class="mb-4" id="verified_amount_block">
                        <label class="form-label fw-bold text-success">المبلغ الفعلي المستلم نقدًا (Verified Amount):</label>
                        <input type="number" step="0.01" min="0" name="verified_amount" id="verified_amount" class="form-control border-success fs-4 fw-bold" required>
                        <div id="cash_remaining_hint" class="form-text fw-bold mt-1" style="display:none;"></div>
                    </div>

                    {{-- Payment method + admin-attached payment notice --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold"><i class="bi bi-credit-card me-1 text-primary"></i> طريقة الدفع</label>
                            <select name="payment_method_id" id="verify_payment_method" class="form-select">
                                <option value="">-- اختر طريقة الدفع --</option>
                                @foreach($payment_methods ?? [] as $pm)
                                    <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold"><i class="bi bi-paperclip me-1 text-info"></i> إرفاق إشعار الدفع <small class="text-muted">(اختياري)</small></label>
                            <input type="file" name="payment_receipt" id="verify_payment_receipt" class="form-control" accept="image/*">
                        </div>
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

                    {{-- Shown when the student is ALREADY seated in a group (branched) → program locked --}}
                    <div id="already_seated_notice" class="alert alert-light-success border border-success border-dashed d-flex align-items-center gap-2" style="display:none;">
                        <i class="bi bi-check-circle-fill text-success fs-4"></i>
                        <div>تم تسكين الطالب في المجموعة «<strong id="already_seated_group"></strong>» بالفعل — لا يمكن تغيير البرنامج، فقط أكّد الدفعة.</div>
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

                    {{-- Simple balance (when program unchanged) --}}
                    <div id="balance_preview" class="alert alert-light-info d-none">
                        المتبقي: <span id="remaining_val" class="fw-bold text-danger">0</span> ILS
                    </div>

                    {{-- Smart diff panel (shown when program is changed) --}}
                    <div id="program_swap_panel" class="d-none mt-3" style="border:2px solid #f5a524; border-radius:14px; overflow:hidden;">
                        <div class="px-4 py-2 fw-bold text-white" style="background:linear-gradient(90deg,#f5a524 0%,#ec9211 100%);">
                            <i class="bi bi-arrow-left-right me-1"></i> تحليل فرق السعر بعد تغيير البرنامج
                        </div>
                        <div class="p-4">
                            <div class="row g-3 mb-3 text-center">
                                <div class="col-md-4">
                                    <div class="p-3 rounded" style="background:#e7f5ff;border:1px solid #c5e0ff;">
                                        <div class="fs-8 text-muted fw-bold mb-1">رسوم البرنامج الأصلي</div>
                                        <div class="fs-3 fw-bold text-info" id="swap_original_fee">0.00</div>
                                        <small class="text-muted">ILS</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 rounded" style="background:#fff4d6;border:1px solid #ffcf60;">
                                        <div class="fs-8 text-muted fw-bold mb-1">رسوم البرنامج الجديد</div>
                                        <div class="fs-3 fw-bold" style="color:#a86e00;" id="swap_new_fee">0.00</div>
                                        <small class="text-muted">ILS</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 rounded" id="swap_delta_box" style="background:#f3f4f6;border:1px solid #e5e7eb;">
                                        <div class="fs-8 text-muted fw-bold mb-1">الفرق</div>
                                        <div class="fs-3 fw-bold" id="swap_delta">0.00</div>
                                        <small class="text-muted">ILS</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-3 text-center">
                                <div class="col-md-6">
                                    <div class="p-3 rounded" style="background:#d1fae5;border:1px solid #6ee7b7;">
                                        <div class="fs-8 text-muted fw-bold mb-1"><i class="bi bi-cash-coin me-1"></i> المؤكد دفعه</div>
                                        <div class="fs-3 fw-bold text-success" id="swap_verified">0.00</div>
                                        <small class="text-muted">ILS</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 rounded" id="swap_outstanding_box">
                                        <div class="fs-8 text-muted fw-bold mb-1" id="swap_outstanding_label">المتبقّي على الطالب</div>
                                        <div class="fs-3 fw-bold" id="swap_outstanding">0.00</div>
                                        <small class="text-muted">ILS</small>
                                    </div>
                                </div>
                            </div>

                            <div id="swap_recommendation" class="alert mb-3 d-flex align-items-start gap-2" style="font-size:0.95rem;"></div>

                            {{-- Action options when there's a credit --}}
                            <div id="swap_credit_actions" class="d-none">
                                <label class="form-label fw-bold mb-2"><i class="bi bi-gear-fill me-1 text-warning"></i> ماذا نفعل بالرصيد الزائد؟</label>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="d-flex align-items-start gap-2 p-3 rounded border bg-light" style="cursor:pointer;">
                                            <input type="radio" name="credit_action" value="keep" class="form-check-input mt-1" checked>
                                            <div>
                                                <strong class="d-block">حفظه كرصيد للطالب</strong>
                                                <small class="text-muted">يُحسم تلقائياً من أي رسوم لاحقة (دورات/كتب).</small>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="d-flex align-items-start gap-2 p-3 rounded border bg-light" style="cursor:pointer;">
                                            <input type="radio" name="credit_action" value="refund" class="form-check-input mt-1">
                                            <div>
                                                <strong class="d-block">ردّ المبلغ نقداً</strong>
                                                <small class="text-muted">يُسجَّل سطر استرداد (refund) في السجل المالي.</small>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                {{-- Optional refund-settlement proof image (shown only for the refund option) --}}
                                <div id="refund_receipt_box" class="mt-3 p-3 rounded border border-danger border-dashed bg-light-danger" style="display:none;">
                                    <label class="form-label fw-bold text-danger mb-2">
                                        <i class="bi bi-paperclip me-1"></i> إرفاق صورة إشعار استرداد الرسوم للطالب <small class="text-muted">(اختياري)</small>
                                    </label>
                                    <input type="file" name="refund_receipt" id="refund_receipt" class="form-control" accept="image/*">
                                    <div class="form-text">صورة إشعار/سند تسليم المبلغ المسترد للطالب — تُحفظ في السجل المالي.</div>
                                    <div id="refund_receipt_preview" class="mt-2 text-center"></div>
                                </div>
                            </div>
                        </div>
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

<!-- Receipt Lightbox Modal -->
<div class="modal fade" id="receiptLightbox" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#003366,#002a55);">
                <h5 class="modal-title text-white d-flex align-items-center gap-2 mb-0">
                    <i class="bi bi-receipt-cutoff"></i> إيصال الدفع
                    <span id="receipt_lightbox_student" class="badge badge-light-info ms-2 fs-9"></span>
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <a id="receipt_lightbox_open" href="#" target="_blank" class="btn btn-sm btn-light-primary" title="فتح في تبويب جديد">
                        <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body text-center p-4" style="background:#f4f6fa;min-height:300px;">
                <div id="receipt_lightbox_content" class="d-flex align-items-center justify-content-center"></div>
            </div>
        </div>
    </div>
</div>

@stop

@section('js')
<script>
    var table;
    var tableId = 'pending_financial_table';
    var filterFields = ['#filter_placement_only', '#filter_placement_graded', '#branch_id_filter'];
    // map: front id → backend param
    var filterParamMap = {
        'filter_placement_only': 'placement_test_only',
        'filter_placement_graded': 'placement_graded',
    };
    var columns = [
        { data: 'id', name: 'id', render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
        { data: "student", name: "student.name", className: "d-flex align-items-center" },
        { data: "type", name: "student_paid_type" },
        { data: "program_group", name: "program_group", orderable: false, searchable: false },
        { data: "total_due_amount", name: "total_due_amount", render: function(d){ return '<span class="fw-bold text-dark">'+d+' ILS</span>'; } },
        { data: "student_fee_paid", name: "student_fee_paid", render: function(d){ return '<span class="fw-bold text-success">'+d+' ILS</span>'; } },
        { data: "receipt", name: "receipt", orderable: false, searchable: false },
        { data: "branch_name", name: "branch_name", orderable: false, searchable: false },
        { data: "created_at", name: "created_at" },
        { data: "actions", name: "actions", orderable: false, searchable: false }
    ];

    $(document).ready(function() {
        // State for change-program tracking + receipt preview
        let originalProgramId = null;
        let receiptUrl = null;
        let receiptShown = false;

        // Function to open verification modal
        window.verifyPayment = function(id, claimed, total, programId, studentId, paidType, groupId, paymentMethodId, groupName) {
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
            $('#verify_program_id').prop('disabled', false); // re-enable (may be locked below)
            $('#already_seated_notice').hide();
            $('#program_swap_panel').addClass('d-none');
            $('#swap_credit_actions').addClass('d-none');
            // Reset refund-proof uploader + restore default credit action
            $('#refund_receipt_box').hide();
            $('#refund_receipt').val('');
            $('#refund_receipt_preview').empty();
            $('input[name="credit_action"][value="keep"]').prop('checked', true);
            // Pre-select the stored payment method (if any)
            $('#verify_payment_method').val(paymentMethodId && parseInt(paymentMethodId) > 0 ? String(paymentMethodId) : '');
            $('#verify_payment_receipt').val('');
            receiptShown = false;
            receiptUrl = null;

            // Remember the originally-chosen program (for diff detection)
            window._setOriginalProgramId(programId);

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
                    loadGroups(programId, groupId); // pre-select the seated group too
                } else {
                    $('#verify_program_id').val('');
                    $('#verify_group_id').html('<option value="">-- اختر المجموعة --</option>');
                }
                // NOTE: program-lock for an already-SEATED student is decided by the financials
                // response below (real GroupStudents membership), not by the fee row's group_id.
            }

            // Pull the receipt link from the row — keep hidden behind a button
            try {
                const row = $('.btn-verify[data-id="' + id + '"]').closest('tr');
                const thumb = row.find('.receipt-thumb').first();
                if (thumb.length && thumb.data('receipt-url')) {
                    receiptUrl = thumb.data('receipt-url');
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

            // Reset modal state before loading financial data
            $('#credit_breakdown_box').hide();
            $('#cash_remaining_hint').hide();
            $('#verified_amount_block').show();
            $('#verified_amount').prop('required', true);
            $('#program_group_box').show();          // always visible by default
            $('#completing_payment_notice').hide();  // hidden until isCompleting is confirmed
            window._pendingFin = null;
            $.get('{{ url("admin/financial/pending/financials") }}/' + id, function (f) {
                if (!f || !f.success) return;
                window._pendingFin = f;

                // Lock the program ONLY when the student has a REAL active seat in a group.
                // (A student merely assigned to a program — no group membership — stays editable.)
                if (f.is_seated && f.group_name) {
                    $('#verify_program_id').prop('disabled', true);
                    $('#default_program_badge').hide();
                    $('#already_seated_group').text(f.group_name);
                    $('#already_seated_notice').show();
                } else {
                    $('#verify_program_id').prop('disabled', false);
                    $('#already_seated_notice').hide();
                }

                if (f.has_credit) {
                    $('#cb_total').text(Number(f.total_due).toFixed(2));
                    $('#cb_credit').text(Number(f.paid_from_credit).toFixed(2));
                    $('#cb_min').text(Number(f.min_payment).toFixed(2));

                    // Distinguish two scenarios:
                    // (A) Actual credit balance (student overpaid before → credit auto-applied)
                    // (B) Completing payment (student paid partially before via verified payments,
                    //     no actual credit balance, just paying the remaining balance)
                    const hasRealCredit = (f.credit_available || 0) > 0.009;
                    const isCompleting  = !hasRealCredit && (f.paid_from_credit || 0) > 0.009;

                    if (isCompleting) {
                        // Scenario B: student is completing a partial payment — not a first-time enrollment
                        $('#cb_box_title').html('<i class="bi bi-check2-all me-1"></i> دفعات سابقة مؤكدة');
                        $('#cb_credit_lbl').text('مدفوع مسبقاً');
                        $('#cb_min_col').hide();
                        // Hide program/group selectors — already enrolled, no need to re-assign
                        $('#program_group_box').hide();
                        $('#already_seated_notice').hide();
                        // Show a clean informational notice instead
                        if (!$('#completing_payment_notice').length) {
                            $('#program_group_box').after(
                                '<div id="completing_payment_notice" class="alert alert-light-primary border border-primary border-dashed d-flex align-items-center gap-2 mt-2">'
                                + '<i class="bi bi-info-circle-fill text-primary fs-5"></i>'
                                + '<div>الطالب مُسجَّل بالفعل في برنامجه ومجموعته — هذه الدفعة لاستكمال السداد فقط، لا يلزم تغيير التسكين.</div>'
                                + '</div>'
                            );
                        } else {
                            $('#completing_payment_notice').show();
                        }
                    } else {
                        // Scenario A: restore to default credit-balance labels
                        $('#cb_box_title').html('<i class="bi bi-piggy-bank-fill me-1"></i> تسوية من الرصيد الدائن');
                        $('#cb_credit_lbl').text('مخصوم من الرصيد');
                        $('#cb_min_col').show();
                        // Restore program/group selectors
                        $('#program_group_box').show();
                        $('#completing_payment_notice').hide();
                    }

                    // Theme-aware note colours (bg-light-* / text-* adapt to dark & light themes)
                    let noteCls, noteHtml;
                    if (isCompleting) {
                        // Completing payment: show clear, non-credit wording
                        noteCls = 'bg-light-success text-success';
                        noteHtml = '<i class="bi bi-check-circle-fill"></i><div>'
                            + 'الطالب يُكمل سداد رسومه — مدفوع ومؤكد مسبقاً: <strong>'
                            + Number(f.paid_from_credit).toFixed(2) + ' ILS</strong>'
                            + (f.remaining > 0.009
                                ? '، المتبقّي بعد هذه الدفعة: <strong>' + Number(f.remaining).toFixed(2) + ' ILS</strong>'
                                : ' — <strong>يُغطّي الرسوم بالكامل</strong>')
                            + '.</div>';
                    } else if (f.min_cash_now > 0.009) {
                        noteCls = 'bg-light-warning text-warning';
                        noteHtml = '<i class="bi bi-exclamation-triangle-fill"></i><div>الرصيد الدائن لا يغطّي الحد الأدنى. <strong>يجب دفع ' + Number(f.min_cash_now).toFixed(2) + ' ILS نقدًا على الأقل الآن</strong>، والمتبقّي الكلّي ' + Number(f.remaining).toFixed(2) + ' ILS لإكمال السداد.</div>';
                    } else {
                        noteCls = 'bg-light-success text-success';
                        noteHtml = '<i class="bi bi-check-circle-fill"></i><div>تم تغطية الحد الأدنى من الرصيد الدائن. المتبقّي ' + Number(f.remaining).toFixed(2) + ' ILS (يمكن دفعه الآن أو لاحقًا) — يكفي تأكيد التسكين.</div>';
                    }
                    $('#cb_note').attr('class', 'mb-0 mt-3 py-2 px-3 fs-8 rounded d-flex align-items-start gap-2 ' + noteCls).html(noteHtml);
                    $('#credit_breakdown_box').show();

                    if (isCompleting) {
                        // Admin must verify the cash the student is submitting now
                        $('#verified_amount_block').show();
                        $('#verified_amount').prop('required', true).val(Number(f.remaining > 0.009 ? f.remaining : 0).toFixed(2));
                        refreshCashHint();
                    } else if (f.min_cash_now > 0.009) {
                        // Minimum NOT covered → admin must collect cash now
                        $('#verified_amount_block').show();
                        $('#verified_amount').prop('required', true).val(Number(f.min_cash_now).toFixed(2));
                        refreshCashHint();
                    } else {
                        // Minimum fully covered by credit → no additional cash needed
                        $('#verified_amount_block').hide();
                        $('#verified_amount').prop('required', false).val('0');
                        $('#cash_remaining_hint').hide();
                    }
                }
            });

            $('#verifyModal').modal('show');
            updateBalancePreview(claimed, total);
        };

        // Live hint: how much will remain after the cash entered now (credit already counted)
        function refreshCashHint() {
            const f = window._pendingFin;
            if (!f || !f.has_credit) { $('#cash_remaining_hint').hide(); return; }
            const cash = parseFloat($('#verified_amount').val()) || 0;
            const left = Math.max(0, +(f.total_due - f.paid_from_credit - cash).toFixed(2));
            const belowMin = cash + 0.009 < f.min_cash_now;
            $('#cash_remaining_hint')
                .css('color', belowMin ? '#dc3545' : (left > 0 ? '#b8860b' : '#198754'))
                .html((belowMin ? '<i class="bi bi-x-circle me-1"></i>أقل من الحد الأدنى المطلوب نقدًا (' + Number(f.min_cash_now).toFixed(2) + ' ILS). ' : '')
                    + 'المتبقّي بعد هذه الدفعة: ' + left.toFixed(2) + ' ILS')
                .show();
        }
        $(document).on('input', '#verified_amount', refreshCashHint);

        // In-panel receipt lightbox — opens when the receipt thumbnail in the table is clicked
        $(document).on('click', '.receipt-thumb', function () {
            const url     = $(this).data('receipt-url');
            const type    = $(this).data('receipt-type');
            const student = $(this).data('student') || '';
            if (!url) return;
            let html;
            if (type === 'pdf') {
                html = '<embed src="' + url + '" type="application/pdf" width="100%" height="600" style="border-radius:10px;border:1px solid #e5e9f0;">';
            } else {
                html = '<img src="' + url + '" alt="receipt" style="max-width:100%;max-height:72vh;border-radius:12px;box-shadow:0 12px 34px rgba(0,0,0,0.18);">';
            }
            $('#receipt_lightbox_content').html(html);
            $('#receipt_lightbox_student').text(student);
            $('#receipt_lightbox_open').attr('href', url);
            $('#receiptLightbox').modal('show');
        });

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
            const groupId = $btn.data('group') || null;
            const groupName = $btn.data('group-name') || '';
            const paymentMethodId = $btn.data('payment') || null;
            const studentId = $btn.data('student') || null;
            const paidType = $btn.data('type') || '';
            // call existing helper
            window.verifyPayment(id, claimed, total, programId, studentId, paidType, groupId, paymentMethodId, groupName);
        });

        // Reject / refund a pending payment
        window.refundPayment = function (id) {
            if (!id) return;
            Swal.fire({
                title: 'رفض الطلب؟',
                text: 'سيتم رفض هذا الطلب المالي. هل أنت متأكد؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'نعم، ارفض',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#d33',
            }).then(function (result) {
                if (!result.isConfirmed) return;
                Swal.fire({ title: 'جاري المعالجة...', didOpen: () => { Swal.showLoading(); } });
                $.post('{{ route("admin.financial.refund") }}', {
                    _token: '{{ csrf_token() }}',
                    id: id,
                }, function (res) {
                    const ok = res.status === 'success' || res.status === 'info';
                    Swal.fire(ok ? 'تم!' : 'خطأ', res.message, ok ? 'success' : 'error');
                    if (ok) $('#' + tableId).DataTable().ajax.reload();
                }).fail(function () {
                    Swal.fire('خطأ', 'تعذّر تنفيذ العملية.', 'error');
                });
            });
        };

        // Delegated click for refund (keeps original refundPayment call behavior)
        $(document).on('click', '.btn-refund', function(e) {
            const id = $(this).data('id');
            if (id) refundPayment(id);
        });

        window.loadGroups = function(programId, preselectGroupId) {
            const $sel  = $('#verify_group_id');
            const $wrap = $sel.parent();
            if (!programId) {
                $sel.html('<option value="">-- اختر المجموعة --</option>');
                return;
            }
            $sel.html('<option value="">جاري التحميل...</option>').prop('disabled', true);
            // Tiny inline spinner inside the select wrapper
            $wrap.css('position','relative').find('.input-spinner').remove();
            $wrap.append('<span class="input-spinner" style="position:absolute;inset-inline-end:32px;top:50%;transform:translateY(-50%);width:16px;height:16px;border:2px solid #e5e9f0;border-top-color:#009ef7;border-radius:50%;animation:inputSpin 0.7s linear infinite;pointer-events:none;"></span>');

            $.get('{{ route("admin.financial.groups_by_program", ":id") }}'.replace(':id', programId), function(data) {
                let html = '<option value="">-- اختر المجموعة --</option>';
                data.forEach(function(g) {
                    html += `<option value="${g.id}">${g.name} (${g.start_date || 'N/A'})</option>`;
                });
                $sel.html(html);
                // Pre-select the group the student was seated in (if provided & present in the list)
                if (preselectGroupId && $sel.find('option[value="' + preselectGroupId + '"]').length) {
                    $sel.val(String(preselectGroupId));
                }
            }).always(() => {
                $sel.prop('disabled', false);
                $wrap.find('.input-spinner').remove();
            });
        };

        // Ensure the input spinner keyframes exist on this page (in case the admin layout
        // doesn't already declare them)
        if (!document.getElementById('inputSpinKf')) {
            const st = document.createElement('style');
            st.id = 'inputSpinKf';
            st.textContent = '@keyframes inputSpin{to{transform:translateY(-50%) rotate(360deg);}}';
            document.head.appendChild(st);
        }

        $('#verified_amount').on('input', function() {
            const total = parseFloat($('#total_due_display').val());
            const verified = parseFloat($(this).val()) || 0;
            updateBalancePreview(verified, total);
            // Refresh diff when amount changes (only if a different program is chosen)
            scheduleSwapDiff();
        });

        // ===== Smart price-difference computation =====
        // Triggered whenever admin picks a DIFFERENT program than the student originally chose.
        let _swapDebounce = null;
        function scheduleSwapDiff() {
            clearTimeout(_swapDebounce);
            _swapDebounce = setTimeout(loadProgramSwapDiff, 250);
        }
        window.scheduleSwapDiff = scheduleSwapDiff;

        function loadProgramSwapDiff() {
            const feeId    = $('#verify_id').val();
            const chosen   = $('#verify_program_id').val();
            const original = window._originalProgramId || '';
            const verified = parseFloat($('#verified_amount').val()) || 0;

            // No swap → hide panel, show simple preview
            if (!chosen || String(chosen) === String(original)) {
                $('#program_swap_panel').addClass('d-none');
                $('#balance_preview').removeClass('d-none');
                return;
            }

            $.get('{{ route("admin.financial.program_swap_diff") }}', {
                fee_id: feeId,
                new_program_id: chosen,
                verified_amount: verified,
            }, function (res) {
                if (!res.success) return;
                renderSwapDiffPanel(res);
            });
        }

        function renderSwapDiffPanel(d) {
            $('#balance_preview').addClass('d-none');
            $('#program_swap_panel').removeClass('d-none');

            $('#swap_original_fee').text(d.original_fee.toFixed(2));
            $('#swap_new_fee').text(d.new_fee.toFixed(2));
            $('#swap_verified').text(d.verified.toFixed(2));

            // Delta box (green=cheaper, red=more expensive, gray=same)
            const $delta = $('#swap_delta_box');
            const sign = d.delta > 0 ? '+' : (d.delta < 0 ? '−' : '');
            $('#swap_delta').text(sign + Math.abs(d.delta).toFixed(2));
            if (d.delta > 0)      { $delta.css({background:'#fee2e2', borderColor:'#fecaca'}); $('#swap_delta').css('color','#b91c1c'); }
            else if (d.delta < 0) { $delta.css({background:'#d1fae5', borderColor:'#6ee7b7'}); $('#swap_delta').css('color','#065f46'); }
            else                  { $delta.css({background:'#f3f4f6', borderColor:'#e5e7eb'}); $('#swap_delta').css('color','#374151'); }

            // Outstanding/credit box
            const $out = $('#swap_outstanding_box');
            const showCreditActions = d.credit_amount > 0.01;
            if (showCreditActions) {
                $('#swap_outstanding_label').html('<i class="bi bi-piggy-bank-fill me-1"></i> رصيد دائن للطالب');
                $('#swap_outstanding').text(d.credit_amount.toFixed(2)).css('color','#1e40af');
                $out.css({background:'#dbeafe', border:'1px solid #93c5fd'});
                $('#swap_credit_actions').removeClass('d-none');
            } else if (d.outstanding > 0.01) {
                $('#swap_outstanding_label').html('<i class="bi bi-exclamation-triangle-fill me-1"></i> المتبقّي على الطالب');
                $('#swap_outstanding').text(d.outstanding.toFixed(2)).css('color','#b91c1c');
                $out.css({background:'#fee2e2', border:'1px solid #fecaca'});
                $('#swap_credit_actions').addClass('d-none');
            } else {
                $('#swap_outstanding_label').html('<i class="bi bi-check-circle-fill me-1"></i> الرصيد متوازن');
                $('#swap_outstanding').text('0.00').css('color','#065f46');
                $out.css({background:'#d1fae5', border:'1px solid #6ee7b7'});
                $('#swap_credit_actions').addClass('d-none');
            }

            // Recommendation alert
            const recoMap = {
                balanced:  ['alert-success', 'bi-check-circle-fill'],
                remaining: ['alert-danger',  'bi-exclamation-triangle-fill'],
                credit:    ['alert-primary', 'bi-piggy-bank-fill'],
            };
            const cls = recoMap[d.recommendation.type] || ['alert-secondary','bi-info-circle'];
            $('#swap_recommendation')
                .removeClass('alert-success alert-danger alert-primary alert-secondary')
                .addClass(cls[0])
                .html('<i class="bi ' + cls[1] + ' fs-4"></i><div><strong>توصية:</strong> ' + d.recommendation.message + '</div>');
        }

        // Track original program when modal opens so we know when admin changes it
        window._setOriginalProgramId = function (pid) { window._originalProgramId = pid || ''; };

        // Re-compute when program selection changes
        $('#verify_program_id').on('change', scheduleSwapDiff);

        function updateBalancePreview(verified, total) {
            const remaining = total - verified;
            $('#remaining_val').text(remaining.toFixed(2));
            $('#balance_preview').removeClass('d-none');
        }

        // Show the refund-proof uploader only when "ردّ المبلغ نقداً" is chosen
        $(document).on('change', 'input[name="credit_action"]', function () {
            if ($(this).val() === 'refund' && $(this).is(':checked')) {
                $('#refund_receipt_box').slideDown(150);
            } else {
                $('#refund_receipt_box').slideUp(150);
            }
        });
        // Live preview of the chosen refund image
        $(document).on('change', '#refund_receipt', function () {
            const file = this.files && this.files[0];
            const $p = $('#refund_receipt_preview').empty();
            if (file && /^image\//.test(file.type)) {
                const reader = new FileReader();
                reader.onload = e => $p.html('<img src="' + e.target.result + '" style="max-height:160px;border-radius:8px;border:1px solid #eee;">');
                reader.readAsDataURL(file);
            }
        });

        // ===== Side notification for background email sending =====
        function mailNotify(state, reports) {
            let $box = $('#mail_notify_box');
            if (!$box.length) {
                $('body').append(
                    '<div id="mail_notify_box" style="position:fixed;bottom:20px;inset-inline-start:20px;z-index:1095;width:320px;background:#fff;border:1px solid #e5e9f0;border-radius:14px;box-shadow:0 12px 36px rgba(0,0,0,0.18);overflow:hidden;font-family:inherit;"></div>'
                );
                $box = $('#mail_notify_box');
            }
            let header = '<div style="display:flex;align-items:center;gap:8px;padding:12px 16px;background:linear-gradient(135deg,#003366,#002a55);color:#fff;">'
                + '<i class="bi bi-envelope-paper-fill"></i><span style="font-weight:700;font-size:0.92rem;">إشعارات البريد الإلكتروني</span>'
                + '<i class="bi bi-x-lg ms-auto" style="cursor:pointer;opacity:.8;" onclick="$(\'#mail_notify_box\').remove();"></i></div>';
            let body;
            if (state === 'loading') {
                body = '<div style="padding:16px;display:flex;align-items:center;gap:10px;color:#475569;font-size:0.88rem;">'
                    + '<span class="spinner-border spinner-border-sm text-primary"></span> جاري الإرسال في الخلفية...</div>';
            } else {
                const rows = (reports || []).map(r =>
                    '<div style="display:flex;align-items:center;gap:8px;padding:6px 0;font-size:0.85rem;color:#334155;">'
                    + (r.ok ? '<i class="bi bi-check-circle-fill" style="color:#16a34a;"></i>' : '<i class="bi bi-x-circle-fill" style="color:#dc2626;"></i>')
                    + '<span>' + r.label + '</span></div>'
                ).join('');
                body = '<div style="padding:12px 16px;">' + (rows || '<div style="color:#94a3b8;font-size:0.85rem;">لا توجد إشعارات.</div>') + '</div>';
            }
            $box.html(header + body);
            if (state === 'done') {
                clearTimeout(window._mailNotifyTimer);
                window._mailNotifyTimer = setTimeout(() => $('#mail_notify_box').fadeOut(400, function(){ $(this).remove(); }), 7000);
            }
        }

        $('#verifyForm').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this); // FormData → supports the refund image upload

            Swal.fire({
                title: 'جاري تأكيد الدفعة...',
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: '{{ route("admin.financial.verify") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
            }).done(function (res) {
                if (res.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'تم تأكيد الدفعة!', text: res.message, timer: 2200, showConfirmButton: false });
                    $('#verifyModal').modal('hide');
                    $('#' + tableId).DataTable().ajax.reload();

                    // Fire the email notifications in the background + show live status on the side
                    if (res.notify && res.fee_id) {
                        mailNotify('loading');
                        $.post('{{ route("admin.financial.send_notifications") }}', {
                            _token: '{{ csrf_token() }}',
                            id: res.fee_id,
                        }).done(function (r) {
                            mailNotify('done', (r && r.reports) || []);
                        }).fail(function () {
                            mailNotify('done', [{ ok: false, label: 'تعذّر إرسال الإشعارات' }]);
                        });
                    }
                } else {
                    Swal.fire('خطأ', res.message, 'error');
                }
            }).fail(function (xhr) {
                let m = 'حدث خطأ أثناء المعالجة.';
                if (xhr.responseJSON && xhr.responseJSON.message) m = xhr.responseJSON.message;
                Swal.fire('خطأ', m, 'error');
            });
        });
    });
</script>
@include('admin.layout.masterLayouts.datatableMaster', [
    'active_menu' => 'financial',
    'customAjaxUrl' => route('admin.financial.pending.list')
])

<script>
(function () {
    var memTable = null;

    function initMembershipTable() {
        if (memTable) return; // already initialized
        memTable = $('#mem_kt_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("membership.list") }}',
                type: 'GET',
                data: function(d) {
                    d.search     = $('#mem_search').val();
                    d.program_type    = $('#mem_program_type').val();
                    d.enrollment_type = $('#mem_enrollment_type').val();
                    d.date_from  = $('#mem_date_from').val();
                    d.date_to    = $('#mem_date_to').val();
                    d.is_today   = $('#mem_is_today').val();
                }
            },
            columns: [
                { data: 'id', name: 'id', orderable: false, searchable: false, render: function(data, type, row, meta){ return meta.row + meta.settings._iDisplayStart + 1; } },
                { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false, className: 'text-center align-middle' },
                { data: 'name', name: 'name', className: 'text-start align-middle' },
                { data: 'mobile', name: 'mobile', className: 'text-center align-middle' },
                { data: 'dob', name: 'dob', className: 'text-center align-middle' },
                { data: 'join_date_fmt', name: 'join_date_fmt', orderable: false, searchable: false, className: 'text-center align-middle' },
                { data: 'status', name: 'status', orderable: false, searchable: false, className: 'text-center align-middle' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center align-middle' }
            ],
            language: { url: '{{ asset("assets/admin/plugins/datatables/Arabic.json") }}' },
            order: [[0, 'desc']],
            pageLength: 15,
            responsive: true,
            drawCallback: function() {
                // Update badge count
                var info = this.api().page.info();
                $('#membership_count_badge').text(info.recordsFiltered);
            }
        });

        // Initialize flatpickr for membership tab date pickers
        $('.mem-date-picker').flatpickr({ dateFormat: 'Y-m-d' });

        // Select all
        $('#mem_selectAll').on('change', function() {
            $('#mem_kt_table .checkboxes').prop('checked', this.checked);
        });

        // Filter on input change
        ['#mem_search', '#mem_program_type', '#mem_enrollment_type', '#mem_date_from', '#mem_date_to'].forEach(function(sel) {
            $(document).on('change keyup', sel, function() {
                if (memTable) memTable.draw();
            });
        });

        $('#mem_todayBtn').on('click', function() {
            $('#mem_is_today').val('1');
            $('#mem_filter_form')[0].reset();
            $('#mem_filter_form select').val('').trigger('change');
            memTable.draw();
        });
        $('#mem_showAllBtn').on('click', function() {
            $('#mem_is_today').val('all');
            $('#mem_filter_form')[0].reset();
            $('#mem_filter_form select').val('').trigger('change');
            memTable.draw();
        });
        $('#mem_resetFilter').on('click', function() {
            $('#mem_is_today').val('');
            $('#mem_filter_form')[0].reset();
            $('#mem_filter_form select').val('').trigger('change');
            memTable.draw();
        });
    }

    // Initialize table when tab is shown
    $('a[href="#tab_membership"]').on('shown.bs.tab', function() {
        initMembershipTable();
    });

    // Bulk activate for membership tab
    $(document).on('click', '.mem_BulkActivate', function() {
        var selectedIds = [];
        $('#mem_kt_table .checkboxes:checked').each(function() {
            selectedIds.push($(this).attr('data-id'));
        });
        if (selectedIds.length === 0) {
            Swal.fire({ text: 'يجب اختيار طالب واحد على الأقل!', icon: 'warning', buttonsStyling: false, confirmButtonText: 'حسناً', customClass: { confirmButton: 'btn btn-primary' } });
            return;
        }
        Swal.fire({
            title: 'تأكيد التفعيل الجماعي',
            text: 'هل أنت متأكد من تفعيل ' + selectedIds.length + ' من الحسابات وإرسال بيانات الدخول؟',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'نعم، تفعيل الكل',
            cancelButtonText: 'إلغاء',
            customClass: { confirmButton: 'btn btn-success', cancelButton: 'btn btn-light' },
            buttonsStyling: false,
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return $.ajax({
                    url: '{{ route("students.bulk.status.membership") }}',
                    method: 'POST',
                    data: { ids: selectedIds, _token: '{{ csrf_token() }}' }
                }).catch(function(err) {
                    Swal.showValidationMessage(err.responseJSON ? err.responseJSON.message : 'حدث خطأ');
                });
            }
        }).then(function(result) {
            if (result.isConfirmed && result.value) {
                Swal.fire({ title: 'تم!', text: result.value.message, icon: 'success', buttonsStyling: false, confirmButtonText: 'حسناً', customClass: { confirmButton: 'btn btn-primary' } });
                if (memTable) memTable.draw();
            }
        });
    });

    // Load initial count for badge without drawing the full table
    $.get('{{ route("membership.list") }}', { length: 1, start: 0 }, function(res) {
        if (res && res.recordsFiltered !== undefined) {
            $('#membership_count_badge').text(res.recordsFiltered);
        }
    });
})();
</script>
@stop
