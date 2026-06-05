@extends('frontend.layouts.dashboard')
@section('title', 'Group Progress')
@section('page-title', 'Group Progress')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/student-dashboard.css') }}?v={{ time() }}">
<style>
    .progress-card { background: var(--surface, white); border-radius: 12px; padding: 20px 24px; box-shadow: 0 2px 12px rgba(0,0,0,.07); border: 1px solid var(--border-color, #edf2f7); margin-bottom: 16px; transition: box-shadow .2s; }
    .progress-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,.1); }
    .progress-track { display: flex; justify-content: space-between; align-items: center; position: relative; margin: 28px 0 40px; }
    .progress-track::before { content:''; position:absolute; top:13px; left:0; right:0; height:4px; background:#e2e8f0; z-index:1; }
    .progress-fill-line { position:absolute; top:13px; left:0; height:4px; background:var(--accent); z-index:2; transition: width 1s ease; border-radius:4px; }
    .step-dot { position:relative; z-index:3; width:30px; height:30px; border-radius:50%; background:white; border:3px solid #e2e8f0; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:700; color:#a0aec0; transition:all .3s; flex-shrink:0; }
    .step-dot.done { background:var(--accent); border-color:var(--accent); color:var(--primary); box-shadow:0 0 10px rgba(245,197,24,.35); }
    .step-dot .dot-label { position:absolute; top:36px; left:50%; transform:translateX(-50%); white-space:nowrap; font-size:10px; color:#718096; font-weight:600; }
    .step-dot.done .dot-label { color:var(--primary); }
    .pct-badge { font-size:26px; font-weight:800; color:var(--accent); line-height:1; }
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
            <span>Group Progress</span>
        </div>

        {{-- Page Header --}}
        <div class="page-header-block d-flex justify-content-between align-items-center">
            <div>
                <p class="page-title m-0"><i class="fa fa-line-chart"></i> Group Progress</p>
                <p class="page-subtitle m-0">Monitor curriculum completion across all assigned groups</p>
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

        @forelse ($info as $group)
            @php $pct = (int)($group->progress ?? 0); @endphp
            <div class="progress-card animate-up">
                {{-- Card Top --}}
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-10">
                    <div>
                        <div style="font-size: 16px; font-weight: 700; color: var(--primary);">{{ $group->name }}</div>
                        @if($group->program ?? null)
                            <div style="font-size: 12px; color: var(--light-text);">{{ $group->program->title }}</div>
                        @endif
                    </div>
                    <div class="text-right">
                        <div class="pct-badge">{{ $pct }}%</div>
                        <div style="font-size: 11px; color: var(--light-text); margin-top:2px;">Completed</div>
                    </div>
                </div>

                {{-- Progress Track --}}
                <div class="progress-track">
                    <div class="progress-fill-line" style="width:{{ $pct }}%"></div>
                    @php
                        $steps = [
                            ['pct'=>10,'label'=>'U1'],['pct'=>20,'label'=>'U2'],['pct'=>30,'label'=>'U3'],
                            ['pct'=>40,'label'=>'U4'],['pct'=>50,'label'=>'U5'],['pct'=>60,'label'=>'U6'],
                            ['pct'=>70,'label'=>'U7'],['pct'=>80,'label'=>'U8'],['pct'=>90,'label'=>'U9'],
                            ['pct'=>100,'label'=>'U10'],
                        ];
                    @endphp
                    @foreach($steps as $step)
                        <div class="step-dot {{ $pct >= $step['pct'] ? 'done' : '' }}">
                            {{ $pct >= $step['pct'] ? '✓' : '' }}
                            <span class="dot-label">{{ $step['label'] }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- Footer --}}
                <div class="d-flex justify-content-between align-items-center" style="margin-top: 8px;">
                    <div class="d-flex gap-15" style="font-size: 12px; color: var(--light-text);">
                        <span><span style="display:inline-block;width:10px;height:10px;background:var(--accent);border-radius:50%;"></span> Done</span>
                        <span><span style="display:inline-block;width:10px;height:10px;background:#e2e8f0;border-radius:50%;"></span> Pending</span>
                    </div>
                    <a href="{{ route('teacher.showGroueStudents',['group_id'=>Crypt::encrypt($group->id),'teacher_id'=>Crypt::encrypt($group->teacher_id)]) }}"
                       class="action-btn-xs evaluate" style="width:auto;padding:0 14px;font-size:12px;gap:5px;display:inline-flex;">
                        <i class="fa fa-users"></i> Students
                    </a>
                </div>
            </div>
        @empty
            <div class="marks-table-wrapper" style="padding: 40px; text-align: center; color: var(--light-text);">
                <i class="fa fa-bar-chart" style="font-size:40px;opacity:.2;display:block;margin-bottom:10px;"></i>
                No groups found for progress tracking.
            </div>
        @endforelse

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
