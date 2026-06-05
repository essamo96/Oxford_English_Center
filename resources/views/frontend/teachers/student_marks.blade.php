@extends('frontend.layouts.dashboard')
@section('title', 'Student Marks')
@section('page-title', 'Student Marks')

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
            <span style="color: var(--primary);">Courses</span>
            <span style="margin: 0 5px;">/</span>
            <strong>{{ isset($group_info) ? $group_info->name : 'Group' }}</strong>
            <span style="margin: 0 5px;">/</span>
            <span>Academic Performance</span>
        </div>

        {{-- Page Header --}}
        <div class="page-header-block d-flex justify-content-between align-items-center">
            <p class="page-title m-0"><i class="fa fa-bar-chart"></i> Academic Performance</p>
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

        {{-- Group Summary Card --}}
        @php
            $totalStudents = count($data);
            $evaluated     = $data->where('has_evaluation', 1)->count();
            $pending        = $totalStudents - $evaluated;
            $firstRow       = $data->first();
            $groupName      = $firstRow ? optional($firstRow->group)->name : 'Group';
            $programTitle   = $firstRow ? optional(optional($firstRow->group)->program)->title : '';
            $avgTotal       = $totalStudents ? round($data->avg('total_degree'), 1) : 0;
        @endphp

        <div style="background: linear-gradient(135deg, var(--primary) 0%, #2d3748 100%); border-radius: 12px; padding: 20px 24px; margin-bottom: 20px; color: white; display: flex; flex-wrap: wrap; gap: 20px; align-items: center; box-shadow: 0 8px 24px rgba(26,39,68,.25);">
            {{-- Group Name Block --}}
            <div style="flex: 1; min-width: 180px;">
                <div style="font-size: 11px; opacity: .6; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Group</div>
                <div style="font-size: 18px; font-weight: 800; color: var(--accent);">{{ $groupName }}</div>
                @if($programTitle)
                    <div style="font-size: 12px; opacity: .7; margin-top: 2px;">{{ $programTitle }}</div>
                @endif
            </div>

            {{-- Divider --}}
            <div style="width: 1px; background: rgba(255,255,255,.1); align-self: stretch; display: none;" class="card-divider"></div>

            {{-- Stats --}}
            <div style="display: flex; gap: 24px; flex-wrap: wrap;">
                <div style="text-align: center;">
                    <div style="font-size: 26px; font-weight: 800; color: var(--accent); line-height: 1;">{{ $totalStudents }}</div>
                    <div style="font-size: 11px; opacity: .65; margin-top: 3px;">Students</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 26px; font-weight: 800; color: #68d391; line-height: 1;">{{ $evaluated }}</div>
                    <div style="font-size: 11px; opacity: .65; margin-top: 3px;">Evaluated</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 26px; font-weight: 800; color: #fbd38d; line-height: 1;">{{ $pending }}</div>
                    <div style="font-size: 11px; opacity: .65; margin-top: 3px;">Pending</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 26px; font-weight: 800; color: #90cdf4; line-height: 1;">{{ $avgTotal }}</div>
                    <div style="font-size: 11px; opacity: .65; margin-top: 3px;">Avg. Total</div>
                </div>
            </div>

            {{-- Quick Links --}}
            <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-left: auto;">
                <a href="{{ url('group/' . $group_id) }}" style="background: rgba(255,255,255,.1); color: white; border: 1px solid rgba(255,255,255,.15); border-radius: 8px; padding: 8px 14px; font-size: 12px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: all .2s;" onmouseover="this.style.background='rgba(255,255,255,.2)'" onmouseout="this.style.background='rgba(255,255,255,.1)'">
                    <i class="fa fa-pencil-square-o"></i> Record Marks
                </a>
                <a href="{{ url('group/attendance/' . $group_id) }}" style="background: rgba(245,197,24,.15); color: var(--accent); border: 1px solid rgba(245,197,24,.25); border-radius: 8px; padding: 8px 14px; font-size: 12px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: all .2s;" onmouseover="this.style.background='rgba(245,197,24,.25)'" onmouseout="this.style.background='rgba(245,197,24,.15)'">
                    <i class="fa fa-calendar-check-o"></i> Attendance
                </a>
            </div>
        </div>

        {{-- Marks Table Card --}}
        <div class="marks-table-wrapper">
            <div class="table-responsive">
                <table class="marks-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>PT 1<br><small style="font-weight:400;opacity:.75;">Units 1-3</small></th>
                            <th>PT 2<br><small style="font-weight:400;opacity:.75;">Units 4-6</small></th>
                            <th>PT 3<br><small style="font-weight:400;opacity:.75;">Units 7-9</small></th>
                            <th>Final<br><small style="font-weight:400;opacity:.75;">Units 1-12</small></th>
                            <th>Activity</th>
                            <th>Workbook</th>
                            <th>Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $group)
                            <tr>
                                {{-- Student info --}}
                                <td>
                                    <div class="student-mini-profile">
                                        <img src="{{ $group->student->image ? url($group->student->image) : url('assets/oxford/img/students/avatar.png') }}" alt="{{ $group->student->name }}">
                                        <div>
                                            <div class="name">{{ $group->student->name }}</div>
                                            <div class="sid">#{{ $group->student->id }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- PT 1 --}}
                                <td>
                                    <span class="mark-chip {{ $group->exam1_degree ? 'has-mark' : 'no-mark' }}">
                                        {{ $group->exam1_degree ?? '—' }}
                                    </span>
                                </td>

                                {{-- PT 2 --}}
                                <td>
                                    <span class="mark-chip {{ $group->exam2_degree ? 'has-mark' : 'no-mark' }}">
                                        {{ $group->exam2_degree ?? '—' }}
                                    </span>
                                </td>

                                {{-- PT 3 --}}
                                <td>
                                    <span class="mark-chip {{ $group->exam3_degree ? 'has-mark' : 'no-mark' }}">
                                        {{ $group->exam3_degree ?? '—' }}
                                    </span>
                                </td>

                                {{-- Final --}}
                                <td>
                                    <span class="mark-chip {{ $group->exam4_degree ? 'final' : 'no-mark' }}">
                                        {{ $group->exam4_degree ?? '—' }}
                                    </span>
                                </td>

                                {{-- Activity --}}
                                <td>
                                    <span class="mark-chip {{ $group->activity_degree ? 'has-mark' : 'no-mark' }}">
                                        {{ $group->activity_degree ?? '—' }}
                                    </span>
                                </td>

                                {{-- Workbook --}}
                                <td>
                                    <span class="mark-chip {{ $group->workbook_degree ? 'has-mark' : 'no-mark' }}">
                                        {{ $group->workbook_degree ?? '—' }}
                                    </span>
                                </td>

                                {{-- Total --}}
                                <td>
                                    <span class="mark-chip total">{{ $group->total_degree ?? 0 }}</span>
                                </td>

                                {{-- Actions --}}
                                <td>
                                    <div class="d-flex gap-10 justify-content-center">
                                        @if ($group->has_evaluation == 0)
                                            <a href="{{ route('teacher.evaluate.view', ['group_id' => Crypt::encrypt($group_id), 'student_id' => Crypt::encrypt($group->student_id)]) }}"
                                               class="action-btn-xs evaluate" title="Evaluate Student">
                                                <i class="fa fa-star"></i>
                                            </a>
                                        @else
                                            <span class="action-btn-xs done" title="Already Evaluated">
                                                <i class="fa fa-check-circle"></i>
                                            </span>
                                        @endif

                                        <a href="{{ route('teacher.remove.student', ['group_id' => Crypt::encrypt($group_id), 'student_id' => Crypt::encrypt($group->student_id)]) }}"
                                           class="action-btn-xs remove delete-confirm" title="Remove Student">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center" style="padding: 40px; color: var(--light-text);">
                                    <i class="fa fa-users" style="font-size: 40px; opacity: .2; display: block; margin-bottom: 10px;"></i>
                                    No students found in this group.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Legend --}}
        <div class="d-flex gap-15 flex-wrap mt-30" style="font-size: 12px; color: var(--light-text);">
            <span><span class="mark-chip has-mark">score</span> Progress Test</span>
            <span><span class="mark-chip final">score</span> Final Exam</span>
            <span><span class="mark-chip total">score</span> Total</span>
            <span><span class="mark-chip no-mark">—</span> Not recorded yet</span>
        </div>

    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        $('.delete-confirm').click(function (e) {
            e.preventDefault();
            const url = $(this).attr('href');
            Swal.fire({
                title: 'Remove Student?',
                text: 'This student will be permanently removed from the group.',
                icon: 'warning',
                background: '#1a2744',
                color: '#fff',
                showCancelButton: true,
                confirmButtonColor: '#e53e3e',
                cancelButtonColor: '#2d3748',
                confirmButtonText: 'Yes, remove'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = url;
            });
        });
    });
</script>
@stop
