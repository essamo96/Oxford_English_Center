@extends('frontend.layouts.dashboard')
@section('title', 'تصحيح المحاولة')
@section('page-title', 'تصحيح المحاولة')

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="mb-1">{{ $attempt->exam->title }}</h5>
        <p class="text-muted mb-0">الطالب: {{ $attempt->student->name ?? '—' }} | الدرجة التلقائية: {{ $attempt->auto_score }} / {{ $attempt->total_marks }}</p>
    </div>
</div>

<form method="post" action="{{ route('teacher.exam_reviews.grade', ['id' => Crypt::encrypt($attempt->id)]) }}">
    {{ csrf_field() }}
    @foreach($attempt->answers as $answer)
        @php $q = $answer->question; @endphp
        @continue(!in_array($q->type, ['text', 'voice']))
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <p class="fw-bold">{{ $q->question_text }}</p>
                @if($q->type === 'text')
                    <div class="border rounded p-3 bg-light mb-3">{{ $answer->answer_text ?: 'لم يجب الطالب' }}</div>
                @else
                    @if($answer->answer_audio_path)
                        <audio controls class="w-100 mb-3"><source src="{{ asset($answer->answer_audio_path) }}"></audio>
                    @else
                        <div class="text-muted mb-3">لم يسجل الطالب إجابة صوتية</div>
                    @endif
                @endif
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">الدرجة (من {{ $q->marks }})</label>
                        <input type="number" step="0.5" min="0" max="{{ $q->marks }}" name="marks[{{ $answer->id }}]" class="form-control" value="{{ $answer->marks_awarded }}">
                    </div>
                    <div class="col-md-9">
                        <label class="form-label">تعليق للطالب</label>
                        <input type="text" name="comments[{{ $answer->id }}]" class="form-control" value="{{ $answer->teacher_comment }}">
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    <button type="submit" class="btn btn-primary">حفظ التصحيح</button>
    <a href="{{ route('teacher.exam_reviews.view') }}" class="btn btn-light">إلغاء</a>
</form>
@stop
