@extends('frontend.layouts.dashboard')
@section('title', 'Student Evaluation')
@section('page-title', 'Student Evaluation')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/student-dashboard.css') }}?v={{ time() }}">
<style>
    .eval-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.09); border: 1px solid #edf2f7; }
    .eval-card-header { background: var(--primary); padding: 18px 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
    .rating-row:hover { background: #f8fafc; }
    .radio-cell { text-align: center; padding: 10px 8px; }
    .radio-label { display: inline-flex; flex-direction: column; align-items: center; gap: 4px; cursor: pointer; font-size: 14.2px; font-weight: 700; color: var(--light-text); }
    .radio-label input { display: none; }
    .radio-mark { width: 28px; height: 28px; border-radius: 6px; border: 2px solid #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 15.2px; font-weight: 700; transition: all .2s; }
    .radio-label:hover .radio-mark { border-color: var(--accent); color: var(--accent); }
    .radio-label input:checked + .radio-mark { background: var(--accent); border-color: var(--accent); color: var(--primary); box-shadow: 0 3px 8px rgba(245,197,24,.3); }
    .total-box { background: var(--primary); border-radius: 10px; padding: 16px 20px; text-align: center; }
    .total-box .total-num { font-size: 35.2px; font-weight: 800; color: var(--accent); line-height: 1; }
    .total-box .total-label { font-size: 14.2px; color: rgba(255,255,255,.6); margin-top: 4px; }
    .modern-select { height: 42px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 16.2px; font-weight: 600; color: var(--primary); padding: 0 12px; width: 100%; }
    .modern-select:focus { border-color: var(--accent); box-shadow: none; outline: none; }
    .modern-textarea { width: 100%; border-radius: 8px; border: 1px solid #e2e8f0; padding: 12px; font-size: 16.2px; resize: vertical; color: #4a5568; }
    .modern-textarea:focus { border-color: var(--accent); outline: none; }
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
            <strong>{{ $student_info->group->name }}</strong>
            <span style="margin: 0 5px;">/</span>
            <span>Evaluation</span>
        </div>

        {{-- Page Header --}}
        <div class="page-header-block d-flex justify-content-between align-items-center">
            <div>
                <p class="page-title m-0"><i class="fa fa-star-half-o"></i> Performance Evaluation</p>
                <p class="page-subtitle m-0">
                    Student: <strong>{{ $student_info->student->name }}</strong> &nbsp;|&nbsp;
                    Group: <strong>{{ $student_info->group->name }}</strong>
                </p>
            </div>
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

        <form id="evaluation-form" action="{{ route('teacher.evaluate.post') }}" method="post">
            {{ csrf_field() }}
            <input type="hidden" name="g_id" value="{{ Crypt::encrypt($student_info->group_id) }}">
            <input type="hidden" name="s_id" value="{{ Crypt::encrypt($student_info->student_id) }}">

            <div class="eval-card">
                {{-- Header selects --}}
                <div class="eval-card-header">
                    <div style="color:white; font-size: 17.2px; font-weight:700;"><i class="fa fa-sliders" style="color:var(--accent);"></i> Evaluation Settings</div>
                    <div class="d-flex gap-15 flex-wrap" style="flex:1; max-width:600px; justify-content:flex-end;">
                        <div style="flex:1; min-width:200px;">
                            <label style="font-size: 14.2px; color:rgba(255,255,255,.6); display:block; margin-bottom:4px;">Evaluation Period</label>
                            <select class="modern-select" name="evaluation_sort" required>
                                <option value="1">Evaluation 1 — Weeks 1-3</option>
                                <option value="2">Evaluation 2 — Weeks 4-6</option>
                                <option value="3">Evaluation 3 — Weeks 7-9</option>
                                <option value="4">Final Evaluation — Weeks 10-12</option>
                            </select>
                        </div>
                        <div style="flex:1; min-width:200px;">
                            <label style="font-size: 14.2px; color:rgba(255,255,255,.6); display:block; margin-bottom:4px;">Current Units</label>
                            <select class="modern-select" name="progress" required>
                                <option value="30">Units 1-3</option>
                                <option value="60">Units 4-6</option>
                                <option value="90">Units 7-9</option>
                                <option value="100">Final Unit</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Evaluation Table --}}
                <div class="table-responsive">
                    <table class="marks-table">
                        <thead>
                            <tr>
                                <th style="text-align:left;padding-left:16px; width:40px;">#</th>
                                <th style="text-align:left;">Criteria</th>
                                <th style="width:70px;">Acceptable<br><small style="opacity:.7;">1</small></th>
                                <th style="width:70px;">Good<br><small style="opacity:.7;">2</small></th>
                                <th style="width:70px;">Very Good<br><small style="opacity:.7;">3</small></th>
                                <th style="width:70px;">Excellent<br><small style="opacity:.7;">4</small></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($questions as $index => $question)
                                <tr class="rating-row">
                                    <td style="text-align:left;padding-left:16px;color:var(--light-text);font-weight:600;">{{ $index + 1 }}</td>
                                    <td style="text-align:left;">
                                        <div style="font-size: 16.2px;font-weight:600;color:var(--primary);">{{ $question->name_en }}</div>
                                        <input type="hidden" name="question_ids[]" value="{{ $question->id }}">
                                    </td>
                                    @for ($i = 1; $i <= 4; $i++)
                                        <td class="radio-cell">
                                            <label class="radio-label">
                                                <input type="radio" value="{{ $i }}" name="evaluate_degree[{{ $question->id }}][]" required class="deg-input">
                                                <span class="radio-mark">{{ $i }}</span>
                                            </label>
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Footer: Notes + Total + Submit --}}
                <div style="padding: 20px 24px; background: #f8fafc; border-top: 1px solid #edf2f7;">
                    <div class="row" style="align-items:flex-end;">
                        <div class="col-md-8">
                            <label style="font-size: 16.2px; font-weight:700; color:var(--primary); display:block; margin-bottom:8px;">
                                <i class="fa fa-pencil-square-o" style="color:var(--accent);"></i> Instructor Notes
                            </label>
                            <textarea class="modern-textarea" name="note" rows="4" placeholder="Add specific feedback for the student..."></textarea>
                        </div>
                        <div class="col-md-4">
                            <div class="total-box" style="margin-bottom:12px;">
                                <div class="total-label">Score</div>
                                <div class="total-num" id="total-score">0</div>
                                <div class="total-label">Total Points</div>
                                <input type="hidden" name="evaluate1_total" id="total-input">
                            </div>
                            <button type="submit" class="btn-modern btn-modern-accent w-100" style="justify-content:center; padding:12px;">
                                <i class="fa fa-save"></i> Submit Evaluation
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </form>

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
        $('.deg-input').change(function () {
            var total = 0;
            $('input[type="radio"]:checked').each(function () {
                total += parseInt($(this).val());
            });
            $('#total-score').text(total);
            $('#total-input').val(total);
        });
    });
</script>
@stop
