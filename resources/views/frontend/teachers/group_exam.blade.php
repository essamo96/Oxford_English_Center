@extends('frontend.layouts.master')
@section('title', 'Exam Schedule Overview')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/student-dashboard.css') }}?v={{ time() }}">
@endsection

@section('content')
<div class="student-dashboard-wrapper">
    <div id="bg-particles"></div>
    <div class="container" style="position: relative; z-index: 1;">

        {{-- Breadcrumbs Navigation --}}
        <div class="breadcrumbs-nav" style="margin-bottom: 15px; font-size: 13px; color: var(--light-text);">
            <a href="{{ url('/teacher') }}" style="color: var(--primary); text-decoration: none;"><i class="fa fa-home"></i> Dashboard</a>
            <span style="margin: 0 5px;">/</span>
            <span>Exam Schedule</span>
        </div>

        {{-- Page Header --}}
        <div class="page-header-block d-flex justify-content-between align-items-center">
            <div>
                <p class="page-title m-0"><i class="fa fa-calendar"></i> Exam Schedule</p>
                <p class="page-subtitle m-0">View and manage exam dates for all assigned groups</p>
            </div>
            <div class="d-flex align-items-center" style="gap: 10px;">
                <button type="button" class="btn-modern btn-sm btn-print" style="background: white; border: 1px solid #e2e8f0; color: var(--primary); padding: 6px 15px; font-size: 13px;" onclick="window.print()">
                    <i class="fa fa-print"></i> Print
                </button>
                <a href="{{ url('/teacher') }}" class="btn-modern btn-modern-primary btn-sm btn-back" style="padding: 6px 15px; font-size: 13px;">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        @include('frontend.layouts.error')

        <div class="marks-table-wrapper">
            <div style="padding: 14px 16px; background: var(--primary);">
                <span style="color: white; font-size: 14px; font-weight: 700;"><i class="fa fa-list" style="color: var(--accent);"></i> Group Exam Dates</span>
            </div>

            <div class="table-responsive">
                <table class="marks-table">
                    <thead>
                        <tr>
                            <th style="text-align:left; padding-left:16px;">Group</th>
                            <th>Progress Test 1</th>
                            <th>Progress Test 2</th>
                            <th>Final Exam</th>
                            <th>Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $group)
                            <tr>
                                <td style="text-align:left; padding-left:16px;">
                                    <div style="font-weight: 700; color: var(--primary); font-size: 13px;">{{ $group->group->name }}</div>
                                    @if($group->group->program ?? null)
                                        <div style="font-size: 11px; color: var(--light-text);">{{ $group->group->program->title }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if ($group->progress_test1)
                                        <span class="mark-chip has-mark"><i class="fa fa-calendar-o"></i> {{ $group->progress_test1 }}</span>
                                    @else
                                        <span class="mark-chip no-mark">Not Set</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($group->progress_test2)
                                        <span class="mark-chip has-mark"><i class="fa fa-calendar-o"></i> {{ $group->progress_test2 }}</span>
                                    @else
                                        <span class="mark-chip no-mark">Not Set</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($group->final_exam)
                                        <span class="mark-chip final"><i class="fa fa-graduation-cap"></i> {{ $group->final_exam }}</span>
                                    @else
                                        <span class="mark-chip no-mark">Not Set</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ url('examDate/' . $group->group_id) }}" class="action-btn-xs evaluate" title="Edit Exam Dates" style="width:auto; padding: 0 12px; font-size:12px; gap:5px; display:inline-flex;">
                                        <i class="fa fa-edit"></i> Edit Dates
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center" style="padding: 60px 20px; color: var(--light-text);">
                                    <div style="width: 100px; height: 100px; background: rgba(49,130,206,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto;">
                                        <i class="fa fa-calendar-times-o" style="font-size:40px; color: #3182ce;"></i>
                                    </div>
                                    <h4 style="color: var(--primary); font-weight: 700; margin-bottom: 5px;">No Exams Scheduled</h4>
                                    <p style="font-size: 13px; margin-bottom: 15px;">There are currently no active groups with scheduled exams.</p>
                                    <a href="{{ url('/teacher') }}" class="btn-modern btn-modern-primary btn-sm">Return to Dashboard</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
</script>
@stop
