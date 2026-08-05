@extends('frontend.layouts.dashboard')
@section('title', 'الامتحانات المتاحة')
@section('page-title', 'الامتحانات المتاحة')

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">الامتحانات المتاحة</h5>
    </div>
    <div class="card-body">
        @if(session('danger'))
            <div class="alert alert-danger">{{ session('danger') }}</div>
        @endif

        <div class="row g-4">
            @forelse($exams as $exam)
                @php
                    $remaining = $exam->max_attempts - $exam->my_attempts_count;
                    $canStart = $remaining > 0;
                @endphp
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border">
                        <div class="card-body">
                            <span class="badge bg-{{ $exam->category === 'placement' ? 'info' : 'primary' }} mb-2">
                                {{ $exam->category === 'placement' ? 'اختبار تحديد مستوى' : 'امتحان مجموعة' }}
                            </span>
                            <h6 class="fw-bold">{{ $exam->title }}</h6>
                            <p class="text-muted small mb-2">{{ $exam->description }}</p>
                            <ul class="list-unstyled small text-muted mb-3">
                                <li><i class="bi bi-clock"></i> المدة: {{ $exam->duration_minutes }} دقيقة</li>
                                <li><i class="bi bi-arrow-repeat"></i> المحاولات المتبقية: {{ max($remaining, 0) }} من {{ $exam->max_attempts }}</li>
                            </ul>
                            @if($canStart)
                                <a href="{{ route('student.exams.start', ['id' => Crypt::encrypt($exam->id)]) }}" class="btn btn-primary btn-sm w-100">بدء الامتحان</a>
                            @else
                                <button class="btn btn-secondary btn-sm w-100" disabled>لا توجد محاولات متبقية</button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-light border text-center">لا توجد امتحانات متاحة حالياً</div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@stop
