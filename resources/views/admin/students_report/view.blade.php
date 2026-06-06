@extends('admin.layout.master')

@section('title', 'Student Report')

@section('css')
<style>
/* ── Search hero card ─────────────────────────────── */
.sr-hero {
    background: linear-gradient(135deg, #0F4C81 0%, #1a6aad 50%, #2C9AB7 100%);
    border-radius: 16px;
    padding: 36px 36px 40px;
    position: relative;
    overflow: hidden;
    margin-bottom: 24px;
    border: 0;
}
.sr-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse 55% 70% at 90% 50%, rgba(44,154,183,.25) 0%, transparent 60%),
        radial-gradient(ellipse 40% 60% at 10% 80%, rgba(247,183,51,.12) 0%, transparent 60%);
    pointer-events: none;
}
.sr-hero__deco {
    position: absolute;
    border-radius: 50%;
    border: 1px solid rgba(255,255,255,.07);
    pointer-events: none;
}
.sr-hero__deco:nth-child(1) { width:320px;height:320px; top:-120px; right:-60px; }
.sr-hero__deco:nth-child(2) { width:180px;height:180px; bottom:-80px; left:5%; border-color:rgba(247,183,51,.1); }

.sr-hero__inner { position: relative; z-index: 2; }
.sr-hero__eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255,255,255,.1);
    border: 1px solid rgba(255,255,255,.18);
    color: rgba(255,255,255,.85);
    font-size: .75rem;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    padding: 4px 14px;
    border-radius: 999px;
    margin-bottom: 14px;
}
.sr-hero__eyebrow-dot {
    width: 6px; height: 6px;
    background: #F7B733;
    border-radius: 50%;
    animation: sr-dot-blink 1.8s ease-in-out infinite;
}
@keyframes sr-dot-blink {
    0%,100%{opacity:1;transform:scale(1)}
    50%{opacity:.4;transform:scale(1.5)}
}
.sr-hero h2 {
    font-size: 1.6rem;
    font-weight: 800;
    color: #fff;
    margin: 0 0 6px;
    line-height: 1.2;
}
.sr-hero h2 em { font-style:normal; color:#F7B733; }
.sr-hero p {
    color: rgba(255,255,255,.6);
    font-size: .88rem;
    margin: 0 0 24px;
}

/* ── Search bar ──────────────────────────────────── */
.sr-search-wrap { max-width: 560px; }
.sr-search-box {
    display: flex;
    align-items: center;
    background: rgba(255,255,255,.97);
    border-radius: 999px;
    box-shadow: 0 16px 48px -8px rgba(15,76,129,.4);
    overflow: hidden;
    border: 2px solid transparent;
    transition: border-color .25s, box-shadow .25s;
    padding: 5px 5px 5px 18px;
}
.sr-search-box:focus-within {
    border-color: #F7B733;
    box-shadow: 0 0 0 4px rgba(247,183,51,.2), 0 16px 48px -8px rgba(15,76,129,.35);
}
.sr-search-input {
    flex: 1;
    border: none !important;
    outline: none !important;
    background: transparent !important;
    box-shadow: none !important;
    font-size: .97rem;
    font-weight: 600;
    color: #1a2332; /* always dark — inside white pill, never changes with theme */
    padding: 8px 12px;
    min-width: 0;
}
.sr-search-input::placeholder { color: #9ca3af; font-weight: 400; }
.sr-search-btn {
    flex-shrink: 0;
    background: linear-gradient(135deg, #0F4C81, #2C9AB7);
    color: #fff;
    border: none;
    border-radius: 999px;
    font-size: .88rem;
    font-weight: 700;
    padding: 10px 24px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 7px;
    white-space: nowrap;
    transition: filter .2s, transform .15s;
}
.sr-search-btn:hover { filter: brightness(1.12); transform: scale(1.02); }
.sr-search-btn:active { transform: scale(.98); }

.sr-search-hint {
    display: flex;
    align-items: center;
    gap: 6px;
    color: rgba(255,255,255,.45);
    font-size: .78rem;
    margin-top: 10px;
    margin-left: 18px;
}
.sr-search-hint i { color: rgba(255,255,255,.3); }

/* ── Student panel ───────────────────────────────── */
#target {
    border-radius: 16px;
    border: 1.5px solid var(--bs-border-color);
    box-shadow: 0 4px 24px rgba(15,76,129,.07);
    background: var(--bs-card-bg, var(--bs-body-bg));
    overflow: hidden;
}
#target .card-body { padding: 28px 28px 24px; }

/* Loading skeleton pulse */
.sr-loading-pulse {
    display: none;
    padding: 32px 28px;
}
.sr-loading-pulse.visible { display: block; }
.sr-skeleton {
    background: linear-gradient(90deg,
        var(--bs-tertiary-bg, #f0f3f8) 25%,
        var(--bs-secondary-bg, #e4eaf2) 50%,
        var(--bs-tertiary-bg, #f0f3f8) 75%);
    background-size: 200% 100%;
    animation: sr-shimmer 1.4s infinite;
    border-radius: 8px;
}
@keyframes sr-shimmer { to { background-position: -200% 0; } }

/* Fade-in for loaded content */
#topinfos { animation: sr-fadein .4s ease both; }
@keyframes sr-fadein { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:none} }

/* ── Results table card ──────────────────────────── */
.sr-table-card {
    border-radius: 16px;
    border: 1.5px solid var(--bs-border-color);
    box-shadow: 0 2px 12px rgba(15,76,129,.05);
    background: var(--bs-card-bg, var(--bs-body-bg));
    overflow: hidden;
}
.sr-table-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    padding: 20px 24px 16px;
    border-bottom: 1.5px solid var(--bs-border-color);
}
.sr-table-title { font-size: .95rem; font-weight: 800; margin: 0; }
.sr-table-subtitle { font-size: .8rem; color: var(--bs-secondary-color, #9ca3af); font-weight: 500; }

/* Empty state */
.sr-empty {
    text-align: center;
    padding: 64px 20px;
    color: #9ca3af;
}
.sr-empty i { font-size: 3rem; display: block; margin-bottom: 12px; color: #d1d5db; }
</style>
@stop

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">
            <i class="ki-duotone ki-home fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
            Home
        </a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Reports</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark fw-bold">Student Report</li>
@stop

@section('page-content')

{{-- ══════════════════ SEARCH HERO ══════════════════ --}}
<div class="sr-hero card mb-6">
    <span class="sr-hero__deco"></span>
    <span class="sr-hero__deco"></span>
    <div class="sr-hero__inner">

        <div class="sr-hero__eyebrow">
            <span class="sr-hero__eyebrow-dot"></span>
            Student Intelligence
        </div>

        <h2>Student <em>Full Report</em></h2>
        <p>Search by name or mobile number to load the complete student dashboard — courses, grades, financials, attendance, and certificates.</p>

        <div class="sr-search-wrap">
            <div class="sr-search-box">
                <i class="ki-duotone ki-magnifier fs-3 text-gray-400 ms-1">
                    <span class="path1"></span><span class="path2"></span>
                </i>
                <input type="text"
                       name="title"
                       id="title"
                       class="sr-search-input searchable"
                       placeholder="Student name or mobile number…"
                       autocomplete="off"
                       spellcheck="false" />
                <button class="sr-search-btn" id="srSearchBtn" type="button">
                    <i class="ki-duotone ki-magnifier fs-5">
                        <span class="path1"></span><span class="path2"></span>
                    </i>
                    Search
                </button>
            </div>
            <div class="sr-search-hint">
                <i class="ki-duotone ki-information-5 fs-6"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                Type at least 3 characters to load student details
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════ STUDENT DETAIL PANEL ══════════════════ --}}
<div id="target" class="mb-6" style="display: none;">

    {{-- Loading skeleton --}}
    <div class="sr-loading-pulse" id="srLoadingPulse">
        <div class="d-flex gap-4 mb-5 align-items-center">
            <div class="sr-skeleton" style="width:100px;height:100px;border-radius:14px;flex-shrink:0;"></div>
            <div class="flex-grow-1">
                <div class="sr-skeleton mb-3" style="height:22px;width:40%;"></div>
                <div class="sr-skeleton mb-2" style="height:14px;width:60%;"></div>
                <div class="d-flex gap-2 mt-3">
                    @for($i=0;$i<5;$i++)
                    <div class="sr-skeleton" style="width:72px;height:52px;border-radius:8px;"></div>
                    @endfor
                </div>
            </div>
        </div>
        <div class="d-flex gap-2 mb-4">
            @for($i=0;$i<5;$i++)
            <div class="sr-skeleton" style="height:38px;width:110px;border-radius:8px;"></div>
            @endfor
        </div>
        <div class="sr-skeleton" style="height:280px;border-radius:12px;"></div>
    </div>

    {{-- Actual content --}}
    <div class="card-body p-0" id="topinfos" style="display:none;">
    </div>
</div>

{{-- ══════════════════ GRADES TABLE ══════════════════ --}}
<div class="sr-table-card">
    <div class="sr-table-header">
        <div>
            <h5 class="sr-table-title">
                <i class="ki-duotone ki-chart-line-up fs-4 text-primary me-2">
                    <span class="path1"></span><span class="path2"></span>
                </i>
                Grades Overview
            </h5>
            <div class="sr-table-subtitle mt-1">All student course records — filter by searching above</div>
        </div>
        <div class="d-flex align-items-center position-relative">
            <i class="ki-duotone ki-magnifier fs-4 position-absolute ms-3 text-gray-400">
                <span class="path1"></span><span class="path2"></span>
            </i>
            <input type="text"
                   data-kt-table-filter="search"
                   class="form-control form-control-sm form-control-solid w-220px ps-10"
                   placeholder="Filter table…" />
        </div>
    </div>
    <div class="card-body pt-0">
        @include('admin.layout.error')
        <div class="table-responsive">
            <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4 fs-7" id="pages_table">
                <thead>
                    <tr class="fw-bold text-gray-400 text-uppercase fs-8 border-bottom border-gray-200">
                        <th class="w-30px text-start ps-3">#</th>
                        <th class="min-w-180px">Student</th>
                        <th class="min-w-120px">Group</th>
                        <th class="min-w-65px text-center">Test 1</th>
                        <th class="min-w-65px text-center">Test 2</th>
                        <th class="min-w-65px text-center">Test 3</th>
                        <th class="min-w-75px text-center">Final</th>
                        <th class="min-w-65px text-center">Act.</th>
                        <th class="min-w-65px text-center">WB</th>
                        <th class="min-w-90px text-center">Total</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold text-gray-600"></tbody>
            </table>
        </div>
    </div>
</div>

@stop

@section('js')
<script>
$(document).ready(function () {

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    /* ── DataTable ─────────────────────────────────────── */
    var oTable = $('#pages_table').DataTable({
        processing: true,
        serverSide: true,
        language: {
            sProcessing: '<div class="d-flex align-items-center gap-2 text-primary"><span class="spinner-border spinner-border-sm"></span> Loading…</div>',
            sZeroRecords: '<div class="sr-empty"><i class="ki-duotone ki-search-list fs-3x text-gray-300 d-block mb-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i><span class="fw-bold text-gray-500">No records found</span></div>',
            sInfo: 'Showing _START_–_END_ of _TOTAL_',
            sInfoEmpty: 'No entries',
            sInfoFiltered: '(from _MAX_ total)',
            sLengthMenu: 'Show _MENU_',
            oPaginate: { sFirst:'«', sPrevious:'‹', sNext:'›', sLast:'»' }
        },
        pageLength: 25,
        ajax: {
            url: "{{ route('students_report.list') }}",
            data: function (d) { d.info = $('#title').val(); }
        },
        order: [[1, 'asc']],
        columnDefs: [{ targets: '_all', defaultContent: '' }],
        columns: [
            { data: '', orderable: false, searchable: false },
            { data: 'student_id', orderable: true, searchable: false },
            { data: 'group_id',   orderable: true, searchable: false },
            { data: 'exam1_degree',    orderable: true, searchable: false, className: 'text-center' },
            { data: 'exam2_degree',    orderable: true, searchable: false, className: 'text-center' },
            { data: 'exam3_degree',    orderable: true, searchable: false, className: 'text-center' },
            { data: 'exam4_degree',    orderable: true, searchable: false, className: 'text-center' },
            { data: 'activity_degree', orderable: true, searchable: false, className: 'text-center' },
            { data: 'workbook_degree', orderable: true, searchable: false, className: 'text-center' },
            { data: 'total_degree',    orderable: true, searchable: false, className: 'text-center' },
        ],
        fnDrawCallback: function () {
            oTable.column(0).nodes().each(function (cell, i) {
                cell.innerHTML = oTable.page.info().start + i + 1;
            });
        }
    });

    /* ── Table search filter box ───────────────────────── */
    $('[data-kt-table-filter="search"]').on('input', function () {
        oTable.search($(this).val()).draw();
    });

    /* ── Student detail panel ──────────────────────────── */
    var searchTimer;

    function loadStudentPanel(val) {
        if (val.length < 3) {
            $('#target').fadeOut(200);
            return;
        }

        // Show skeleton
        $('#target').fadeIn(150);
        $('#srLoadingPulse').addClass('visible');
        $('#topinfos').hide();

        $.ajax({
            type: 'POST',
            url:  "{{ route('students_report.showtopinfos') }}",
            data: { info: val },
            success: function (data) {
                $('#srLoadingPulse').removeClass('visible');
                if (data.trim() !== '') {
                    $('#topinfos').html(data).show();
                } else {
                    $('#target').fadeOut(200);
                }
            },
            error: function () {
                $('#srLoadingPulse').removeClass('visible');
                $('#target').fadeOut(200);
            }
        });
    }

    $('.searchable').on('input', function () {
        var val = $(this).val();
        oTable.draw();
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () { loadStudentPanel(val); }, 400);
    });

    $('#srSearchBtn').on('click', function () {
        loadStudentPanel($('#title').val());
    });

    $('#title').on('keydown', function (e) {
        if (e.key === 'Enter') loadStudentPanel($(this).val());
    });

});
</script>
@stop

@section('modals')
    @include('admin.layout.ajax')
@stop
