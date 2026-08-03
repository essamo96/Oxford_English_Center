@extends('frontend.layouts.dashboard')
@section('title', 'Teacher Area - Exam Dates')
@section('page-title', 'Exam Dates')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/student-dashboard.css') }}?v={{ time() }}">
<link href="{{ asset('assets/oxford/vendor/date/bootstrap-datepicker.min.css') }}?v={{ time() }}" rel="stylesheet" type="text/css" />
<style>
    .exam-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:18px; }
    @media (max-width: 640px){ .exam-grid { grid-template-columns:1fr; } }
    .exam-field label { display:block; font-size: 16.2px; font-weight:700; color:var(--primary,#14213d); margin-bottom:6px; }
    .exam-input-wrap { position:relative; }
    .exam-input-wrap .fa-calendar { position:absolute; inset-inline-end:14px; top:50%; transform:translateY(-50%); color:#9aa4b8; pointer-events:none; }
    .exam-date {
        width:100%; height:48px; padding:10px 42px 10px 14px; border:1px solid #e2e8f0;
        border-radius:10px; background:#fff; font-size: 17.2px; font-weight:600; color:#1f2937; cursor:pointer;
        transition:border-color .15s, box-shadow .15s;
    }
    .exam-date:focus { outline:none; border-color:var(--accent,#f5c518); box-shadow:0 0 0 3px rgba(245,197,24,.15); }
    /* Make sure the popup never sticks open below the field */
    .datepicker.datepicker-dropdown { z-index: 1060; }
</style>
@endsection

@section('content')
<div class="student-dashboard-wrapper">
    <div id="bg-particles"></div>
    <div class="container" style="position: relative; z-index: 1;">
        {{-- Breadcrumbs --}}
        <div class="breadcrumbs-nav" style="margin-bottom: 15px; font-size: 16.2px; color: var(--light-text);">
            <a href="{{ url('/teacher') }}" style="color: var(--primary); text-decoration: none;"><i class="fa fa-home"></i> الرئيسية</a>
            <span style="margin: 0 5px;">/</span>
            <span style="color: var(--primary);">جدول الامتحانات</span>
            <span style="margin: 0 5px;">/</span>
            <strong>{{ $group->name }}</strong>
        </div>

        {{-- Page header (single, no repeated titles) --}}
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                @include('frontend.layouts.error')
                <div class="info-card" style="box-shadow: 0 10px 30px rgba(0,0,0,0.08); border:none; overflow:hidden;">
                    {{-- Card header: title + subtitle + back button (all inside the form card) --}}
                    <div class="d-flex justify-content-between align-items-center"
                         style="gap:12px; padding:18px 20px; background:linear-gradient(135deg,#14213d,#1f2d50); color:#fff; flex-wrap:wrap;">
                        <div style="min-width:0; flex:1 1 200px;">
                            <div style="font-size: 19.2px; font-weight:800;"><i class="fa fa-calendar-plus-o" style="color:var(--accent,#f5c518);"></i> جدولة امتحانات «{{ $group->name }}»</div>
                            <div style="font-size: 15.7px; opacity:.8; margin-top:3px;">حدّد مواعيد الاختبارات لتظهر لطلاب المجموعة</div>
                        </div>
                        <a href="{{ route('teacher.ExamDates', Auth::guard('teachers')->user()->id) }}"
                           class="btn-modern btn-sm" style="background:rgba(255,255,255,.15); color:#fff; padding:7px 16px; font-size: 16.2px; white-space:nowrap; flex:0 0 auto;">
                            <i class="fa fa-arrow-left"></i> رجوع
                        </a>
                    </div>
                    <div class="p-10" style="padding:22px 20px;">
                        <form method="post" action="{{ route('teacher.group.examDate',['id' =>$group->id]) }}">
                            {{ csrf_field() }}
                            <div class="exam-grid">
                                @php
                                    $fields = [
                                        'progress_test1' => 'اختبار التقدّم الأول',
                                        'progress_test2' => 'اختبار التقدّم الثاني',
                                        'progress_test3' => 'اختبار التقدّم الثالث',
                                        'final_exam'     => 'الامتحان النهائي',
                                    ];
                                @endphp
                                @foreach($fields as $name => $label)
                                    <div class="exam-field">
                                        <label>{{ $label }}</label>
                                        <div class="exam-input-wrap">
                                            <input type="text" class="exam-date js-date" readonly autocomplete="off"
                                                   name="{{ $name }}"
                                                   value="{{ $exam_dates ? $exam_dates->{$name} : '' }}"
                                                   placeholder="اختر التاريخ">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-40 text-center">
                                <button class="btn-modern btn-modern-accent" type="submit" style="min-width:240px; justify-content:center;">
                                    <i class="fa fa-save"></i> حفظ مواعيد الامتحانات
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="mt-30" style="background: rgba(49,130,206,.05); border-inline-start:4px solid #3182ce; border-radius:8px; padding:14px 18px;">
                    <div style="font-size: 16.2px; font-weight:700; color:#2b6cb0; margin-bottom:4px;"><i class="fa fa-info-circle"></i> ملاحظة</div>
                    <p class="m-0 text-muted small">ستظهر هذه المواعيد لجميع طلاب المجموعة بمجرّد حفظها. يُرجى مطابقتها مع التقويم الأكاديمي.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
<script src="{{ asset('assets/oxford/vendor/date/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
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

    $(function () {
        // Initialise on the INPUT (not the container) so the calendar pops up on focus/click
        // instead of rendering inline under every field.
        $('.js-date').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            orientation: 'bottom auto'
        }).on('changeDate', function () {
            $(this).datepicker('hide');
        });
    });
</script>
@stop