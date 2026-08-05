{{-- Simulates exactly what the student sees on the exam-taking screen (frontend.students.exams.take) --}}
<div class="mb-5">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h4 class="mb-0">{{ $exam->title }}</h4>
        <span class="alert alert-warning py-1 px-3 mb-0 fw-bold">{{ $exam->duration_minutes }} دقيقة</span>
    </div>
    @if($exam->description)
        <div class="text-muted">{!! $exam->description !!}</div>
    @endif
</div>

<div class="row g-3 mb-5">
    <div class="col-md-4">
        <div class="border rounded p-3 text-center">
            <div class="fw-bold fs-4">{{ $exam->questions->count() }}</div>
            <div class="text-muted small">عدد الأسئلة</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="border rounded p-3 text-center">
            <div class="fw-bold fs-4">{{ $exam->questions->sum(fn($q) => $q->pivot->marks_override ?? $q->marks) }}</div>
            <div class="text-muted small">الدرجة الكلية</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="border rounded p-3 text-center">
            <div class="fw-bold fs-4">{{ $exam->passing_score }}%</div>
            <div class="text-muted small">درجة النجاح</div>
        </div>
    </div>
</div>

@if($exam->anti_cheat_enabled)
<div class="alert alert-info">
    <i class="bi bi-shield-exclamation"></i>
    هذا الامتحان مراقب: يُمنع النسخ واللصق والقص والنقر بزر الفأرة الأيمن وتبديل النوافذ أثناء الامتحان.
</div>
@endif

@forelse($exam->questions as $index => $q)
<div class="card mb-4 border">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-2">
            <span class="fw-bold">السؤال {{ $index + 1 }}</span>
            <span class="badge bg-light text-dark">{{ $q->pivot->marks_override ?? $q->marks }} درجة</span>
        </div>
        <p>{!! $q->question_text !!}</p>

        @if($q->image_path)
            <img src="{{ asset($q->image_path) }}" class="img-fluid rounded mb-3" style="max-height:200px">
        @endif
        @if($q->audio_path)
            <audio controls class="w-100 mb-3"><source src="{{ asset($q->audio_path) }}"></audio>
        @endif

        @if(in_array($q->type, ['mcq', 'true_false']))
            @foreach($q->options as $opt)
                <label class="d-block border rounded p-2 mb-2">
                    <input type="radio" disabled> {{ $opt->option_text }}
                </label>
            @endforeach
        @elseif($q->type === 'text')
            <textarea class="form-control" rows="3" disabled placeholder="سيكتب الطالب إجابته هنا..."></textarea>
        @elseif($q->type === 'voice')
            <button type="button" class="btn btn-danger btn-sm" disabled><i class="bi bi-mic-fill"></i> تسجيل</button>
            <span class="text-muted small ms-2">سيسجل الطالب إجابته الصوتية هنا</span>
        @endif
    </div>
</div>
@empty
<div class="alert alert-warning">لا توجد أسئلة مرتبطة بهذا الامتحان بعد — لن يظهر شيء للطالب حالياً.</div>
@endforelse
