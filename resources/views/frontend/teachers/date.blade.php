@extends('frontend.layouts.master')
@section('title', 'Teacher Area - Exam Dates')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/student-dashboard.css') }}?v={{ time() }}">
<link href="{{ asset('assets/admin/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    .form-control-modern[readonly] {
        background-color: #fff !important;
        cursor: pointer;
    }
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
            <span style="color: var(--primary);">Exam Schedule</span>
            <span style="margin: 0 5px;">/</span>
            <strong>{{ $group->name }}</strong>
        </div>

        <div class="page-header-block d-flex justify-content-between align-items-center">
            <div>
                <p class="page-title m-0"><i class="fa fa-calendar-plus-o"></i> Exam Scheduling</p>
                <p class="page-subtitle m-0">Set exam dates for <strong>{{$group->name}}</strong></p>
            </div>
            <a href="{{ route('teacher.ExamDates', Auth::guard('teachers')->user()->id) }}" class="btn-modern btn-modern-primary btn-sm" style="padding: 6px 15px; font-size: 13px;">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>

        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                @include('frontend.layouts.error')
                <div class="info-card" style="box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: none;">
                    <div class="p-10">
                        <div style="font-size:15px; font-weight:700; color:var(--primary); margin-bottom:18px; padding-bottom:12px; border-bottom:2px solid var(--bg-light);">
                            <i class="fa fa-calendar-plus-o" style="color:var(--accent);"></i> Schedule Exams
                        </div>
                        <form method="post" action="{{ route('teacher.group.examDate',['id' =>$group->id]) }}">
                            {{ csrf_field() }}
                            <div class="modern-form-grid">
                                <div class="form-group-modern">
                                    <label>Progress Test 1</label>
                                    <div class="input-group date date-picker" data-date-format="yyyy-mm-dd" style="width: 100%;">
                                        <input type="text" class="form-control-modern" readonly name="progress_test1" value="{{ $exam_dates ? $exam_dates->progress_test1 : '' }}" placeholder="Select Date">
                                        <span class="input-group-btn" style="position: absolute; right: 15px; top: 12px; z-index: 5;">
                                            <i class="fa fa-calendar text-muted"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group-modern">
                                    <label>Progress Test 2</label>
                                    <div class="input-group date date-picker" data-date-format="yyyy-mm-dd" style="width: 100%;">
                                        <input type="text" class="form-control-modern" readonly name="progress_test2" value="{{ $exam_dates ? $exam_dates->progress_test2 : '' }}" placeholder="Select Date">
                                        <span class="input-group-btn" style="position: absolute; right: 15px; top: 12px; z-index: 5;">
                                            <i class="fa fa-calendar text-muted"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group-modern">
                                    <label>Progress Test 3</label>
                                    <div class="input-group date date-picker" data-date-format="yyyy-mm-dd" style="width: 100%;">
                                        <input type="text" class="form-control-modern" readonly name="progress_test3" value="{{ $exam_dates ? $exam_dates->progress_test3 : '' }}" placeholder="Select Date">
                                        <span class="input-group-btn" style="position: absolute; right: 15px; top: 12px; z-index: 5;">
                                            <i class="fa fa-calendar text-muted"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group-modern">
                                    <label>Final Exam</label>
                                    <div class="input-group date date-picker" data-date-format="yyyy-mm-dd" style="width: 100%;">
                                        <input type="text" class="form-control-modern" readonly name="final_exam" value="{{ $exam_dates ? $exam_dates->final_exam : '' }}" placeholder="Select Date">
                                        <span class="input-group-btn" style="position: absolute; right: 15px; top: 12px; z-index: 5;">
                                            <i class="fa fa-calendar text-muted"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-40 text-center">
                                <button class="btn btn-success btn-lg" type="submit" style="min-width: 250px; padding: 15px; border-radius: 12px; font-weight: 700; box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);">
                                    <i class="fa fa-save"></i> Save Exam Schedule
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="mt-30 p-20" style="background: rgba(49, 130, 206, 0.05); border-left: 4px solid #3182ce; border-radius: 8px;">
                    <div style="font-size:13px; font-weight:700; color:#2b6cb0; margin-bottom:6px;"><i class="fa fa-info-circle"></i> Important Note</div>
                    <p class="m-0 text-muted small">Once set, dates will be visible to all students in <strong>{{$group->name}}</strong>. Please align dates with the academic calendar.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
<script src="{{ asset('assets/admin/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
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
        $('.date-picker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });
    });
</script>
@stop