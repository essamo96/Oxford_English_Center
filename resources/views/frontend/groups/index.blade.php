@extends('frontend.layouts.dashboard')
@section('title', 'Record Student Marks')
@section('page-title', 'Record Marks')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/student-dashboard.css') }}?v={{ time() }}">
<style>
    /* ── Header Card with Particles ── */
    .marks-header-card {
        position: relative;
        background: linear-gradient(135deg, var(--primary) 0%, #2d3748 100%);
        border-radius: 16px;
        padding: 28px 30px;
        color: white;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(26,39,68,.3);
        margin-bottom: 20px;
    }

    #marks-particles {
        position: absolute;
        inset: 0;
        z-index: 0;
        pointer-events: none;
    }

    .marks-header-inner {
        position: relative;
        z-index: 2;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 20px;
    }

    .marks-header-info { flex: 1; min-width: 180px; }
    .marks-group-label { font-size: 14.2px; opacity: .6; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
    .marks-group-name  { font-size: 23.2px; font-weight: 800; color: var(--accent); line-height: 1.2; }
    .marks-group-meta  { font-size: 15.2px; opacity: .7; margin-top: 5px; display: flex; gap: 12px; flex-wrap: wrap; }

    /* Stats row */
    .marks-stats { display: flex; gap: 20px; flex-wrap: wrap; }
    .marks-stat  { text-align: center; min-width: 56px; }
    .marks-stat-num   { font-size: 29.2px; font-weight: 800; line-height: 1; }
    .marks-stat-label { font-size: 14.2px; opacity: .65; margin-top: 3px; }

    /* Quick action buttons */
    .marks-quick-btns {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-left: auto;
    }

    .mq-btn {
        border-radius: 8px;
        padding: 8px 14px;
        font-size: 15.2px;
        font-weight: 700;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all .2s;
        white-space: nowrap;
    }
    .mq-btn-gold  { background: rgba(245,197,24,.18); color: var(--accent); border: 1px solid rgba(245,197,24,.3); }
    .mq-btn-white { background: rgba(255,255,255,.1);  color: white;         border: 1px solid rgba(255,255,255,.15); }
    .mq-btn:hover { filter: brightness(1.15); transform: translateY(-1px); }

    /* ── White Content Container ── */
    .marks-content-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,.07);
        overflow: hidden;
        border: 1px solid #edf2f7;
    }

    .marks-content-card-header {
        background: #f8fafc;
        border-bottom: 1px solid #edf2f7;
        padding: 14px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .marks-content-card-header .card-title {
        font-size: 17.2px;
        font-weight: 700;
        color: var(--primary);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .marks-content-card-header .card-hint {
        font-size: 15.2px;
        color: var(--light-text);
    }

    /* ── Input Table ── */
    .marks-input-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 16.2px;
    }

    .marks-input-table thead tr {
        background: var(--primary);
        color: white;
    }

    .marks-input-table thead th {
        padding: 11px 10px;
        font-size: 14.2px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .4px;
        text-align: center;
        white-space: nowrap;
        border: none;
    }

    .marks-input-table thead th:first-child { text-align: left; padding-left: 16px; }
    .marks-input-table thead th:nth-child(2) { text-align: left; }

    .marks-input-table tbody tr {
        border-bottom: 1px solid #edf2f7;
        transition: background .15s;
        background-color: white; /* Needed for sticky column */
    }
    .marks-input-table tbody tr:hover { background: #fffdf5; }
    .marks-input-table tbody tr:last-child { border-bottom: none; }

    .marks-input-table tbody td {
        padding: 9px 8px;
        text-align: center;
        vertical-align: middle;
        border: none;
    }
    .marks-input-table tbody td:first-child { text-align: left; padding-left: 16px; }
    
    /* Sticky Header & Frozen Name Column */
    .table-responsive {
        max-height: 600px;
        overflow-y: auto;
        overflow-x: auto;
    }
    .marks-input-table th {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: var(--primary);
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    }
    .marks-input-table td:nth-child(2),
    .marks-input-table th:nth-child(2) {
        position: sticky;
        left: 0;
        z-index: 5;
    }
    .marks-input-table td:nth-child(2) {
        background-color: inherit;
        box-shadow: 2px 0 5px -2px rgba(0,0,0,0.1);
    }
    .marks-input-table th:nth-child(2) {
        z-index: 11;
    }

    /* Input fields */
    .deg-input {
        width: 64px;
        height: 34px;
        border: 1.5px solid #e2e8f0;
        border-radius: 7px;
        text-align: center;
        font-size: 16.2px;
        font-weight: 600;
        color: var(--primary);
        background: white;
        transition: all .2s;
        outline: none;
    }
    .deg-input:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(245,197,24,.15);
        background: #fffdf0;
    }
    .deg-input::placeholder { color: #c3cdd9; font-weight: 400; }

    /* Total badge */
    .total-lbl {
        display: inline-block;
        min-width: 44px;
        padding: 4px 10px;
        background: var(--primary);
        color: var(--accent);
        border-radius: 20px;
        font-weight: 800;
        font-size: 16.2px;
    }

    /* Save button */
    .save-marks-btn {
        background: linear-gradient(135deg, #28a745 0%, #20863a 100%);
        color: white;
        border: none;
        padding: 12px 44px;
        border-radius: 10px;
        font-size: 17.2px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(40,167,69,.3);
        transition: all .25s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .save-marks-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(40,167,69,.35);
    }

    /* ── Responsive ── */
    @media (max-width: 768px) {
        .marks-header-card { padding: 20px 16px; }
        .marks-quick-btns  { margin-left: 0; width: 100%; justify-content: center; }
        .marks-stats       { justify-content: center; width: 100%; }
        .deg-input         { width: 52px; font-size: 15.2px; }
        .marks-input-table thead th { font-size: 13.2px; padding: 8px 4px; }
        .marks-input-table tbody td { padding: 7px 4px; }
    }
</style>
@endsection

@section('content')
<div class="student-dashboard-wrapper">
    <div id="bg-particles"></div>
    <div class="container" style="position: relative; z-index: 1;">

        {{-- Breadcrumbs Navigation --}}
        <div class="breadcrumbs-nav" style="margin-bottom: 15px; font-size: 16.2px; color: var(--light-text);">
            <a href="{{ url('/teacher') }}" style="color: var(--primary); text-decoration: none;"><i class="fa fa-home"></i> Dashboard</a>
            <span style="margin: 0 5px;">/</span>
            <span style="color: var(--primary);">Courses</span>
            <span style="margin: 0 5px;">/</span>
            <strong>{{ $group_info->name }}</strong>
            <span style="margin: 0 5px;">/</span>
            <span>Record Marks</span>
        </div>

        {{-- Page Header --}}
        <div class="page-header-block d-flex justify-content-between align-items-center">
            <p class="page-title m-0"><i class="fa fa-pencil-square-o"></i> Record Student Marks</p>
            <div class="d-flex align-items-center" style="gap: 10px;">
                <button type="button" class="btn-modern btn-sm btn-print" style="background: white; border: 1px solid #e2e8f0; color: var(--primary); padding: 6px 15px; font-size: 16.2px;" onclick="window.print()">
                    <i class="fa fa-print"></i> Print
                </button>
                <a href="{{ url('/teacher') }}" class="btn-modern btn-modern-primary btn-sm btn-back" style="padding: 6px 15px; font-size: 16.2px;">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        @include('frontend.layouts.error')

        {{-- ═══ Group Header Card with Particles ═══ --}}
        @php
            $totalStudents = count($group_students);
            $filled = $group_students->filter(fn($s) => $s->total_degree > 0)->count();
        @endphp

        <div class="marks-header-card">
            <div id="marks-particles"></div>
            <div class="marks-header-inner">

                {{-- Group Info --}}
                <div class="marks-header-info">
                    <div class="marks-group-label">Active Group</div>
                    <div class="marks-group-name">{{ $group_info->name }}</div>
                    <div class="marks-group-meta">
                        @if($group_info->ctime ?? null)
                            <span><i class="fa fa-clock-o"></i> {{ $group_info->ctime->times }}</span>
                            <span><i class="fa fa-calendar"></i> {{ $group_info->ctime->days }}</span>
                        @endif
                        @if($group_info->program ?? null)
                            <span><i class="fa fa-book"></i> {{ $group_info->program->title }}</span>
                        @endif
                    </div>
                </div>

                {{-- Stats --}}
                <div class="marks-stats">
                    <div class="marks-stat">
                        <div class="marks-stat-num" style="color: var(--accent);">{{ $totalStudents }}</div>
                        <div class="marks-stat-label">Students</div>
                    </div>
                    <div class="marks-stat">
                        <div class="marks-stat-num" style="color: #68d391;">{{ $filled }}</div>
                        <div class="marks-stat-label">Entered</div>
                    </div>
                    <div class="marks-stat">
                        <div class="marks-stat-num" style="color: #fbd38d;">{{ $totalStudents - $filled }}</div>
                        <div class="marks-stat-label">Pending</div>
                    </div>
                </div>

                {{-- Quick Buttons --}}
                <div class="marks-quick-btns">
                    <a href="{{ url('group/attendance/' . $group_info->id) }}" class="mq-btn mq-btn-gold">
                        <i class="fa fa-calendar-check-o"></i> Attendance
                    </a>
                    <a href="{{ url('/teacher') }}" class="mq-btn mq-btn-white">
                        <i class="fa fa-home"></i> Dashboard
                    </a>
                </div>

            </div>
        </div>

        {{-- ═══ White Marks Entry Card ═══ --}}
        <div class="marks-content-card">
            <div class="marks-content-card-header">
                <div class="card-title">
                    <i class="fa fa-edit" style="color: var(--accent);"></i>
                    Enter Student Marks
                </div>
                <div class="card-hint">
                    <i class="fa fa-info-circle"></i> Totals update automatically as you type
                </div>
            </div>

            <form id="checkout-form" method="post" action="{{ route('teacher.group.view', ['id' => $group_info->id]) }}">
                {{ csrf_field() }}
                <div class="table-responsive">
                    <table class="marks-input-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student</th>
                                <th>PT 1<br><small style="opacity:.65; font-weight:400;">Units 1-3</small></th>
                                <th>PT 2<br><small style="opacity:.65; font-weight:400;">Units 4-6</small></th>
                                <th>PT 3<br><small style="opacity:.65; font-weight:400;">Units 7-9</small></th>
                                <th>Final<br><small style="opacity:.65; font-weight:400;">Units 1-12</small></th>
                                <th>Coursework</th>
                                <th>Workbook</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $counter = 1; @endphp
                            @foreach($group_students as $group)
                                <tr>
                                    <td style="color: var(--light-text); font-weight: 600; font-size: 15.2px;">{{ $counter }}</td>
                                    <td>
                                        <div class="student-mini-profile">
                                            <img src="{{ $group->student && $group->student->image ? url($group->student->image) : url('assets/oxford/img/students/avatar.png') }}" alt="">
                                            <div class="name">{{ $group->student ? $group->student->name : '—' }}</div>
                                        </div>
                                    </td>
                                    <td><input type="text" value="{{ $group->exam1_degree }}"    name="exam1_degree[{{ $group->student_id }}]"    id="exam1_degree_{{ $group->student_id }}"    data_id="{{ $group->student_id }}" placeholder="15"       class="deg-input tes"></td>
                                    <td><input type="text" value="{{ $group->exam2_degree }}"    name="exam2_degree[{{ $group->student_id }}]"    id="exam2_degree_{{ $group->student_id }}"    data_id="{{ $group->student_id }}" placeholder="15"       class="deg-input"></td>
                                    <td><input type="text" value="{{ $group->exam3_degree }}"    name="exam3_degree[{{ $group->student_id }}]"    id="exam3_degree_{{ $group->student_id }}"    data_id="{{ $group->student_id }}" placeholder="60"       class="deg-input"></td>
                                    <td><input type="text" value="{{ $group->exam4_degree }}"    name="exam4_degree[{{ $group->student_id }}]"    id="exam4_degree_{{ $group->student_id }}"    data_id="{{ $group->student_id }}" placeholder="60"       class="deg-input"></td>
                                    <td><input type="text" value="{{ $group->activity_degree }}" name="activity_degree[{{ $group->student_id }}]" id="activity_degree_{{ $group->student_id }}" data_id="{{ $group->student_id }}" placeholder="Activity" class="deg-input"></td>
                                    <td><input type="text" value="{{ $group->workbook_degree }}"  name="workbook_degree[{{ $group->student_id }}]"  id="workbook_degree_{{ $group->student_id }}"  data_id="{{ $group->student_id }}" placeholder="Workbook" class="deg-input"></td>
                                    <td><span class="total-lbl" id="total_lbl_{{ $group->student_id }}">{{ $group->total_degree ?: 0 }}</span></td>
                                </tr>
                                @php $counter++; @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Save Footer --}}
                <div style="padding: 18px 24px; border-top: 1px solid #edf2f7; text-align: center; background: #fafafa;">
                    <button class="save-marks-btn" type="submit">
                        <i class="fa fa-save"></i> Save All Marks
                    </button>
                </div>
            </form>
        </div>

        {{-- Tip Note --}}
        <div style="margin-top: 16px; background: rgba(49,130,206,.06); border-left: 3px solid #3182ce; border-radius: 6px; padding: 11px 16px; font-size: 16.2px; color: var(--light-text);">
            <i class="fa fa-info-circle" style="color: #3182ce;"></i>
            <strong style="color: var(--primary);">Tip:</strong>
            Totals calculate live as you type. Click <em>Save All Marks</em> to persist changes.
        </div>

    </div>
</div>
@stop

@section('js')
{{-- Particles.js --}}
<script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof particlesJS !== 'undefined') {
        particlesJS('bg-particles', {
            particles: {
                number: { value: 50, density: { enable: true, value_area: 700 } },
                color:  { value: ['#f5c518', '#ffffff', '#90cdf4', '#68d391'] },
                shape:  { type: 'circle' },
                opacity: { value: 0.45, random: true, anim: { enable: true, speed: 0.8, opacity_min: 0.1, sync: false } },
                size:    { value: 5, random: true, anim: { enable: true, speed: 2, size_min: 1, sync: false } },
                line_linked: { enable: false },
                move: {
                    enable: true, speed: 1.4, direction: 'none',
                    random: true, straight: false, out_mode: 'out', bounce: false
                }
            },
            interactivity: {
                detect_on: 'canvas',
                events: {
                    onhover: { enable: true, mode: 'bubble' },
                    onclick: { enable: true, mode: 'push' },
                    resize: true
                },
                modes: {
                    bubble: { distance: 180, size: 10, duration: 2, opacity: 0.8, speed: 3 },
                    push:   { particles_nb: 3 }
                }
            },
            retina_detect: true
        });
    }
});

// ── Live total calculation (original logic preserved) ──
$('.deg-input').keyup(function () {
    var id = this.getAttribute('data_id');
    var e1 = parseFloat($('#exam1_degree_'    + id).val()) || 0;
    var e2 = parseFloat($('#exam2_degree_'    + id).val()) || 0;
    var e3 = parseFloat($('#exam3_degree_'    + id).val()) || 0;
    var e4 = parseFloat($('#exam4_degree_'    + id).val()) || 0;
    var ac = parseFloat($('#activity_degree_' + id).val()) || 0;
    var wb = parseFloat($('#workbook_degree_' + id).val()) || 0;
    var total = e1 + e2 + e3 + e4 + ac + wb;
    $('#total_lbl_' + id).text(total > 0 ? total : 0);
});

$('.deg-input').trigger('keyup');
$('.deg-input').focus(function () { $(this).select(); });
</script>
@stop