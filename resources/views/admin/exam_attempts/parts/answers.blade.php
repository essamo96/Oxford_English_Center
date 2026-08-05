{{-- Read-only review of a student's attempt: all answers marked correct/incorrect, or only the wrong ones --}}
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h5 class="mb-1">{{ $attempt->exam->title }}</h5>
        <span class="text-muted">الطالب: {{ $attempt->student->name ?? '—' }} | الدرجة: {{ $attempt->final_score ?? $attempt->auto_score }} / {{ $attempt->total_marks }} ({{ $attempt->percentage ?? '—' }}%)</span>
    </div>
    @if($attempt->violations_count > 0)
        <span class="badge badge-light-danger"><i class="bi bi-shield-exclamation"></i> {{ $attempt->violations_count }} مخالفة</span>
    @endif
</div>

@php
    $typeLabels = ['mcq' => 'اختيار من متعدد', 'true_false' => 'صح/خطأ', 'text' => 'إجابة نصية', 'voice' => 'إجابة صوتية'];
    $answers = $attempt->answers;
    if ($onlyWrong) {
        $answers = $answers->filter(fn($a) => $a->is_correct === false);
    }
@endphp

@forelse($answers as $index => $answer)
    @php $q = $answer->question; @endphp
    <div class="card mb-3 border {{ $answer->is_correct === false ? 'border-danger' : ($answer->is_correct === true ? 'border-success' : '') }}">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
                <span class="fw-bold" dir="auto">{{ $index + 1 }}. {!! $q->question_text !!}</span>
                <div class="d-flex gap-1 flex-shrink-0">
                    <span class="badge bg-light text-dark">{{ $typeLabels[$q->type] ?? $q->type }}</span>
                    @if($answer->is_correct === true)
                        <span class="badge badge-light-success"><i class="bi bi-check-circle"></i> صحيحة</span>
                    @elseif($answer->is_correct === false)
                        <span class="badge badge-light-danger"><i class="bi bi-x-circle"></i> خاطئة</span>
                    @else
                        <span class="badge badge-light-warning">بانتظار التصحيح</span>
                    @endif
                    <span class="badge bg-light-primary text-primary">{{ $answer->marks_awarded ?? 0 }} / {{ $q->marks }}</span>
                </div>
            </div>

            @if(in_array($q->type, ['mcq', 'true_false']))
                <ul class="list-unstyled mb-0 mt-2" dir="auto">
                    @foreach($q->options as $opt)
                        <li class="{{ $opt->is_correct ? 'text-success fw-bold' : ($opt->id == $answer->selected_option_id ? 'text-danger' : 'text-muted') }}">
                            @if($opt->id == $answer->selected_option_id)<i class="bi bi-arrow-left-short"></i>@endif
                            {{ $opt->option_text }}
                            @if($opt->is_correct)<i class="bi bi-check-circle-fill text-success"></i>@endif
                        </li>
                    @endforeach
                </ul>
            @elseif($q->type === 'text')
                <div class="border rounded p-2 bg-light mt-2" dir="auto">{{ $answer->answer_text ?: 'لم يجب الطالب' }}</div>
            @elseif($q->type === 'voice')
                @if($answer->answer_audio_path)
                    <audio controls class="w-100 mt-2"><source src="{{ asset($answer->answer_audio_path) }}"></audio>
                @else
                    <div class="text-muted mt-2">لم يسجل الطالب إجابة صوتية</div>
                @endif
            @endif

            @if($q->explanation)
                <div class="alert alert-light border mt-2 mb-0 py-2"><strong>الشرح:</strong> {!! $q->explanation !!}</div>
            @endif
            @if($answer->teacher_comment)
                <div class="alert alert-info mt-2 mb-0 py-2"><strong>تعليق المدرس:</strong> {{ $answer->teacher_comment }}</div>
            @endif
        </div>
    </div>
@empty
    <div class="alert alert-success text-center">
        @if($onlyWrong)
            <i class="bi bi-emoji-smile fs-2 d-block mb-2"></i> لا توجد إجابات خاطئة — أحسنت!
        @else
            لا توجد إجابات مسجلة لهذه المحاولة.
        @endif
    </div>
@endforelse
