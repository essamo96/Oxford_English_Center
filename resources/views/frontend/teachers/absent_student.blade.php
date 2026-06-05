@extends('frontend.layouts.dashboard')
@section('title', 'Attendance Management')
@section('page-title', 'Attendance')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/student-dashboard.css') }}?v={{ time() }}">
<style>
    .attendance-checkbox { width: 18px; height: 18px; cursor: pointer; accent-color: var(--accent); }
    .attendance-label { display: inline-flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13px; font-weight: 600; }
    .select-all-bar { background: rgba(26,39,68,.05); border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 16px; display: inline-flex; align-items: center; gap: 10px; margin-bottom: 16px; font-weight: 700; color: var(--primary); font-size: 13px; cursor: pointer; }
</style>
@endsection

@section('content')
<div class="student-dashboard-wrapper">
    <div id="bg-particles"></div>
    <div class="container" style="position: relative; z-index: 1;">

        {{-- Breadcrumbs Navigation --}}
        <div class="breadcrumbs-nav" style="margin-bottom: 15px; font-size: 13px; color: var(--light-text);">
            <a href="{{ url('/teacher') }}" style="color: var(--primary); text-decoration: none;"><i class="fa fa-home"></i> Dashboard</a>
            <span style="margin: 0 5px;">/</span>
            <span style="color: var(--primary);">Courses</span>
            <span style="margin: 0 5px;">/</span>
            <strong>{{ $group_info->name }}</strong>
            <span style="margin: 0 5px;">/</span>
            <span>Attendance</span>
        </div>

        {{-- Page Header (themed gradient card, consistent with other teacher screens) --}}
        <div class="d-flex justify-content-between align-items-center flex-wrap"
             style="gap:12px; padding:18px 22px; margin-bottom:18px; border-radius:14px;
                    background:linear-gradient(135deg,#14213d,#1f2d50); color:#fff;
                    box-shadow:0 10px 30px rgba(0,33,71,.18);">
            <div>
                <div style="font-size:17px; font-weight:800;"><i class="fa fa-calendar-check-o" style="color:var(--accent,#f5c518);"></i> كشف الحضور والغياب</div>
                <div style="font-size:13px; opacity:.85; margin-top:3px;">المجموعة: <strong>{{ $group_info->name }}</strong></div>
            </div>
            <div class="d-flex align-items-center" style="gap: 10px;">
                <button type="button" class="btn-modern btn-sm" style="background:rgba(255,255,255,.15); color:#fff; border:none; padding:7px 16px; font-size:13px;" onclick="window.print()">
                    <i class="fa fa-print"></i> طباعة
                </button>
                <a href="{{ url('/teacher') }}" class="btn-modern btn-sm" style="background:#f5c518; color:#14213d; padding:7px 16px; font-size:13px; font-weight:700;">
                    <i class="fa fa-arrow-left"></i> رجوع
                </a>
            </div>
        </div>

        @include('frontend.layouts.error')

        @php
            $gateOk   = $att_gate_ok ?? true;
            $round    = $att_round ?? 1;
            $reasons  = $att_gate_reasons ?? [];
            $window   = $att_window ?? null;
        @endphp

        {{-- Lecture window + round badges --}}
        <div class="d-flex flex-wrap align-items-center" style="gap:10px; margin-bottom:14px;">
            @if($window)
                <span class="mark-chip" style="background:rgba(49,130,206,.12); color:#2c5282; font-weight:700;">
                    <i class="fa fa-clock-o"></i> وقت المحاضرة: {{ $window }}
                </span>
            @endif
            @if($round >= 2)
                <span class="mark-chip" style="background:rgba(245,197,24,.18); color:#9a7400; font-weight:700;">
                    <i class="fa fa-user-clock"></i> جولة المتأخرين — يظهر فقط الطلاب غير المسجَّلين ({{ $att_present_count ?? 0 }} مسجّل مسبقاً)
                </span>
            @endif
        </div>

        @if(!$gateOk)
            <div style="background:#fff5f5; border:1px solid #feb2b2; border-radius:8px; padding:14px 16px; margin-bottom:16px; color:#9b2c2c;">
                <strong><i class="fa fa-lock"></i> لا يمكن أخذ الحضور الآن:</strong>
                <ul style="margin:8px 0 0; padding-inline-start:20px;">
                    @foreach($reasons as $r)
                        <li>{{ $r }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($round >= 2 && (is_countable($data) ? count($data) : 0) === 0)
            <div style="background:#f0fff4; border:1px solid #9ae6b4; border-radius:8px; padding:14px 16px; margin-bottom:16px; color:#276749;">
                <i class="fa fa-check-circle"></i> تم تسجيل حضور جميع الطلاب — لا يوجد متأخرون.
            </div>
        @endif

        <div class="marks-table-wrapper" @if(!$gateOk) style="opacity:.55; pointer-events:none;" @endif>
            <form action="{{ route('post.group.attendance', $group_info->id) }}" method="post" id="attendance-form">
                {{ csrf_field() }}

                {{-- Select All Bar --}}
                <div style="padding: 14px 16px; background: var(--primary); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <span style="color: white; font-size: 14px; font-weight: 700;"><i class="fa fa-users" style="color: var(--accent);"></i> Student Roster</span>
                    <label class="attendance-label" for="check-all" style="color: white;">
                        <input type="checkbox" id="check-all" class="attendance-checkbox"> Select All Present
                    </label>
                </div>

                <div class="table-responsive">
                    <table class="marks-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th style="text-align:left; padding-left:16px;">Student Name</th>
                                <th>Attendance</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $index => $group)
                                <tr>
                                    <td style="color: var(--light-text); font-weight: 600;">{{ $index + 1 }}</td>
                                    <td style="text-align:left; padding-left:16px;">
                                        <div class="student-mini-profile">
                                            <img src="{{ $group->student && $group->student->image ? url($group->student->image) : url('assets/oxford/img/students/avatar.png') }}" alt="">
                                            <div>
                                                <div class="name">{{ $group->student ? $group->student->name : 'N/A' }}</div>
                                                <input type="hidden" name="student_id[{{$group->student_id}}]" value="{{$group->student_id}}">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="attendance[{{ $group->student_id }}]" class="check attendance-checkbox" value="1" id="att_{{ $group->student_id }}">
                                    </td>
                                    <td>
                                        <span class="mark-chip no-mark att-status-{{ $group->student_id }}">Absent</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="padding: 16px 20px; background: #f8fafc; border-top: 1px solid #edf2f7; text-align: center;">
                    <button type="submit" id="save-att-btn" class="btn-modern btn-modern-accent" style="min-width: 220px; justify-content: center;" @if(!$gateOk) disabled @endif>
                        <i class="fa fa-save"></i> {{ $round >= 2 ? 'حفظ حضور المتأخرين' : 'حفظ الحضور' }}
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-30" style="background: rgba(245,197,24,.05); border-left: 3px solid var(--accent); border-radius: 6px; padding: 12px 16px; font-size: 13px; color: var(--light-text);">
            <i class="fa fa-info-circle" style="color: var(--accent);"></i>
            <strong style="color: var(--primary);">ملاحظة:</strong> يُحتسب حضور المدرس مرة واحدة فقط لكل محاضرة. عند فتح الشاشة ثانيةً في نفس المحاضرة ستظهر قائمة المتأخرين فقط (الطلاب غير المسجّلين) دون احتساب محاضرة جديدة. ويُشترط الاتصال بشبكة المركز وأن يكون ضمن وقت المحاضرة.
        </div>

    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof particlesJS !== 'undefined') {
        particlesJS('bg-particles', {
            particles: {
                number: { value: 40, density: { enable: true, value_area: 800 } },
                color:  { value: ['#f5c518', '#3182ce', '#ffffff'] },
                shape:  { type: 'circle' },
                opacity: { value: 0.3, random: true },
                size:    { value: 3, random: true },
                line_linked: { enable: false },
                move: { enable: true, speed: 1, direction: 'none', random: true, out_mode: 'out' }
            },
            interactivity: {
                detect_on: 'canvas',
                events: { onhover: { enable: true, mode: 'bubble' }, onclick: { enable: true, mode: 'push' } },
                modes: { bubble: { distance: 200, size: 6, duration: 2, opacity: 0.8 }, push: { particles_nb: 4 } }
            },
            retina_detect: true
        });
    }
});

    $(document).ready(function () {
        // Select all
        $('#check-all').click(function () {
            $('.check').prop('checked', this.checked);
            updateStatuses();
        });

        // Individual checkbox update
        $(document).on('change', '.check', function () {
            updateStatuses();
            if (!this.checked) $('#check-all').prop('checked', false);
        });

        function updateStatuses() {
            $('.check').each(function () {
                var sid = $(this).attr('name').match(/\[(\d+)\]/)[1];
                var badge = $('.att-status-' + sid);
                if (this.checked) {
                    badge.removeClass('no-mark').addClass('final').text('Present');
                } else {
                    badge.removeClass('final').addClass('no-mark').text('Absent');
                }
            });
        }

        // Disable button after submit
        $('#attendance-form').submit(function () {
            $('#save-att-btn').html('<i class="fa fa-spinner fa-spin"></i> Saving...').attr('disabled', true);
        });
    });
</script>
@stop
