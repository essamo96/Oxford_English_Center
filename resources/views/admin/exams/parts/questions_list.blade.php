{{-- Admin-facing review of the exam's questions, with correct answers marked --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">أسئلة الامتحان: {{ $exam->title }}</h5>
    <span class="badge bg-primary">{{ $exam->questions->count() }} سؤال</span>
</div>

@php
    $typeLabels = ['mcq' => 'اختيار من متعدد', 'true_false' => 'صح/خطأ', 'text' => 'إجابة نصية', 'voice' => 'إجابة صوتية'];
    $difficultyLabels = ['easy' => 'سهل', 'medium' => 'متوسط', 'hard' => 'صعب', 'custom' => 'مخصص'];
@endphp

@forelse($exam->questions as $index => $q)
<div class="card mb-3 border">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
            <span class="fw-bold">{{ $index + 1 }}. {!! $q->question_text !!}</span>
            <div class="d-flex gap-1 flex-shrink-0">
                <span class="badge bg-light text-dark">{{ $typeLabels[$q->type] ?? $q->type }}</span>
                <span class="badge bg-light text-dark">{{ $difficultyLabels[$q->difficulty] ?? $q->difficulty }}</span>
                @if($q->skill)<span class="badge bg-light-info text-info">{{ $q->skill->name_ar }}</span>@endif
                <span class="badge bg-light-primary text-primary">{{ $q->pivot->marks_override ?? $q->marks }} درجة</span>
            </div>
        </div>

        @if(in_array($q->type, ['mcq', 'true_false']))
            <ul class="list-unstyled mb-0 mt-2">
                @foreach($q->options as $opt)
                    <li class="{{ $opt->is_correct ? 'text-success fw-bold' : 'text-muted' }}">
                        @if($opt->is_correct)<i class="bi bi-check-circle-fill text-success"></i>@else<i class="bi bi-circle text-muted"></i>@endif
                        {{ $opt->option_text }}
                    </li>
                @endforeach
            </ul>
        @elseif($q->type === 'text')
            <div class="text-muted small mt-2"><i class="bi bi-pencil-square"></i> يتطلب تصحيحاً يدوياً (إجابة نصية)</div>
        @elseif($q->type === 'voice')
            <div class="text-muted small mt-2"><i class="bi bi-mic-fill"></i> يتطلب تصحيحاً يدوياً (إجابة صوتية)</div>
        @endif

        @if($q->explanation)
            <div class="alert alert-light border mt-2 mb-0 py-2"><strong>الشرح:</strong> {!! $q->explanation !!}</div>
        @endif
    </div>
</div>
@empty
<div class="alert alert-warning">لا توجد أسئلة مرتبطة بهذا الامتحان بعد.</div>
@endforelse
