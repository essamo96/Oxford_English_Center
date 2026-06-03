@extends('admin.layout.master')

@section('title')
    إدارة المجموعات
@stop

@section('page-title')
    إدارة المجموعات
@stop

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">إدارة المجموعات</li>
@stop

@section('page-content')
    <div class="card shadow-sm mb-9">
        <div class="card-header">
            <h3 class="card-title">فلاتر البحث والمراجعة المتقدمة</h3>
            <div class="card-toolbar">
                <button type="button" class="btn btn-sm btn-light-danger" id="reset_button">
                    <i class="bi bi-arrow-clockwise fs-4 me-1"></i> تصفية الفلاتر
                </button>
            </div>
        </div>
        <div class="card-body">
            <form id="filter_form" class="row g-7">
                <!-- Group Name -->
                <div class="col-lg-3 col-md-6 fv-row">
                    <label class="form-label fw-bold">اسم المجموعة</label>
                    <input type="text" id="title" name="title" class="form-control form-control-solid" placeholder="بحث باسم المجموعة...">
                </div>

                <!-- Student Search -->
                <div class="col-lg-3 col-md-6 fv-row">
                    <label class="form-label fw-bold">بحث باسم الطالب أو الجوال</label>
                    <input type="text" id="student_name" name="student_name" class="form-control form-control-solid" placeholder="الاسم أو رقم الجوال...">
                </div>

                <!-- Program -->
                <div class="col-lg-3 col-md-6 fv-row">
                    <label class="form-label fw-bold">البرنامج التعليمي</label>
                    <select id="program_id" name="program_id" class="form-select form-select-solid" data-control="select2" data-placeholder="اختر البرنامج">
                        <option value=""></option>
                        @foreach ($programs as $item)
                            <option value="{{ $item->id }}">{{ $item->title }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Teacher Filter with Avatars -->
                <div class="col-lg-3 col-md-6 fv-row">
                    <label class="form-label fw-bold">المدرس</label>
                    <select id="teacher_id" name="teacher_id" class="form-select form-select-solid" data-control="select2" data-placeholder="اختر المدرس">
                        <option value=""></option>
                        @foreach ($teachers as $item)
                            <option value="{{ $item->id }}" data-kt-select2-image="{{ $item->image ? url($item->image) : asset('assets/media/avatars/blank.png') }}">
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Range & Time -->
                <div class="col-lg-6 col-md-12 fv-row">
                    <label class="form-label fw-bold">تاريخ البدء & الوقت</label>
                    <div class="d-flex align-items-center gap-3">
                        <input type="date" name="date_from" id="date_from" class="form-control form-control-solid" placeholder="من" />
                        <span class="text-gray-500">إلى</span>
                        <input type="date" name="date_to" id="date_to" class="form-control form-control-solid" placeholder="إلى" />
                        
                        <div class="flex-grow-1 ms-3">
                            <select id="date_id" name="date_id" class="form-select form-select-solid" data-control="select2" data-placeholder="اختر الوقت">
                                <option value=""></option>
                                @foreach ($times as $item)
                                    <option value="{{ $item->id }}">{{ $item->days }} | {{ $item->times }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Status and Quick Filters -->
                <div class="col-lg-6 col-md-12 d-flex align-items-end gap-3 flex-wrap">
                    <div class="w-150px">
                        <label class="form-label fw-bold">الحالة</label>
                        <select id="activeG" name="activeG" class="form-select form-select-solid" data-control="select2" data-placeholder="الحالة">
                            <option value=""></option>
                            <option value="1">نشط</option>
                            <option value="0">منتهي</option>
                        </select>
                    </div>

                    <label class="form-check form-check-custom form-check-solid bg-light-success rounded p-3 h-45px border border-success border-dashed shadow-sm cursor-pointer hover-elevate-up transition-3ms">
                        <input class="form-check-input h-20px w-20px me-3" type="checkbox" name="is_today" id="is_today" value="1" />
                        <div class="d-flex flex-column">
                            <span class="form-check-label fw-bold text-success fs-6 lh-1 mb-1">مجموعات اليوم</span>
                            <span class="text-dark fs-10">تصفية حسب جدول اليوم فقط</span>
                        </div>
                    </label>
                </div>

            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                        <span class="path1"></span><span class="path2"></span>
                    </i>
                    <input type="text" data-kt-user-table-filter="search"
                        class="form-control form-control-solid w-250px ps-13" placeholder="بحث سريع..." />
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end gap-3">
                    <button type="button" class="btn btn-sm btn-light-success" id="CEmail">
                        <i class="ki-duotone ki-sms fs-2"><span class="path1"></span><span class="path2"></span></i>
                        إرسال إيميل للمحددين
                    </button>

                    <button type="button" class="btn btn-sm btn-light-primary" id="sms">
                        <i class="ki-duotone ki-sms fs-2"><span class="path1"></span><span class="path2"></span></i>
                        إرسال SMS للمحددين
                    </button>

                    @can('admin.groups.edit')
                        <button type="button" class="btn btn-warning text-nowrap d-inline-flex align-items-center gap-1"
                                style="padding: 5px 11px; font-size: 0.78rem; font-weight:600;"
                                data-bs-toggle="modal" data-bs-target="#bulkAssignModal">
                            <i class="bi bi-people-fill" style="font-size:0.95rem;"></i>
                            <span>تشعيب طلاب</span>
                        </button>
                        <button type="button" class="btn btn-info text-nowrap d-inline-flex align-items-center gap-1"
                                style="padding: 5px 11px; font-size: 0.78rem; font-weight:600;"
                                data-bs-toggle="modal" data-bs-target="#promoteModal">
                            <i class="bi bi-arrow-up-right-square-fill" style="font-size:0.95rem;"></i>
                            <span>تصعيد طلاب</span>
                        </button>
                    @endcan

                    @can('admin.groups.add')
                        <a href="{{ route('groups.add') }}" class="btn btn-sm btn-primary">
                            <i class="ki-duotone ki-plus fs-2"></i> إضافة مجموعة
                        </a>
                    @endcan
                    <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">
                        <i class="ki-duotone ki-arrow-left fs-2"><span class="path1"></span><span
                                class="path2"></span></i>
                        رجوع
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body py-4">
            @include('admin.layout.masterLayouts.error')
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center"
                    id="groups_table">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="w-25px text-center"> # </th>
                            <th class="w-25px text-center">
                                <div class="d-flex align-items-center justify-content-center" style="height: 48px;">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input h-20px w-20px" type="checkbox" id="select-all" />
                                    </div>
                                </div>
                            </th>
                            <th class="min-w-120px"> اسم المجموعة </th>
                            <th class="min-w-120px"> المدرس </th>
                            <th class="min-w-120px"> البرنامج </th>
                            <th class="min-w-130px"> الطلاب </th>
                            <th class="min-w-130px"> كود الانضمام </th>
                            <th class="min-w-130px"> الشهادات </th>
                            <th class="text-center min-w-80px"> الحالة </th>
                            <th class="text-center min-w-100px"> العمليات </th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold"></tbody>
                </table>
            </div>
        </div>
    </div>

@stop

@section('modal')
    @include('admin.layout.masterLayouts.modal')

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

    {{-- ===== Shuttle UI styles — theme-aware (Light + Dark) ===== --}}
    <style>
        /* ---------- Theme tokens (Light defaults) ---------- */
        .shuttle-modal {
            --sh-surface:       #ffffff;
            --sh-surface-2:     #fafbfd;
            --sh-surface-3:     #f8fafc;
            --sh-border:        #e5e9f0;
            --sh-border-2:      #eef0f5;
            --sh-border-hover:  #cfd6e2;
            --sh-text:          #1f2937;
            --sh-muted:         #6b7280;
            --sh-faint:         #9aa3b3;
            --sh-label:         #374151;
            --sh-grip:          #b8c0cf;
            --sh-count-bg:      #eef2f7;
            --sh-count-fg:      #475569;
            --sh-item-hover:    #f7fafc;
            --sh-shadow:        0 6px 18px rgba(0,0,0,0.12);

            /* Highlight tints for header strips */
            --sh-warn-tint:     #fef9e7;
            --sh-success-tint:  #ecfdf5;

            /* Tag pills */
            --sh-tag-kids-bg:   #d1fae5;  --sh-tag-kids-fg:   #065f46;
            --sh-tag-adult-bg:  #dbeafe;  --sh-tag-adult-fg:  #1e3a8a;
            --sh-tag-level-bg:  #fef3c7;  --sh-tag-level-fg:  #92400e;
            --sh-count-warn-bg: #fde68a;  --sh-count-warn-fg: #92400e;
            --sh-count-ok-bg:   #a7f3d0;  --sh-count-ok-fg:   #065f46;

            /* Ghost (dragged) */
            --sh-ghost-bg:      #fffbeb;
            --sh-droptarget-bg: #f0f9ff;
            --sh-droptarget-bd: #3b82f6;
        }

        /* ---------- Dark mode overrides ----------
           Supports all common selectors: Bootstrap 5.3 native, Metronic, and class-based */
        [data-bs-theme="dark"] .shuttle-modal,
        [data-theme-mode="dark"] .shuttle-modal,
        .dark-mode .shuttle-modal,
        html.dark .shuttle-modal {
            --sh-surface:       #1c1d2b;
            --sh-surface-2:     #15161f;
            --sh-surface-3:     #1f2030;
            --sh-border:        #2b2d3c;
            --sh-border-2:      #262737;
            --sh-border-hover:  #3b3d52;
            --sh-text:          #e7e9ef;
            --sh-muted:         #9aa0b4;
            --sh-faint:         #6b708a;
            --sh-label:         #c7cbdb;
            --sh-grip:          #5a5f76;
            --sh-count-bg:      #2a2c3c;
            --sh-count-fg:      #b6bcd0;
            --sh-item-hover:    #23253a;
            --sh-shadow:        0 8px 22px rgba(0,0,0,0.55);

            --sh-warn-tint:     #2a2618;
            --sh-success-tint:  #16241f;

            --sh-tag-kids-bg:   #133929;  --sh-tag-kids-fg:   #4ade80;
            --sh-tag-adult-bg:  #16264b;  --sh-tag-adult-fg:  #93c5fd;
            --sh-tag-level-bg:  #3a2c10;  --sh-tag-level-fg:  #fbbf24;
            --sh-count-warn-bg: #3a2c10;  --sh-count-warn-fg: #fbbf24;
            --sh-count-ok-bg:   #133929;  --sh-count-ok-fg:   #4ade80;

            --sh-ghost-bg:      #2a2618;
            --sh-droptarget-bg: #122036;
            --sh-droptarget-bd: #60a5fa;
        }

        /* ---------- Layout ---------- */
        .shuttle-modal .modal-dialog       { max-width: 980px; }
        .shuttle-modal .modal-content      {
            border-radius: 16px; overflow: hidden;
            background: var(--sh-surface);
            color: var(--sh-text);
            border: 1px solid var(--sh-border);
        }
        .shuttle-modal .modal-body         { padding: 18px 22px; background: var(--sh-surface); }
        .shuttle-modal .modal-header       {
            padding: 14px 22px; border-bottom: 1px solid var(--sh-border-2);
            background: var(--sh-surface-2);
        }
        .shuttle-modal .modal-footer       {
            padding: 12px 22px; border-top: 1px solid var(--sh-border-2);
            background: var(--sh-surface-2);
        }
        .shuttle-modal .modal-header .btn-close {
            filter: var(--sh-close-filter, none);
        }
        [data-bs-theme="dark"] .shuttle-modal .modal-header .btn-close,
        [data-theme-mode="dark"] .shuttle-modal .modal-header .btn-close,
        .dark-mode .shuttle-modal .modal-header .btn-close,
        html.dark .shuttle-modal .modal-header .btn-close {
            filter: invert(1) grayscale(100%) brightness(1.2);
        }

        .shuttle-column {
            background: var(--sh-surface);
            border: 1.5px solid var(--sh-border);
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            min-height: 0;
        }
        .shuttle-head {
            padding: 12px 14px;
            border-bottom: 1px solid var(--sh-border-2);
            background: var(--sh-surface-2);
            border-radius: 12px 12px 0 0;
        }
        .shuttle-head .ttl { font-weight: 700; font-size: 0.92rem; margin: 0; color: var(--sh-text); }
        .shuttle-head input[type="text"] {
            height: 36px; font-size: 0.88rem; margin-top: 8px;
            background: var(--sh-surface);
            color: var(--sh-text);
            border: 1px solid var(--sh-border);
        }
        .shuttle-head input[type="text"]::placeholder { color: var(--sh-faint); }

        /* Override header tint for "basket" sides without hard-coded colors */
        .shuttle-modal .shuttle-head.tint-warn    { background: var(--sh-warn-tint); }
        .shuttle-modal .shuttle-head.tint-success { background: var(--sh-success-tint); }

        .shuttle-list {
            list-style: none;
            margin: 0;
            padding: 6px;
            overflow-y: auto;
            flex: 1 1 auto;
            min-height: 320px;
            max-height: 360px;
            background: var(--sh-surface);
        }
        .shuttle-empty {
            text-align: center;
            padding: 40px 16px;
            color: var(--sh-faint);
            font-style: italic;
            font-size: 0.88rem;
        }

        .shuttle-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            margin-bottom: 4px;
            background: var(--sh-surface);
            border: 1px solid var(--sh-border-2);
            border-radius: 8px;
            cursor: grab;
            transition: background-color .15s ease, border-color .15s ease, transform .12s ease;
            color: var(--sh-text);
        }
        .shuttle-item:hover { background: var(--sh-item-hover); border-color: var(--sh-border-hover); }
        .shuttle-item:active { cursor: grabbing; transform: scale(0.99); }
        .shuttle-item .grip {
            color: var(--sh-grip); font-size: 0.95rem; flex-shrink: 0;
        }
        .shuttle-item .avatar {
            width: 30px; height: 30px; border-radius: 50%; object-fit: cover;
            border: 1px solid var(--sh-border-2); flex-shrink: 0;
        }
        .shuttle-item .meta { flex: 1; min-width: 0; }
        .shuttle-item .name { font-weight: 600; font-size: 0.88rem; color: var(--sh-text); line-height: 1.2; }
        .shuttle-item .sub  { font-size: 0.72rem; color: var(--sh-muted); margin-top: 2px; }
        .shuttle-item .tags { display: flex; gap: 4px; flex-shrink: 0; }
        .shuttle-item .tag  {
            font-size: 0.62rem; font-weight: 700; padding: 2px 6px; border-radius: 4px;
            text-transform: uppercase; letter-spacing: 0.02em;
        }
        .shuttle-item .tag.kids   { background: var(--sh-tag-kids-bg);  color: var(--sh-tag-kids-fg); }
        .shuttle-item .tag.adult  { background: var(--sh-tag-adult-bg); color: var(--sh-tag-adult-fg); }
        .shuttle-item .tag.level  { background: var(--sh-tag-level-bg); color: var(--sh-tag-level-fg); }

        /* ===== Universal AJAX spinner overlay ===== */
        .ajax-spinner-overlay {
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.78);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 10;
            border-radius: inherit;
            backdrop-filter: blur(2px);
            transition: opacity 0.2s ease;
        }
        [data-bs-theme="dark"] .ajax-spinner-overlay,
        [data-theme-mode="dark"] .ajax-spinner-overlay,
        .dark-mode .ajax-spinner-overlay,
        html.dark .ajax-spinner-overlay {
            background: rgba(28, 29, 43, 0.82);
        }
        .ajax-spinner-overlay .spinner-grow,
        .ajax-spinner-overlay .spinner-border { width: 2.4rem; height: 2.4rem; }
        .ajax-spinner-overlay small { color: #6c7689; margin-top: 8px; font-weight: 600; font-size: 0.82rem; }
        .ajax-host { position: relative; }

        /* ===== QR Modal — themed header & buttons ===== */
        .qr-modal .modal-content { border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 24px 60px rgba(0, 51, 102, 0.25); }
        .qr-modal-header {
            background: linear-gradient(135deg, #003366 0%, #001a40 100%);
            border-bottom: 3px solid #ffcc00;
            color: #fff !important;
        }
        .qr-modal-header .modal-title { font-size: 1.15rem; letter-spacing: 0.01em; }
        .qr-modal-header .modal-title #qrModalGroupName {
            font-family: 'Fraunces', 'Playfair Display', Georgia, serif;
            font-weight: 600;
            font-size: 1.2rem;
            text-shadow: 0 2px 8px rgba(255, 204, 0, 0.3);
        }
        .qr-modal-header .btn-close { opacity: 0.85; }
        .qr-modal-header .btn-close:hover { opacity: 1; }

        /* "توليد الـ QR" — white text + icon on navy → gold gradient */
        .btn-qr-generate {
            background: linear-gradient(135deg, #003366 0%, #0a4486 50%, #003366 100%);
            background-size: 200% 200%;
            color: #ffffff !important;
            border: 2px solid transparent;
            padding: 14px 28px;
            font-weight: 700;
            font-size: 1.02rem;
            letter-spacing: 0.04em;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 8px 22px rgba(0, 51, 102, 0.32);
            transition: all 0.3s ease;
            animation: qrBtnGradient 4s ease infinite;
            position: relative;
            overflow: hidden;
        }
        @keyframes qrBtnGradient {
            0%, 100% { background-position: 0% 50%; }
            50%      { background-position: 100% 50%; }
        }
        .btn-qr-generate i, .btn-qr-generate span { color: #ffffff !important; }
        .btn-qr-generate i { font-size: 1.15rem; transition: transform 0.4s ease; }
        .btn-qr-generate:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(0, 51, 102, 0.42);
            border-color: #ffcc00;
            color: #ffffff !important;
        }
        .btn-qr-generate:hover i { transform: rotate(-15deg) scale(1.1); }
        .btn-qr-generate:disabled { opacity: 0.7; cursor: wait; transform: none; }
        .btn-qr-generate::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, transparent 0%, rgba(255,204,0,0.25) 50%, transparent 100%);
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }
        .btn-qr-generate:hover::after { transform: translateX(100%); }

        /* ===== QR generation overlay — animated scan frame ===== */
        .qr-canvas-wrap { background: #fff !important; border: 2px solid #003366 !important; }
        .qr-gen-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(4px);
            border-radius: inherit;
        }
        .qr-scan-frame {
            width: 65%;
            aspect-ratio: 1 / 1;
            position: relative;
            margin-bottom: 14px;
        }
        .qr-corner {
            position: absolute;
            width: 22px;
            height: 22px;
            border: 4px solid #ffcc00;
            animation: qrCornerPulse 1.4s ease-in-out infinite;
        }
        .qr-corner.tl { top: 0;    left: 0;    border-right: none; border-bottom: none; border-radius: 4px 0 0 0; }
        .qr-corner.tr { top: 0;    right: 0;   border-left:  none; border-bottom: none; border-radius: 0 4px 0 0; }
        .qr-corner.bl { bottom: 0; left: 0;    border-right: none; border-top:    none; border-radius: 0 0 0 4px; }
        .qr-corner.br { bottom: 0; right: 0;   border-left:  none; border-top:    none; border-radius: 0 0 4px 0; }
        @keyframes qrCornerPulse {
            0%, 100% { transform: scale(1);   opacity: 1;   }
            50%      { transform: scale(1.15); opacity: 0.65; }
        }
        .qr-scan-line {
            position: absolute;
            left: 6%;
            right: 6%;
            height: 3px;
            background: linear-gradient(90deg, transparent 0%, #ffcc00 50%, transparent 100%);
            box-shadow: 0 0 18px 2px #ffcc00, 0 0 6px rgba(255, 204, 0, 0.8);
            border-radius: 3px;
            top: 0;
            animation: qrScanSweep 1.8s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes qrScanSweep {
            0%   { top: 8%;  opacity: 0; }
            10%  { opacity: 1; }
            45%  { top: 92%; opacity: 1; }
            55%  { top: 92%; opacity: 0; }
            56%  { top: 8%;  opacity: 0; }
            66%  { opacity: 1; }
            95%  { top: 92%; opacity: 1; }
            100% { top: 92%; opacity: 0; }
        }
        .qr-gen-text {
            color: #003366;
            font-weight: 700;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 6px;
            animation: qrTextPulse 1.8s ease-in-out infinite;
        }
        .qr-gen-text i { color: #ffcc00; font-size: 1.2rem; animation: qrIconSpin 2.5s linear infinite; }
        @keyframes qrIconSpin   { to { transform: rotate(360deg); } }
        @keyframes qrTextPulse  { 0%, 100% { opacity: 1; } 50% { opacity: 0.6; } }

        /* Per-row history button — Metronic-style icon button */
        .shuttle-history-btn {
            background: #f1faff;
            border: 1px solid transparent;
            color: #009ef7;
            border-radius: 8px;
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
            cursor: pointer;
            transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
            flex-shrink: 0;
            box-shadow: 0 1px 2px rgba(0, 158, 247, 0.08);
        }
        .shuttle-history-btn:hover {
            background: #009ef7;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(0, 158, 247, 0.35);
        }
        .shuttle-history-btn:active { transform: translateY(0); }
        [data-bs-theme="dark"] .shuttle-history-btn,
        [data-theme-mode="dark"] .shuttle-history-btn,
        .dark-mode .shuttle-history-btn,
        html.dark .shuttle-history-btn {
            background: rgba(0, 158, 247, 0.15);
            color: #4ecbff;
        }
        .shuttle-item .tag.exists { background: var(--sh-count-bg);     color: var(--sh-count-fg); }
        .shuttle-item .tag.new    { background: var(--sh-count-ok-bg);  color: var(--sh-count-ok-fg); }

        /* Locked (existing-member) row — visible but not interactive */
        .shuttle-item.locked       { cursor: not-allowed; opacity: 0.72; background: var(--sh-surface-2); }
        .shuttle-item.locked:hover { background: var(--sh-surface-2); border-color: var(--sh-border-2); transform: none; }
        .shuttle-item.locked .grip { color: transparent; }
        .shuttle-item.locked .grip::before {
            content: '\F47A'; /* bi-lock-fill */
            font-family: 'bootstrap-icons';
            color: var(--sh-faint);
        }
        /* Unlocked state — applied to all locked items when admin clicks unlock toggle */
        .shuttle-list.unlock-mode .shuttle-item.locked       { cursor: grab; opacity: 1; background: var(--sh-surface); border-color: #fbbf24; }
        .shuttle-list.unlock-mode .shuttle-item.locked:hover { background: var(--sh-item-hover); border-color: #f59e0b; }
        .shuttle-list.unlock-mode .shuttle-item.locked .grip::before { content: '\F47B'; color: #f59e0b; } /* bi-unlock-fill */
        .shuttle-list.unlock-mode .shuttle-item.locked .tag.exists   {
            background: #fed7aa; color: #9a3412;
        }
        /* Toggle button active visual */
        #unlockToggleBtn.active { background: #fef3c7; color: #92400e; }
        #unlockToggleBtn.active i::before { content: '\F47B'; } /* bi-unlock-fill */

        /* Pulse the save button when there are pending changes */
        .btn-pulse { animation: btnPulse 1.8s ease-in-out infinite; box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.55); }
        @keyframes btnPulse {
            0%   { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.5); }
            70%  { box-shadow: 0 0 0 12px rgba(245, 158, 11, 0); }
            100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
        }
        .btn-pulse .badge { font-size: 0.7rem; padding: 3px 8px; }

        /* ---------- Select2 dark mode overrides (scoped to modal) ---------- */
        [data-bs-theme="dark"] .shuttle-modal .select2-container--bootstrap5 .select2-selection,
        [data-theme-mode="dark"] .shuttle-modal .select2-container--bootstrap5 .select2-selection,
        .dark-mode .shuttle-modal .select2-container--bootstrap5 .select2-selection,
        html.dark .shuttle-modal .select2-container--bootstrap5 .select2-selection {
            background: var(--sh-surface) !important;
            color: var(--sh-text) !important;
            border-color: var(--sh-border) !important;
        }
        [data-bs-theme="dark"] .shuttle-modal .select2-container--bootstrap5 .select2-selection__rendered,
        [data-theme-mode="dark"] .shuttle-modal .select2-container--bootstrap5 .select2-selection__rendered,
        .dark-mode .shuttle-modal .select2-container--bootstrap5 .select2-selection__rendered,
        html.dark .shuttle-modal .select2-container--bootstrap5 .select2-selection__rendered {
            color: var(--sh-text) !important;
        }
        [data-bs-theme="dark"] .shuttle-modal .select2-search__field,
        [data-theme-mode="dark"] .shuttle-modal .select2-search__field,
        .dark-mode .shuttle-modal .select2-search__field,
        html.dark .shuttle-modal .select2-search__field {
            background: var(--sh-surface) !important;
            color: var(--sh-text) !important;
            border: 1px solid var(--sh-border) !important;
        }
        [data-bs-theme="dark"] .shuttle-modal .select2-dropdown,
        [data-theme-mode="dark"] .shuttle-modal .select2-dropdown,
        .dark-mode .shuttle-modal .select2-dropdown,
        html.dark .shuttle-modal .select2-dropdown {
            background: var(--sh-surface) !important;
            border-color: var(--sh-border) !important;
        }
        [data-bs-theme="dark"] .shuttle-modal .select2-results__option,
        [data-theme-mode="dark"] .shuttle-modal .select2-results__option,
        .dark-mode .shuttle-modal .select2-results__option,
        html.dark .shuttle-modal .select2-results__option {
            color: var(--sh-text) !important;
        }
        [data-bs-theme="dark"] .shuttle-modal .select2-results__option--highlighted,
        [data-theme-mode="dark"] .shuttle-modal .select2-results__option--highlighted,
        .dark-mode .shuttle-modal .select2-results__option--highlighted,
        html.dark .shuttle-modal .select2-results__option--highlighted {
            background: var(--sh-item-hover) !important;
            color: var(--sh-text) !important;
        }

        /* SortableJS state classes */
        .sortable-ghost  { opacity: 0.35; background: var(--sh-ghost-bg) !important; border-style: dashed !important; }
        .sortable-chosen { box-shadow: var(--sh-shadow); }
        .sortable-drag   { transform: rotate(-1deg); }
        .shuttle-list.drop-target {
            background: var(--sh-droptarget-bg);
            border-radius: 8px;
            box-shadow: inset 0 0 0 2px var(--sh-droptarget-bd);
        }

        .shuttle-count {
            font-size: 0.7rem; font-weight: 700; padding: 2px 8px; border-radius: 10px;
            background: var(--sh-count-bg); color: var(--sh-count-fg);
        }
        .shuttle-count.tint-warn    { background: var(--sh-count-warn-bg); color: var(--sh-count-warn-fg); }
        .shuttle-count.tint-success { background: var(--sh-count-ok-bg);   color: var(--sh-count-ok-fg); }

        .shuttle-tools-row {
            background: var(--sh-surface-3);
            border: 1px solid var(--sh-border);
            border-radius: 10px;
            padding: 12px 14px; margin-bottom: 14px;
        }
        .shuttle-tools-row .form-label {
            font-size: 0.78rem; font-weight: 600; margin-bottom: 4px;
            color: var(--sh-label);
        }
        .shuttle-tools-row .form-select,
        .shuttle-tools-row .form-control {
            height: 38px; font-size: 0.88rem;
            background: var(--sh-surface);
            color: var(--sh-text);
            border: 1px solid var(--sh-border);
        }
        .shuttle-tools-row .form-select option { background: var(--sh-surface); color: var(--sh-text); }

        @media (max-width: 768px) {
            .shuttle-list { min-height: 240px; max-height: 280px; }
        }
    </style>

    {{-- =========================================================
         BULK ASSIGN MODAL
         ========================================================= --}}
    <div class="modal fade shuttle-modal" id="bulkAssignModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-warning"><i class="bi bi-people-fill me-2"></i>تشعيب طلاب</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {{-- Tools row — only 2 filters + target-group picker + refresh icon --}}
                    <div class="shuttle-tools-row">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">نوع برنامج الطلاب</label>
                                <select id="bulkProgramTypeFilter" class="form-select form-select-solid"
                                        data-control="select2" data-placeholder="-- الكل --" data-minimum-results-for-search="-1">
                                    <option value="">الكل</option>
                                    <option value="adult">الكبار</option>
                                    <option value="kids">الأطفال</option>
                                </select>
                                <small class="text-muted fs-8 d-block mt-1">يفلتر الطلاب حسب (كبار وصغار)</small>

                            </div>
                            <div class="col-md-3">
                                <label class="form-label">
                                    <i class="bi bi-funnel text-info me-1"></i>
                                    البرنامج
                                </label>
                                <select id="bulkProgramIdFilter" class="form-select form-select-solid"
                                        data-control="select2" data-placeholder="— كل البرامج —">
                                    <option value=""></option>
                                    @foreach(($programs_with_fees ?? collect()) as $p)
                                        <option value="{{ $p->id }}">{{ $p->title }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted fs-8 d-block mt-1">يفلتر الطلاب وقائمة المجموعات الهدف معاً</small>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">المجموعة المُستهدفة *</label>
                                <select id="bulkTargetGroup" class="form-select form-select-solid"
                                        data-control="select2" data-placeholder="🔍 ابحث واختر المجموعة...">
                                    <option></option>
                                    @foreach($active_groups_for_picker ?? [] as $g)
                                        <option value="{{ $g->id }}" data-program-id="{{ $g->program_id ?? ($g->program->id ?? '') }}">
                                            {{ ($g->program->title ?? '—') }} — {{ $g->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted fs-8 d-block mt-1" id="bulkTargetGroupHint">اختيار المجموعة لا يؤثّر على قائمة الطلاب</small>
                            </div>
                            <div class="col-md-1">
                                <button type="button" id="bulkRefreshBtn" class="btn btn-icon btn-light-primary w-100"
                                        style="height:38px;" title="تحديث">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Two columns --}}
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="shuttle-column">
                                <div class="shuttle-head">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <h6 class="ttl text-primary"><i class="bi bi-person-check me-1"></i>المؤهلون</h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="shuttle-count" id="poolCount">0</span>
                                            <button type="button" id="selectAllPoolBtn" class="btn btn-sm btn-light-primary py-1 px-2" title="إضافة كل الطلاب الظاهرين للسلة">
                                                <i class="bi bi-check2-all"></i> اختر الكل
                                            </button>
                                        </div>
                                    </div>
                                    <input type="text" id="poolSearch" class="form-control" placeholder="🔍 ابحث بالاسم أو الجوال...">
                                    <label class="form-check form-switch form-check-custom form-check-solid mt-2 bg-light-warning rounded px-2 py-1 border border-warning border-dashed">
                                        <input class="form-check-input" type="checkbox" id="bulkMultiEnroll" value="1">
                                        <span class="form-check-label fw-bold text-warning fs-8 ms-2">
                                            <i class="bi bi-people-fill me-1"></i> إظهار المسجّلين في مجموعات أخرى (تشعيب متعدّد البرامج)
                                        </span>
                                    </label>
                                </div>
                                <ul class="shuttle-list" id="poolList">
                                    <li class="shuttle-empty">جاري التحميل...</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="shuttle-column">
                                <div class="shuttle-head tint-warn">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h6 class="ttl text-warning"><i class="bi bi-cart-check-fill me-1"></i>للتشعيب</h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="shuttle-count tint-warn" id="basketCount">0</span>
                                            <button type="button" id="unlockToggleBtn" class="btn btn-sm btn-icon btn-light-info" title="فتح القفل لإزالة أعضاء حاليين">
                                                <i class="bi bi-lock-fill"></i>
                                            </button>
                                            <button type="button" id="clearBasketBtn" class="btn btn-sm btn-icon btn-light-danger" title="إفراغ"><i class="bi bi-trash"></i></button>
                                        </div>
                                    </div>
                                    <small class="text-muted d-block mt-1" id="basketHint">⤵ اسحب أو انقر لإضافة</small>
                                </div>
                                <ul class="shuttle-list" id="basketList">
                                    <li class="shuttle-empty">اسحب أو انقر اسماً من اليسار</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-warning" id="bulkAssignSubmit">
                        <i class="bi bi-check2-circle me-1"></i> تشعيب المحددين
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- =========================================================
         QR GENERATION MODAL
         ========================================================= --}}
    <div class="modal fade qr-modal" id="qrGenModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-600px">
            <div class="modal-content">
                <div class="modal-header qr-modal-header py-3">
                    <h5 class="modal-title fw-bold text-white">
                        <i class="bi bi-qr-code-scan me-2" style="color:#ffcc00;"></i>
                        QR للمجموعة:
                        <span id="qrModalGroupName" class="ms-1" style="color:#ffcc00; font-style:italic;">—</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="qrGenGroupId">
                    <input type="hidden" id="qrGenCreatedByType" value="admin">
                    <div id="qrGenForm">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">صالح حتى (تاريخ + وقت) *</label>
                                <input type="datetime-local" id="qrExpiresAt" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">الاستخدامات الأقصى <small class="text-muted">(اختياري)</small></label>
                                <input type="number" id="qrMaxUses" min="1" class="form-control" placeholder="غير محدد">
                            </div>
                        </div>
                        <button type="button" id="qrGenSubmit" class="btn btn-qr-generate w-100">
                            <i class="bi bi-magic"></i>
                            <span>توليد الـ QR</span>
                        </button>
                    </div>
                    <div id="qrGenResult" class="text-center mt-4" style="display:none;">
                        <div class="p-3 bg-light rounded mb-3 d-inline-block qr-canvas-wrap" style="position:relative;">
                            <canvas id="qrGenCanvas" width="480" height="480" style="max-width:280px; width:100%; display:block;"></canvas>
                            {{-- Fancy generation overlay (multi-stage animation) --}}
                            <div id="qrGenSpinner" class="qr-gen-overlay">
                                <div class="qr-scan-frame">
                                    <div class="qr-corner tl"></div>
                                    <div class="qr-corner tr"></div>
                                    <div class="qr-corner bl"></div>
                                    <div class="qr-corner br"></div>
                                    <div class="qr-scan-line"></div>
                                </div>
                                <div class="qr-gen-text">
                                    <i class="bi bi-qr-code-scan"></i>
                                    <span id="qrGenStage">جاري الإنشاء...</span>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-light-info text-start small mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            صالح حتى: <strong id="qrGenExpires">—</strong><br>
                            رابط المسح: <a id="qrGenUrl" href="#" target="_blank" class="text-break"></a>
                        </div>
                        <div class="d-flex gap-2 justify-content-center flex-wrap">
                            <a id="qrGenDownload" href="#" download="oxford_qr.png" class="btn btn-sm btn-light-primary"><i class="bi bi-download me-1"></i> تحميل</a>
                            <a id="qrGenWhatsapp" href="#" target="_blank" class="btn btn-sm btn-light-success"><i class="bi bi-whatsapp me-1"></i> مشاركة عبر واتساب</a>
                            <button id="qrGenRegenerate" class="btn btn-sm btn-light-warning"><i class="bi bi-arrow-clockwise me-1"></i> توليد جديد</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- =========================================================
         STUDENT HISTORY MODAL (stacked above bulk-assign modal)
         ========================================================= --}}
    <div class="modal fade" id="studentHistoryModal" tabindex="-1" aria-hidden="true" style="z-index: 1080;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable mw-700px">
            <div class="modal-content">
                <div class="modal-header py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-clock-history text-warning me-2"></i>تاريخ المجموعات: <span id="historyStudentName" class="text-info"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="historyContent">
                    <div class="text-center py-5"><div class="spinner-border text-warning"></div></div>
                </div>
            </div>
        </div>
    </div>

    {{-- =========================================================
         PROMOTE MODAL
         ========================================================= --}}
    <div class="modal fade shuttle-modal" id="promoteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-info"><i class="bi bi-arrow-up-right-square-fill me-2"></i>تصعيد طلاب</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="shuttle-tools-row">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label">من مجموعة *</label>
                                <select id="promoteSourceGroup" class="form-select form-select-solid"
                                        data-control="select2" data-placeholder="🔍 ابحث واختر المصدر...">
                                    <option></option>
                                    @foreach($active_groups_for_picker ?? [] as $g)
                                        <option value="{{ $g->id }}">{{ ($g->program->title ?? '—') }} — {{ $g->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">إلى مجموعة *</label>
                                <select id="promoteTargetGroup" class="form-select form-select-solid"
                                        data-control="select2" data-placeholder="🔍 ابحث واختر الهدف...">
                                    <option></option>
                                    @foreach($active_groups_for_picker ?? [] as $g)
                                        <option value="{{ $g->id }}">{{ ($g->program->title ?? '—') }} — {{ $g->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" id="carryFeesToggle" checked>
                                    <label class="form-check-label fw-bold fs-8" for="carryFeesToggle">ترصيد المتبقي</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="shuttle-column">
                                <div class="shuttle-head">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <h6 class="ttl text-info"><i class="bi bi-people me-1"></i>المجموعة المصدر</h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="shuttle-count" id="rosterCount">0</span>
                                            <button type="button" id="selectAllRosterBtn" class="btn btn-sm btn-light-info py-1 px-2" title="إضافة كل الطلاب للتصعيد">
                                                <i class="bi bi-check2-all"></i> اختر الكل
                                            </button>
                                        </div>
                                    </div>
                                    <input type="text" id="rosterSearch" class="form-control" placeholder="🔍 ابحث ضمن المجموعة...">
                                </div>
                                <ul class="shuttle-list" id="rosterList">
                                    <li class="shuttle-empty">اختر المجموعة المصدر أولاً</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="shuttle-column">
                                <div class="shuttle-head tint-success">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h6 class="ttl text-success"><i class="bi bi-arrow-up-right me-1"></i>للتصعيد</h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="shuttle-count tint-success" id="promoteBasketCount">0</span>
                                            <button type="button" id="clearPromoteBasketBtn" class="btn btn-sm btn-icon btn-light-danger" title="إفراغ"><i class="bi bi-trash"></i></button>
                                        </div>
                                    </div>
                                    <small class="text-muted d-block mt-1">⤵ اسحب أو انقر لإضافة</small>
                                </div>
                                <ul class="shuttle-list" id="promoteBasketList">
                                    <li class="shuttle-empty">اسحب أو انقر طالباً لإضافته</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-info" id="promoteSubmit">
                        <i class="bi bi-arrow-up-right-square-fill me-1"></i> تصعيد المحددين
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <style>
        #groups_table .dropdown-menu { z-index: 1051 !important; position: fixed !important; }
        .table-responsive { overflow: visible !important; }
    </style>
    <script>
        var table;
        var tableId = 'groups_table';
        var columns = [{
                data: "id",
                name: "id",
                orderable: false,
                searchable: false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: "checkbox",
                name: "checkbox",
                orderable: false,
                searchable: false
            },
            {
                data: "name",
                name: "name",
                orderable: true
            },
            {
                data: "teacher_name",
                name: "teacher_name"
            },
            {
                data: "program_name",
                name: "program_name"
            },
            {
                data: "studens_no",
                name: "studens_no",
                className: "text-center"
            },
            {
                data: "code",
                name: "code",
                className: "text-center"
            },
            {
                data: "certifcate",
                name: "certifcate",
                className: "text-center"
            },
            {
                data: "status",
                name: "status",
                orderable: true,
                searchable: false
            },
            {
                data: "actions",
                name: "actions",
                orderable: false,
                searchable: false,
                className: "text-center"
            }
        ];

        var filterFields = ['#title', '#activeG', '#program_id', '#teacher_id', '#student_name', '#is_today', '#date_from', '#date_to', '#date_id'];

        $(document).ready(function() {
            // Select2 with Images Utility
            const formatSelect2Image = (item) => {
                if (!item.id || !$(item.element).data('kt-select2-image')) return item.text;
                var imgUrl = $(item.element).data('kt-select2-image');
                var span = $("<span><img src='" + imgUrl + "' class='rounded-circle me-2' style='height:20px;width:20px;object-fit:cover;'/>" + item.text + "</span>");
                return span;
            };

            $('#teacher_id, #date_id').select2({
                templateResult: formatSelect2Image,
                templateSelection: formatSelect2Image
            });

            $('#select-all').on('click', function() {
                $('.checkboxes').prop('checked', this.checked);
            });

            $('#filter_form').on('submit', function(e) {
                e.preventDefault();
                table.draw();
            });

            $(document).on('click', '#reset_button', function(e) {
                e.preventDefault();
                $('#filter_form')[0].reset();
                $('#filter_form select').val('').trigger('change');
                table.draw();
            });

            // Live filters
            $('#filter_form input').on('keyup change', function() {
                table.draw();
            });

            $('#filter_form select').on('change', function() {
                table.draw();
            });


            $('#sms').on('click', function() {
                var selectedGroups = [];
                $(".checkboxes:checked").each(function() {
                    selectedGroups.push($(this).data("id"));
                });
                if (selectedGroups.length > 0) {
                    Swal.fire({
                        title: 'إرسال رسالة SMS للمجموعات المحددة',
                        input: 'textarea',
                        inputPlaceholder: 'اكتب نص الرسالة هنا...',
                        showCancelButton: true,
                        confirmButtonText: 'إرسال',
                        cancelButtonText: 'إلغاء',
                        customClass: {
                            confirmButton: "btn btn-primary",
                            cancelButton: "btn btn-light"
                        },
                        buttonsStyling: false,
                        showLoaderOnConfirm: true,
                        preConfirm: (message) => {
                            if (!message) {
                                Swal.showValidationMessage('يجب إدخال نص الرسالة');
                            }
                            return $.ajax({
                                type: 'POST',
                                url: '{{ route('send.groups.sms') }}',
                                data: {
                                    message: message,
                                    id: selectedGroups,
                                    _token: '{{ csrf_token() }}'
                                }
                            });
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                text: 'تم الإرسال بنجاح!',
                                icon: 'success',
                                buttonsStyling: false,
                                confirmButtonText: 'حسناً',
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'تنبيه!',
                        text: 'يجب اختيار مجموعة واحدة على الأقل!',
                        icon: 'warning',
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }
            });

            // Bulk Email logic using EmailCampaign Component
            $('#CEmail').on('click', function() {
                var selectedGroups = [];
                $(".checkboxes:checked").each(function() {
                    selectedGroups.push($(this).data("id"));
                });
                if (selectedGroups.length === 0) {
                    Swal.fire({ 
                        text: 'يجب اختيار مجموعة واحدة على الأقل!', 
                        icon: 'warning', 
                        buttonsStyling: false, 
                        confirmButtonText: 'حسناً', 
                        customClass: { confirmButton: 'btn btn-primary' } 
                    });
                    return;
                }
                
                if (window.EmailComposer) {
                    window.EmailComposer.send({
                        type: 'bulk',
                        recipients: selectedGroups,
                        url: '{{ route("groups.send.CEmail") }}',
                        title: 'رسالة بريد إلكتروني لطلاب المجموعات المحددة'
                    });
                } else {
                    Swal.fire('خطأ', 'مكون إرسال الإيميلات غير متوفر.', 'error');
                }
            });
        });

        function showGroupModal(id) {
            fetchModalContent('{{ route('groups.details') }}', { id: id });
        }

        function showTeacherModal(id) {
            fetchModalContent('{{ route('teachers.details') }}', { id: id });
        }

        function showProgramModal(id) {
            fetchModalContent('{{ route('programs.details.post') }}', { id: id });
        }

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

        function generatekey(id) {
            let randomNumber = Math.random().toString(36).substring(2, 11);
            Swal.fire({
                title: 'توليد كود الانضمام للمجموعة',
                html: '<div class="fv-row mb-7 text-start">' +
                    '<label class="fs-6 fw-semibold mb-2">كود العشوائي</label>' +
                    '<input id="number2" class="form-control form-control-solid mb-3" value ="' + randomNumber +
                    '" readonly>' +
                    '</div>' +
                    '<div class="fv-row text-start">' +
                    '<label class="fs-6 fw-semibold mb-2">تاريخ انتهاء الكود</label>' +
                    '<input id="datetime2" type="datetime-local" class="form-control form-control-solid" placeholder="تاريخ انتهاء الكود">' +
                    '</div>',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: 'توليد وإرسال',
                denyButtonText: 'توليد وحفظ',
                cancelButtonText: 'إلغاء',
                customClass: {
                    confirmButton: "btn btn-success",
                    denyButton: "btn btn-primary",
                    cancelButton: "btn btn-light"
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed || result.isDenied) {
                    let randomKey = $("#number2").val();
                    let dateTime = $("#datetime2").val();
                    let sendEmail = result.isConfirmed ? 1 : 0;
                    
                    $.ajax({
                        url: '{{ route('groups.add.code') }}',
                        method: 'post',
                        data: {
                            'id': id,
                            'code': randomKey,
                            'code_scope': dateTime,
                            'send_email': sendEmail,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function(response) {
                        Swal.fire({
                            text: response.message,
                            icon: 'success',
                            buttonsStyling: false,
                            confirmButtonText: 'حسناً',
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        }).then(() => {
                            if (response.campaign_id) {
                                if (window.EmailCampaignMonitor) {
                                    window.EmailCampaignMonitor.start(response.campaign_id, response.total_recipients, response.redirect_url);
                                } else {
                                    window.location.href = response.redirect_url;
                                }
                            }
                            table.ajax.reload(null, false);
                        });
                    });
                }
            });
        }

        function generateCertificateCode(rowId) {
            $.ajax({
                type: "POST",
                url: "{{ url('admin/groups/generate-certificate-code') }}/" + rowId,
                data: {
                    row_id: rowId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        text: response.message,
                        icon: 'success',
                        buttonsStyling: false,
                        confirmButtonText: 'حسناً',
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }
            });
        }
    </script>

    {{-- SortableJS (lightweight ~25KB) — enables drag & drop between lists --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

    {{-- =========================================================
         BULK ASSIGN  +  PROMOTE — shuttle logic (click + drag&drop)
         ========================================================= --}}
    <script>
    (function() {
        const CSRF         = '{{ csrf_token() }}';
        const URL_ELIGIBLE = '{{ route("groups.eligible_students") }}';
        const URL_ROSTER   = '{{ url("admin/groups/roster") }}';
        const URL_ASSIGN   = '{{ route("groups.bulk_assign") }}';
        const URL_PROMOTE  = '{{ route("groups.bulk_promote") }}';

        function escapeHtml(s) {
            return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        }

        /* ===== Spinner helpers — call .ajaxBusy(true, 'label') on any container ===== */
        window.ajaxBusy = function (hostSel, on, label) {
            const $host = $(hostSel);
            if (!$host.length) return;
            $host.addClass('ajax-host');
            $host.find('> .ajax-spinner-overlay').remove();
            if (on) {
                $host.append(
                    '<div class="ajax-spinner-overlay">' +
                    '  <div class="spinner-border text-warning"></div>' +
                    (label ? '<small>' + label + '</small>' : '') +
                    '</div>'
                );
            }
        };
        // Tiny inline spinner inside an input wrapper
        window.inputBusy = function (wrapperSel, on) {
            const $w = $(wrapperSel);
            $w.find('> .input-spinner').remove();
            if (on) $w.append('<span class="input-spinner"></span>');
        };

        // Compact shuttle card markup
        function itemHTML(s, opts = {}) {
            const pTag  = s.program_type === 'kids' ? '<span class="tag kids">KIDS</span>' : '<span class="tag adult">ADULT</span>';
            const lvl   = s.level ? `<span class="tag level">${escapeHtml(s.level)}</span>` : '';
            const mob   = s.mobile ? escapeHtml(s.mobile) : '';
            const paid  = (typeof s.total_paid === 'number' && s.total_paid > 0)
                          ? ` · 💰 ${s.total_paid.toFixed(0)}` : '';
            let statusTag = '';
            if (opts.inBasket) {
                statusTag = s.existing
                    ? '<span class="tag exists" title="موجود مسبقاً في المجموعة"><i class="bi bi-check-lg"></i> موجود</span>'
                    : '<span class="tag new">جديد</span>';
            }
            const locked = s.existing ? 'locked' : '';
            const lockedAttr = s.existing ? 'data-locked="1"' : '';
            const historyBtn = `<button type="button" class="shuttle-history-btn" data-history-id="${s.id}" data-history-name="${escapeHtml(s.name)}" title="عرض كل المجموعات السابقة"><i class="bi bi-clock-history"></i></button>`;

            // Business-rule warnings (relative to the chosen target group)
            let warnTags = '';
            if (s.has_conflict) {
                warnTags += `<span class="tag" style="background:#fee2e2;color:#b91c1c;font-weight:700;" title="تعارض في موعد المحاضرات"><i class="bi bi-clock-history"></i> تعارض: ${escapeHtml(s.conflict_with || '')}</span>`;
            }
            if (typeof s.outstanding === 'number' && s.outstanding > 0.009) {
                warnTags += `<span class="tag" style="background:#fee2e2;color:#b91c1c;font-weight:700;" title="عليه رسوم مستحقة سابقة"><i class="bi bi-cash-coin"></i> مستحق ${s.outstanding.toFixed(0)}</span>`;
            }
            const blocked   = s.blocked === true;
            const blkStyle  = blocked ? 'style="border-inline-start:4px solid #ef4444;background:#fff5f5;"' : '';
            const blkTitle  = blocked ? 'title="هذا الطالب مخالف للشروط (تعارض زمني أو رسوم مستحقة) وسيُمنع تشعيبه"' : '';

            return `
                <li class="shuttle-item ${locked}" data-id="${s.id}" ${lockedAttr} ${blkStyle} ${blkTitle}>
                    <i class="bi bi-grip-vertical grip"></i>
                    <img src="${s.avatar}" class="avatar">
                    <div class="meta">
                        <div class="name">${escapeHtml(s.name)} ${blocked ? '<i class="bi bi-exclamation-triangle-fill text-danger"></i>' : ''}</div>
                        <div class="sub">${mob}${paid}</div>
                    </div>
                    <div class="tags">${statusTag}${pTag}${lvl}${warnTags}</div>
                    ${historyBtn}
                </li>`;
        }
        function emptyHTML(msg) { return `<li class="shuttle-empty">${msg}</li>`; }

        // Generic shuttle controller — reused for both modals
        function makeShuttle(opts) {
            const state = { pool: [], basket: [] };
            const $pool   = $(opts.poolSel);
            const $basket = $(opts.basketSel);
            const $poolCount   = $(opts.poolCountSel);
            const $basketCount = $(opts.basketCountSel);
            // Lock enforcement is dynamic — caller provides a getter (default: always enforced)
            const isLocked = () => (typeof opts.isLockEnforced === 'function' ? opts.isLockEnforced() : true);

            function filtered(list, exclude, q) {
                q = (q || '').trim().toLowerCase();
                return list.filter(s => {
                    if (exclude.some(x => x.id === s.id)) return false;
                    if (!q) return true;
                    return (s.name||'').toLowerCase().includes(q)
                        || (s.mobile||'').toLowerCase().includes(q)
                        || (s.email||'').toLowerCase().includes(q);
                });
            }

            function renderPool() {
                const q = $(opts.searchSel).val() || '';
                const rows = filtered(state.pool, state.basket, q);
                $poolCount.text(rows.length);
                $pool.html(rows.length
                    ? rows.map(s => itemHTML(s)).join('')
                    : emptyHTML(opts.emptyPoolMsg || 'لا يوجد طلاب مطابقون'));
            }
            function renderBasket() {
                const newCount   = state.basket.filter(s => !s.existing).length;
                const existCount = state.basket.length - newCount;
                $basketCount.text(existCount > 0 ? `+${newCount} / ${state.basket.length}` : state.basket.length);
                $basketCount.attr('title', existCount > 0
                    ? `${newCount} طالب جديد · ${existCount} موجود مسبقاً`
                    : `${state.basket.length} طالب محدد`);
                $basket.html(state.basket.length
                    ? state.basket.map(s => itemHTML(s, { inBasket: true })).join('')
                    : emptyHTML(opts.emptyBasketMsg || 'اسحب أو انقر لإضافة طالب'));
                applyUnlockAttrs();
            }

            // Toggle data-locked on rendered items based on current enforcement
            function applyUnlockAttrs() {
                if (isLocked()) {
                    $basket.find('.shuttle-item.locked').attr('data-locked', '1');
                } else {
                    $basket.find('.shuttle-item.locked').removeAttr('data-locked');
                }
            }

            // ----- Click handlers (both lists) -----
            $pool.on('click', '.shuttle-item', function (e) {
                // Ignore clicks coming from the history button (or any interactive child)
                if ($(e.target).closest('.shuttle-history-btn').length) return;
                const id = +$(this).data('id');
                const s  = state.pool.find(x => x.id === id);
                if (s && !state.basket.some(b => b.id === id)) {
                    state.basket.push(s);
                    renderBasket(); renderPool();
                }
            });
            $basket.on('click', '.shuttle-item', function (e) {
                if ($(e.target).closest('.shuttle-history-btn').length) return;
                const id = +$(this).data('id');
                const item = state.basket.find(x => x.id === id);
                if (!item) return;
                if (item.existing && isLocked()) return;

                state.basket = state.basket.filter(x => x.id !== id);
                if (item.existing) {
                    state.pool.unshift({ ...item, existing: false });
                    if (typeof opts.onUnassignExisting === 'function') opts.onUnassignExisting(id);
                }
                renderBasket(); renderPool();
            });

            // ----- SortableJS (drag & drop) -----
            const sharedGroup = 'shuttle-' + Math.random().toString(36).slice(2, 8);

            const poolSort = Sortable.create($pool[0], {
                group: { name: sharedGroup, pull: true, put: true },
                sort: false,
                animation: 160,
                // filter is dynamic — checked per drag attempt
                filter(evt, item) {
                    // Skip drag if user clicked the history button or another interactive child
                    if (evt.target && evt.target.closest && evt.target.closest('.shuttle-history-btn')) return true;
                    return isLocked() && item.dataset.locked === '1';
                },
                preventOnFilter: true,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                onAdd(evt) {
                    const id   = +$(evt.item).data('id');
                    const item = state.basket.find(x => x.id === id);
                    if (!item) { renderBasket(); renderPool(); return; }
                    // Dropping a locked item into pool is only allowed when lock is OFF
                    if (item.existing && isLocked()) { renderBasket(); renderPool(); return; }

                    state.basket = state.basket.filter(x => x.id !== id);
                    if (item.existing) {
                        state.pool.unshift({ ...item, existing: false });
                        if (typeof opts.onUnassignExisting === 'function') opts.onUnassignExisting(id);
                    }
                    renderBasket(); renderPool();
                },
            });

            const basketSort = Sortable.create($basket[0], {
                group: { name: sharedGroup, pull: true, put: true },
                sort: true,
                animation: 160,
                filter(evt, item) {
                    // Skip drag if user clicked the history button or another interactive child
                    if (evt.target && evt.target.closest && evt.target.closest('.shuttle-history-btn')) return true;
                    return isLocked() && item.dataset.locked === '1';
                },
                preventOnFilter: true,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                onMove(evt) {
                    if (isLocked() && $(evt.dragged).data('locked')) return false;
                },
                onAdd(evt) {
                    const id = +$(evt.item).data('id');
                    const s  = state.pool.find(x => x.id === id);
                    if (s && !state.basket.some(b => b.id === id)) state.basket.push(s);
                    renderBasket(); renderPool();
                },
            });

            // Public surface
            return {
                state,
                setPool(arr)         { state.pool = arr; renderPool(); },
                resetBasket()        { state.basket = []; renderBasket(); renderPool(); },
                preloadExisting(arr) {
                    state.basket = arr.map(s => ({ ...s, existing: true }));
                    renderBasket(); renderPool();
                },
                /** Push all currently-visible pool rows into the basket */
                selectAllVisible() {
                    const q = $(opts.searchSel).val() || '';
                    const rows = filtered(state.pool, state.basket, q);
                    rows.forEach(s => { if (!state.basket.some(b => b.id === s.id)) state.basket.push(s); });
                    renderBasket(); renderPool();
                },
                /** Force re-evaluation of lock visuals (no full re-render) */
                syncLockState()      { applyUnlockAttrs(); },
                getNewIds()          { return state.basket.filter(s => !s.existing).map(s => s.id); },
                getBasketIds()       { return state.basket.map(s => s.id); },
                getExistingIds()     { return state.basket.filter(s =>  s.existing).map(s => s.id); },
                refresh()            { renderPool(); renderBasket(); },
                searchUpdated()      { renderPool(); },
            };
        }

        /* -------------------- BULK ASSIGN -------------------- */
        let unlockMode      = false;     // declared early so closures below capture it
        let removedExisting = [];

        const bulk = makeShuttle({
            poolSel: '#poolList', basketSel: '#basketList',
            poolCountSel: '#poolCount', basketCountSel: '#basketCount',
            searchSel: '#poolSearch',
            emptyPoolMsg: 'لا يوجد طلاب مؤهلون مطابقون',
            emptyBasketMsg: 'اسحب أو انقر لإضافة طالب',
            isLockEnforced: () => !unlockMode,    // dynamic lock state
            onUnassignExisting: (id) => {
                if (!removedExisting.includes(id)) removedExisting.push(id);
            },
        });

        function loadEligible() {
            ajaxBusy('#poolList', true, 'جاري جلب الطلاب المؤهلين...');
            $.get(URL_ELIGIBLE, {
                search:           $('#poolSearch').val() || '',
                program_type:     $('#bulkProgramTypeFilter').val() || '',
                program_id:       $('#bulkProgramIdFilter').val() || '',
                exclude_ids:      bulk.getBasketIds().join(','),
                // Pass the chosen target group so the server flags time-conflict / outstanding students
                target_group_id:  $('#bulkTargetGroup').val() || '',
                // Multi-enroll mode: also list students already seated in other active groups
                include_enrolled: $('#bulkMultiEnroll').is(':checked') ? 1 : 0,
            }, function (res) {
                if (res.success) {
                    bulk.setPool(res.students);
                    if (!res.students || !res.students.length) {
                        $('#poolList').append('<li class="shuttle-empty mt-0 pt-0"><a href="#" class="btn btn-sm btn-light-info mt-3" onclick="window.showEligibilityDiagnostic(); return false;"><i class="bi bi-question-circle me-1"></i> لماذا لا يظهر طلاب؟</a></li>');
                    }
                }
            }).fail(() => $('#poolList').html('<li class="shuttle-empty text-danger">تعذّر تحميل القائمة</li>'))
              .always(() => ajaxBusy('#poolList', false));
        }

        // Show a stats popup explaining why students might be excluded
        window.showEligibilityDiagnostic = function() {
            $.get('{{ route("groups.eligibility_diagnose") }}', function(res) {
                if (!res.success) return;
                const s = res.stats;
                const html = `
                    <div class="text-start" dir="rtl">
                        <p class="text-muted mb-3">القائمة تعرض فقط الطلاب الذين يحققون <b>كل</b> الشروط التالية. هذه إحصائية بمن يُستبعد ولماذا:</p>
                        <ul class="list-group list-group-flush text-end">
                            <li class="list-group-item d-flex justify-content-between"><span><i class="bi bi-person-x text-danger me-1"></i> طلاب غير مفعّلين (status≠1)</span><span class="badge bg-secondary">${s.inactive}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span><i class="bi bi-people-fill text-info me-1"></i> طلاب مشعّبون حالياً في مجموعات فعّالة</span><span class="badge bg-info">${s.in_active_group}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span><i class="bi bi-hourglass-split text-warning me-1"></i> طلاب لديهم دفعات قيد التدقيق</span><span class="badge bg-warning text-dark">${s.has_pending_fees}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span><i class="bi bi-clipboard-x text-danger me-1"></i> اختبار تحديد مستوى غير مُقيَّم</span><span class="badge bg-danger">${s.ungraded_placement}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span><i class="bi bi-cash-coin text-danger me-1"></i> بدون دفعة مؤكدة</span><span class="badge bg-danger">${s.no_verified_payment}</span></li>
                        </ul>
                        <div class="alert alert-info mt-3 mb-0 small">
                            <i class="bi bi-lightbulb me-1"></i>
                            تذكّر: لو أزلت طالباً من السلة بـ 🔓 ولم تضغط <b>"تشعيب المحددين"</b>، فالإزالة لم تُحفظ بعد.
                        </div>
                    </div>`;
                Swal.fire({ title: 'لماذا الطلاب مستبعدون؟', html, width: 600, confirmButtonText: 'فهمت' });
            });
        };

        // When target group changes, preload its current roster as locked basket items
        function loadGroupRosterForBasket() {
            const gid = $('#bulkTargetGroup').val();
            if (!gid) { bulk.resetBasket(); loadEligible(); return; }
            ajaxBusy('#basketList', true, 'تحميل أعضاء المجموعة...');
            $.get(URL_ROSTER + '/' + gid, function (res) {
                if (res.success) {
                    bulk.preloadExisting(res.students || []);
                } else {
                    bulk.resetBasket();
                }
                loadEligible();
            }).always(() => ajaxBusy('#basketList', false))
              .fail(() => {
                bulk.resetBasket();
                loadEligible();
            });
        }

        // Init Select2 inside the modal (with dropdown anchored to modal so it doesn't escape)
        function initShuttleSelects($modal) {
            $modal.find('select[data-control="select2"]').each(function () {
                const $el = $(this);
                if ($el.hasClass('select2-hidden-accessible')) $el.select2('destroy');
                $el.select2({
                    dropdownParent: $modal,
                    placeholder: $el.data('placeholder') || '— اختر —',
                    allowClear: true,
                    width: '100%',
                    language: { noResults: () => 'لا توجد نتائج', searching: () => 'جاري البحث...' },
                    minimumResultsForSearch: $el.data('minimum-results-for-search') ?? 0,
                });
            });
        }

        $('#bulkAssignModal').on('shown.bs.modal', function () {
            initShuttleSelects($(this));
            unlockMode = false;
            removedExisting = [];
            reflectUnlockUI();
            bulk.resetBasket();
            const gid = $('#bulkTargetGroup').val();
            if (gid) loadGroupRosterForBasket(); else loadEligible();
        });

        // Warn before closing if there are unsaved changes
        let allowBulkClose = false;
        $('#bulkAssignModal').on('hide.bs.modal', function (e) {
            if (allowBulkClose) return; // programmatic close (e.g. after save)
            const hasChanges = bulk.getNewIds().length > 0 || removedExisting.length > 0;
            if (!hasChanges) return;
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'لديك تغييرات لم تُحفظ',
                html: 'هل تريد إغلاق المودال وفقدان التغييرات؟<br><small class="text-muted">انقر "تشعيب المحددين" أولاً لتثبيت الإضافات والإزالات.</small>',
                showCancelButton: true,
                confirmButtonText: 'نعم، أغلق وافقد التغييرات',
                cancelButtonText: 'لا، عُد للمودال',
                confirmButtonColor: '#dc3545',
            }).then(res => {
                if (res.isConfirmed) {
                    allowBulkClose = true;
                    $('#bulkAssignModal').modal('hide');
                }
            });
        });
        // After successful submit, allow programmatic close
        function bulkCloseProgrammatic() {
            allowBulkClose = true;
            $('#bulkAssignModal').modal('hide');
            setTimeout(() => { allowBulkClose = false; }, 500);
        }

        // Select2 fires change properly so this still works
        $('#bulkTargetGroup').on('change', loadGroupRosterForBasket);
        $('#bulkRefreshBtn').on('click', loadEligible);
        $('#bulkProgramTypeFilter').on('change', loadEligible);

        // ===== Unified program filter: drives BOTH students AND target-group dropdown =====
        // Cache the full group option list once at modal init (used by the filter to rebuild).
        const fullGroupOptions = (function () {
            const $target = $('#bulkTargetGroup');
            const arr = [];
            $target.find('option').each(function () {
                if (!$(this).val()) return;
                arr.push({
                    value: $(this).val(),
                    label: $(this).text().trim(),
                    programId: String($(this).data('program-id') || ''),
                });
            });
            return arr;
        })();

        function applyProgramFilterToTargetGroups() {
            const pid = String($('#bulkProgramIdFilter').val() || '');
            const $target = $('#bulkTargetGroup');
            const prevVal = $target.val();

            let html = '<option></option>';
            const visible = fullGroupOptions.filter(o => !pid || o.programId === pid);
            visible.forEach(o => {
                html += `<option value="${o.value}" data-program-id="${o.programId}">${$('<div/>').text(o.label).html()}</option>`;
            });
            $target.html(html);

            // Keep selection if still visible
            if (prevVal && visible.some(o => o.value === prevVal)) {
                $target.val(prevVal);
            } else if (prevVal) {
                // Selected group no longer matches → clear it & reset basket
                $target.val('');
                removedExisting = [];
                unlockMode = false;
                reflectUnlockUI();
                bulk.resetBasket();
            }

            if ($target.hasClass('select2-hidden-accessible')) {
                $target.trigger('change.select2');
            }

            $('#bulkTargetGroupHint').text(pid
                ? `${visible.length} مجموعة ضمن البرنامج المختار · اختيار المجموعة لا يؤثّر على الطلاب`
                : `${visible.length} مجموعة · اختيار المجموعة لا يؤثّر على الطلاب`);
        }

        // Program filter drives: (1) student eligibility query, (2) target-group dropdown options
        $('#bulkProgramIdFilter').on('change', function () {
            applyProgramFilterToTargetGroups();
            loadEligible();
        });

        // ============== QR generation ==============
        window.openQrGenModal = function (groupId, createdByType, groupName) {
            $('#qrGenGroupId').val(groupId);
            $('#qrGenCreatedByType').val(createdByType || 'admin');
            // Header title: show the actual group name (passed in by the trigger button)
            $('#qrModalGroupName').text(groupName || '—');
            $('#qrGenForm').show();
            $('#qrGenResult').hide();
            $('#qrExpiresAt').val('');
            $('#qrMaxUses').val('');
            const def = new Date(Date.now() + 24*60*60*1000);
            const pad = n => String(n).padStart(2,'0');
            $('#qrExpiresAt').val(`${def.getFullYear()}-${pad(def.getMonth()+1)}-${pad(def.getDate())}T${pad(def.getHours())}:${pad(def.getMinutes())}`);
            $('#qrGenModal').modal('show');
        };

        // Cycle the loading message through stages to give the operation a sense of progress.
        function startQrGenStageCycle() {
            const stages = [
                'يرسم رمز التشعيب...',
                'يُضيف الحماية...',
                'يضع شعار Oxford...',
                'يُجهّز للتنزيل...',
            ];
            let i = 0;
            const $el = $('#qrGenStage');
            $el.text(stages[0]);
            window._qrStageTimer = setInterval(() => {
                i = (i + 1) % stages.length;
                $el.fadeOut(150, function () { $(this).text(stages[i]).fadeIn(150); });
            }, 900);
        }
        function stopQrGenStageCycle() {
            if (window._qrStageTimer) { clearInterval(window._qrStageTimer); window._qrStageTimer = null; }
        }
        // Compose the QR + Oxford logo on a single canvas.
        // QR is generated with ECC=H (30% redundancy) so a small centered logo
        // doesn't break the scan as long as it covers ≤ ~20% of QR area.
        function composeQrWithLogo(qrUrl, logoUrl) {
            return new Promise((resolve, reject) => {
                const canvas = document.getElementById('qrGenCanvas');
                const ctx = canvas.getContext('2d');
                const size = 480;
                canvas.width = size; canvas.height = size;
                ctx.clearRect(0, 0, size, size);

                const qrImg = new Image();
                qrImg.crossOrigin = 'anonymous';
                qrImg.onload = () => {
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, size, size);
                    ctx.drawImage(qrImg, 0, 0, size, size);

                    const logoImg = new Image();
                    // Same origin → no CORS issue
                    logoImg.onload = () => {
                        const logoBoxRatio = 0.20;   // 20% of QR side — safe under ECC=H
                        const padRatio     = 0.04;   // outer white padding around the logo
                        const radiusRatio  = 0.12;   // rounded-corner radius (% of logoBox)

                        const logoBox = size * logoBoxRatio;
                        const pad     = size * padRatio;
                        const boxSize = logoBox + pad * 2;
                        const x = (size - boxSize) / 2;
                        const y = (size - boxSize) / 2;
                        const r = boxSize * radiusRatio;

                        // White rounded-rect background behind the logo
                        ctx.fillStyle = '#ffffff';
                        ctx.beginPath();
                        ctx.moveTo(x + r, y);
                        ctx.lineTo(x + boxSize - r, y);
                        ctx.quadraticCurveTo(x + boxSize, y, x + boxSize, y + r);
                        ctx.lineTo(x + boxSize, y + boxSize - r);
                        ctx.quadraticCurveTo(x + boxSize, y + boxSize, x + boxSize - r, y + boxSize);
                        ctx.lineTo(x + r, y + boxSize);
                        ctx.quadraticCurveTo(x, y + boxSize, x, y + boxSize - r);
                        ctx.lineTo(x, y + r);
                        ctx.quadraticCurveTo(x, y, x + r, y);
                        ctx.closePath();
                        ctx.fill();
                        // Thin navy border for branded feel
                        ctx.strokeStyle = '#003366';
                        ctx.lineWidth = 2;
                        ctx.stroke();

                        // Logo (preserve aspect ratio, fit inside logoBox)
                        const ratio = logoImg.width / logoImg.height;
                        let lw = logoBox, lh = logoBox;
                        if (ratio > 1) { lh = logoBox / ratio; } else { lw = logoBox * ratio; }
                        const lx = (size - lw) / 2;
                        const ly = (size - lh) / 2;
                        ctx.drawImage(logoImg, lx, ly, lw, lh);

                        resolve(canvas.toDataURL('image/png'));
                    };
                    logoImg.onerror = () => resolve(canvas.toDataURL('image/png')); // QR-only fallback
                    logoImg.src = logoUrl;
                };
                qrImg.onerror = () => reject(new Error('فشل تحميل صورة QR'));
                qrImg.src = qrUrl;
            });
        }

        $('#qrGenSubmit, #qrGenRegenerate').on('click', function () {
            const groupId = $('#qrGenGroupId').val();
            const expires = $('#qrExpiresAt').val();
            const maxUses = $('#qrMaxUses').val();
            if (!expires) { Swal.fire({icon:'warning', title:'حدد تاريخ ووقت انتهاء الصلاحية'}); return; }
            const btn = $('#qrGenSubmit'); btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جاري التوليد...');
            ajaxBusy('#qrGenForm', true, 'جاري إنشاء الـ QR Code...');
            $.post('{{ route("groups.qr.generate") }}', {
                _token: CSRF,
                group_id: groupId,
                expires_at: expires.replace('T',' ') + ':00',
                max_uses:  maxUses || null,
                created_by_type: $('#qrGenCreatedByType').val(),
            }, function (res) {
                if (res.status !== 'success') {
                    Swal.fire({icon:'error', title:'خطأ', text: res.message || 'فشل التوليد'});
                    return;
                }
                // Show result frame + animated overlay while canvas paints
                $('#qrGenForm').hide();
                $('#qrGenResult').show();
                $('#qrGenSpinner').show();
                startQrGenStageCycle();
                $('#qrGenUrl').attr('href', res.qr_url).text(res.qr_url);
                $('#qrGenExpires').text(res.expires);

                composeQrWithLogo(res.qr_image, res.logo_url)
                    .then(dataUrl => {
                        // Hold the animation briefly so the user sees the stages complete
                        setTimeout(() => {
                            stopQrGenStageCycle();
                            $('#qrGenSpinner').fadeOut(280);
                        }, 1200);
                        $('#qrGenDownload').attr('href', dataUrl);
                        const waText = encodeURIComponent('انضم لمجموعة ' + (res.group.name||'') + ' عبر هذا الرمز:\n' + res.qr_url);
                        $('#qrGenWhatsapp').attr('href', 'https://wa.me/?text=' + waText);
                    })
                    .catch(err => {
                        stopQrGenStageCycle();
                        $('#qrGenSpinner').hide();
                        Swal.fire({icon:'warning', title:'تنبيه', text: 'تم توليد الرمز لكن تعذّر تركيب الشعار: ' + err.message});
                    });
            }).fail(xhr => Swal.fire({icon:'error', title:'خطأ', text: xhr.responseJSON?.message || 'حدث خطأ'}))
              .always(() => {
                  btn.prop('disabled', false).html('<i class="bi bi-magic me-1"></i> توليد الـ QR');
                  ajaxBusy('#qrGenForm', false);
              });
        });

        // History button on each student row
        $(document).on('click', '.shuttle-history-btn', function (e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            const sid  = +$(this).data('history-id');
            const name = $(this).data('history-name') || 'الطالب';
            $('#historyStudentName').text(name);
            $('#historyContent').html('');
            $('#studentHistoryModal').modal('show');
            ajaxBusy('#historyContent', true, 'جاري جلب التاريخ...');
            $.get('{{ url("admin/groups/student-history") }}/' + sid, function (res) {
                if (!res.success) {
                    $('#historyContent').html('<div class="alert alert-danger">تعذّر جلب التاريخ</div>');
                    return;
                }
                if (!res.history.length) {
                    $('#historyContent').html('<div class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i> لم ينضم لأي مجموعة بعد</div>');
                    return;
                }
                let html = '<div class="table-responsive"><table class="table table-row-bordered fs-7 align-middle">';
                html += '<thead class="bg-light"><tr class="fw-bold text-muted"><th>المجموعة / البرنامج</th><th>المدرس</th><th>المدة</th><th class="text-center">الحالة</th></tr></thead><tbody>';
                res.history.forEach(h => {
                    const groupActive = (h.group_status == 1);
                    const stillIn = h.left_at === null;
                    const statusBadge = !stillIn
                        ? '<span class="badge badge-light-danger fw-bold"><i class="bi bi-box-arrow-right me-1"></i>غادر</span>'
                        : (groupActive
                            ? '<span class="badge badge-light-success fw-bold"><i class="bi bi-check-circle me-1"></i>عضو فعّال</span>'
                            : '<span class="badge badge-light-secondary fw-bold"><i class="bi bi-archive me-1"></i>مجموعة مغلقة</span>');
                    const joined = h.joined_at ? new Date(h.joined_at).toLocaleDateString() : '—';
                    const left   = h.left_at   ? new Date(h.left_at).toLocaleDateString()   : '—';
                    html += `<tr>
                        <td>
                            <div class="fw-bold text-dark">${escapeHtml(h.group_name || '—')}</div>
                            <div class="text-muted fs-8">${escapeHtml(h.program_title || '')}</div>
                        </td>
                        <td><i class="bi bi-person-badge me-1 text-info"></i>${escapeHtml(h.teacher_name || '—')}</td>
                        <td class="fs-8">
                            <div><i class="bi bi-arrow-down-circle text-success"></i> ${joined}</div>
                            <div><i class="bi bi-arrow-up-circle text-danger"></i> ${left}</div>
                        </td>
                        <td class="text-center">${statusBadge}</td>
                    </tr>`;
                });
                html += '</tbody></table></div>';
                $('#historyContent').html(html);
            }).fail(() => $('#historyContent').html('<div class="alert alert-danger">تعذّر الاتصال بالخادم</div>'))
              .always(() => ajaxBusy('#historyContent', false));
        });
        $('#poolSearch').on('input', () => bulk.searchUpdated());
        // Re-fetch the eligible pool when toggling multi-enroll mode
        $('#bulkMultiEnroll').on('change', function () { loadEligible(); });
        // "Clear" clears only NEW picks (keep locked existing members)
        $('#clearBasketBtn').on('click', () => {
            const keepExisting = bulk.state.basket.filter(s => s.existing);
            bulk.state.basket = keepExisting;
            bulk.refresh();
        });

        /* -------- Unlock toggle + pending-changes indicator -------- */
        function reflectUnlockUI() {
            $('#unlockToggleBtn').toggleClass('active', unlockMode);
            $('#basketList').toggleClass('unlock-mode', unlockMode);
            $('#basketHint').text(unlockMode
                ? '🔓 يمكنك نقل أو إزالة الأعضاء الحاليين الآن — احفظ لتثبيت التغييرات'
                : '⤵ اسحب أو انقر لإضافة');
            bulk.syncLockState();
            updateSaveLabel();
        }

        // Dynamic save button label: shows +N (additions) and -M (removals)
        function updateSaveLabel() {
            const adds    = bulk.getNewIds().length;
            const removes = removedExisting.length;
            const $btn = $('#bulkAssignSubmit');
            let label = '<i class="bi bi-check2-circle me-1"></i> ';
            if (!adds && !removes) {
                label += 'تشعيب المحددين';
                $btn.removeClass('btn-pulse');
            } else {
                const parts = [];
                if (adds)    parts.push(`+${adds}`);
                if (removes) parts.push(`-${removes}`);
                label += `تشعيب المحددين <span class="badge bg-light text-dark ms-1">${parts.join(' · ')}</span>`;
                $btn.addClass('btn-pulse');
            }
            $btn.html(label);
        }
        // Hook updateSaveLabel into every basket mutation
        new MutationObserver(updateSaveLabel).observe(document.getElementById('basketList'), { childList: true });
        $('#unlockToggleBtn').on('click', function () {
            unlockMode = !unlockMode;
            reflectUnlockUI();
        });

        /* -------- Select All -------- */
        $('#selectAllPoolBtn').on('click', () => bulk.selectAllVisible());

        /* -------- Reset removals when target group changes -------- */
        $('#bulkTargetGroup').on('change', function () {
            removedExisting = [];
            unlockMode = false;
            reflectUnlockUI();
        });

        $('#bulkAssignSubmit').on('click', function () {
            const groupId = $('#bulkTargetGroup').val();
            const newIds  = bulk.getNewIds();
            if (!groupId) return Swal.fire({icon:'warning', title:'اختر المجموعة المستهدفة أولاً'});
            if (!newIds.length && !removedExisting.length) {
                return Swal.fire({icon:'warning', title:'لا توجد تغييرات', text:'لم تضف طلاباً جدد ولم تُزل أحداً.'});
            }

            const btn = $(this);
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جاري الحفظ...');
            $.post(URL_ASSIGN, {
                _token: CSRF,
                group_id: groupId,
                student_ids: newIds,
                remove_ids: removedExisting,
                // Carry the multi-enroll intent so the server relaxes the same-program guard
                include_enrolled: $('#bulkMultiEnroll').is(':checked') ? 1 : 0,
            }, function (res) {
                if (res.status === 'success') {
                    const hasBlocked = res.blocked && res.blocked.length;
                    let html = '<div>' + (res.message || '') + '</div>';
                    if (res.fee_warning) {
                        html += '<div style="margin-top:8px;padding:8px 10px;background:#fff8e1;border:1px solid #ffe082;border-radius:6px;color:#8a6d00;font-size:13px;"><i class="bi bi-exclamation-triangle-fill"></i> ' + res.fee_warning + '</div>';
                    }
                    if (hasBlocked) {
                        html += '<hr><div style="text-align:right;font-size:13px;"><b class="text-danger"><i class="bi bi-exclamation-triangle-fill"></i> طلاب مُنع تشعيبهم:</b><ul style="padding-inline-start:18px;margin-top:6px;">';
                        res.blocked.forEach(b => {
                            html += '<li><b>' + b.name + '</b>: ' + ((b.reasons || []).join('، ')) + '</li>';
                        });
                        html += '</ul></div>';
                    }
                    Swal.fire({ icon: hasBlocked ? 'warning' : 'success', title: 'تم!', html: html }).then(() => {
                        bulkCloseProgrammatic();
                        if (typeof table !== 'undefined' && table.ajax) table.ajax.reload();
                    });
                } else {
                    Swal.fire({icon:'error', title:'خطأ', text: res.message || 'فشل التشعيب'});
                }
            }).fail(xhr => Swal.fire({icon:'error', title:'خطأ', text: xhr.responseJSON?.message || 'حدث خطأ في الخادم'}))
              .always(() => { btn.prop('disabled', false); updateSaveLabel(); });
        });

        /* -------------------- PROMOTE -------------------- */
        const prom = makeShuttle({
            poolSel: '#rosterList', basketSel: '#promoteBasketList',
            poolCountSel: '#rosterCount', basketCountSel: '#promoteBasketCount',
            searchSel: '#rosterSearch',
            emptyPoolMsg: 'لا يوجد طلاب مطابقون',
            emptyBasketMsg: 'اسحب أو انقر طالباً لإضافته',
        });
        $('#selectAllRosterBtn').on('click', () => prom.selectAllVisible());

        function loadRoster() {
            const gid = $('#promoteSourceGroup').val();
            if (!gid) { prom.setPool([]); return; }
            $('#rosterList').html('<li class="shuttle-empty"><div class="spinner-border spinner-border-sm text-info"></div> جاري التحميل...</li>');
            $.get(URL_ROSTER + '/' + gid, function (res) {
                if (res.success) prom.setPool(res.students);
            }).fail(() => $('#rosterList').html('<li class="shuttle-empty text-danger">تعذّر تحميل الطلاب</li>'));
        }

        $('#promoteModal').on('shown.bs.modal', function () {
            initShuttleSelects($(this));
            prom.resetBasket(); prom.setPool([]);
        });
        $('#promoteSourceGroup').on('change', function () { prom.resetBasket(); loadRoster(); });
        $('#rosterSearch').on('input', () => prom.searchUpdated());
        $('#clearPromoteBasketBtn').on('click', () => prom.resetBasket());

        $('#promoteSubmit').on('click', function () {
            const src = $('#promoteSourceGroup').val();
            const tgt = $('#promoteTargetGroup').val();
            const ids = prom.getBasketIds();
            if (!src || !tgt) return Swal.fire({icon:'warning', title:'اختر المجموعة المصدر والمجموعة الهدف'});
            if (src === tgt)   return Swal.fire({icon:'warning', title:'المجموعتان متطابقتان'});
            if (!ids.length)   return Swal.fire({icon:'warning', title:'لم تحدد أي طالب للتصعيد'});

            const btn = $(this);
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جاري التصعيد...');
            $.post(URL_PROMOTE, {
                _token: CSRF,
                source_group_id: src,
                target_group_id: tgt,
                carry_fees: $('#carryFeesToggle').is(':checked') ? 1 : 0,
                student_ids: ids,
            }, function (res) {
                if (res.status === 'success') {
                    const skipped = res.skipped || [];
                    let html = '<div>' + (res.message || '') + '</div>';
                    if (skipped.length) {
                        html += '<hr><div style="text-align:right;font-size:13px;"><b class="text-danger"><i class="bi bi-cash-coin"></i> لم يُصعَّدوا بسبب رسوم مستحقة:</b>'
                              + '<ul style="padding-inline-start:18px;margin-top:6px;">';
                        skipped.forEach(s => {
                            html += '<li><b>' + s.name + '</b> — مستحق: <span class="text-danger">' + Number(s.amount).toFixed(2) + ' ILS</span></li>';
                        });
                        html += '</ul></div>';
                    }
                    Swal.fire({ icon: skipped.length ? 'warning' : 'success', title: 'تم!', html: html }).then(() => {
                        $('#promoteModal').modal('hide');
                        if (typeof table !== 'undefined' && table.ajax) table.ajax.reload();
                    });
                } else {
                    Swal.fire({icon:'error', title:'خطأ', text: res.message || 'فشل التصعيد'});
                }
            }).fail(xhr => Swal.fire({icon:'error', title:'خطأ', text: xhr.responseJSON?.message || 'حدث خطأ في الخادم'}))
              .always(() => btn.prop('disabled', false).html('<i class="bi bi-arrow-up-right-square-fill me-1"></i> تصعيد المحددين'));
        });
    })();
    </script>

    @include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'groups'])
@stop

