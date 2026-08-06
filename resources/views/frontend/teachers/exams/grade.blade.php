@extends('frontend.layouts.dashboard')
@section('title', 'تصحيح المحاولة')
@section('page-title', 'تصحيح المحاولة')

@section('css')
@include('frontend.teachers.exams.parts.design')
<style>
    .grade-q-card { background: #fff; border: 1px solid var(--tex-border); border-radius: 14px; margin-bottom: 16px; overflow: hidden; }
    .grade-q-card__num { width: 32px; height: 32px; border-radius: 8px; background: var(--tex-navy); color: var(--tex-accent); display: inline-flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px; flex-shrink: 0; }
    .grade-answer-box { background: #f9fafc; border: 1px solid var(--tex-border); border-radius: 10px; padding: 14px; }
    .grade-sticky-bar { position: sticky; bottom: 0; background: #fff; border-top: 1px solid var(--tex-border); padding: 14px 20px; margin: 0 -20px -20px; border-radius: 0 0 14px 14px; }
</style>
@stop

@section('content')
<div class="tex-page-head">
    <div class="tex-page-head__title">
        <span class="tex-page-head__icon"><i class="bi bi-pencil-square"></i></span>
        <div>
            <h4>{{ $attempt->exam->title }}</h4>
            <p><i class="bi bi-person"></i> {{ $attempt->student->name ?? '—' }}
                &nbsp;·&nbsp; <i class="bi bi-lightning-charge"></i> الدرجة التلقائية: {{ $attempt->auto_score }} / {{ $attempt->total_marks }}
            </p>
        </div>
    </div>
    <a href="{{ route('teacher.exam_reviews.view') }}" class="tex-icon-btn" title="رجوع"><i class="bi bi-arrow-right"></i></a>
</div>

@if($attempt->violations_count > 0)
@php
    $violationLabels = ['copy' => 'نسخ', 'paste' => 'لصق', 'cut' => 'قص', 'right_click' => 'زر الفأرة الأيمن/أدوات المطوّر', 'tab_switch' => 'تبديل النافذة/التبويب', 'window_blur' => 'خروج من النافذة', 'window_focus' => 'عودة للنافذة', 'fullscreen_exit' => 'خروج من ملء الشاشة'];
    $violationCounts = $attempt->violations->groupBy('type')->map->count();
@endphp
<div class="tex-card" style="border-color:#f5c6cb;">
    <div class="tex-card__body">
        <p class="tex-card__header-title text-danger mb-3"><i class="bi bi-shield-exclamation"></i> مخالفات مراقبة الغش: {{ $attempt->violations_count }}</p>
        <div class="d-flex flex-wrap gap-2">
            @foreach($violationCounts as $type => $count)
                <span class="tex-pill tex-pill--danger">{{ $violationLabels[$type] ?? $type }}: {{ $count }}</span>
            @endforeach
        </div>
        @if($attempt->is_auto_submitted)
            <div class="text-danger small mt-3"><i class="bi bi-exclamation-triangle"></i> تم تسليم هذه المحاولة تلقائياً بسبب تجاوز حد المخالفات.</div>
        @endif
    </div>
</div>
@endif

@php $gradableCount = $attempt->answers->filter(fn($a) => in_array($a->question->type, ['text', 'voice']))->count(); @endphp

<form method="post" action="{{ route('teacher.exam_reviews.grade', ['id' => Crypt::encrypt($attempt->id)]) }}">
    {{ csrf_field() }}

    @if($gradableCount === 0)
        <div class="tex-empty tex-card">
            <i class="bi bi-emoji-smile"></i>
            <p class="fw-semibold mb-1">لا توجد أسئلة تحتاج تصحيحاً يدوياً في هذه المحاولة</p>
        </div>
    @else
        @foreach($attempt->answers as $index => $answer)
            @php $q = $answer->question; @endphp
            @continue(!in_array($q->type, ['text', 'voice']))
            <div class="grade-q-card">
                <div class="tex-card__header">
                    <p class="tex-card__header-title mb-0"><span class="grade-q-card__num">{{ $index + 1 }}</span> {{ \Illuminate\Support\Str::limit(strip_tags($q->question_text), 90) }}</p>
                    <span class="tex-pill tex-pill--info">{{ $q->type === 'text' ? 'إجابة نصية' : 'إجابة صوتية' }}</span>
                </div>
                <div class="tex-card__body">
                    @if($q->type === 'text')
                        <div class="grade-answer-box mb-3">{{ $answer->answer_text ?: 'لم يجب الطالب' }}</div>
                    @else
                        @if($answer->answer_audio_path)
                            <audio controls class="w-100 mb-3"><source src="{{ asset($answer->answer_audio_path) }}"></audio>
                        @else
                            <div class="grade-answer-box mb-3 text-muted">لم يسجل الطالب إجابة صوتية</div>
                        @endif
                    @endif
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">الدرجة (من {{ $q->marks }})</label>
                            <input type="number" step="0.5" min="0" max="{{ $q->marks }}" name="marks[{{ $answer->id }}]" class="form-control" value="{{ $answer->marks_awarded }}">
                        </div>
                        <div class="col-md-9">
                            <label class="form-label fw-semibold small">تعليق للطالب</label>
                            <input type="text" name="comments[{{ $answer->id }}]" class="form-control" value="{{ $answer->teacher_comment }}" placeholder="اختياري">
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="tex-card">
            <div class="grade-sticky-bar d-flex gap-2">
                <button type="submit" class="tex-btn-primary"><i class="bi bi-check-circle"></i> حفظ التصحيح</button>
                <a href="{{ route('teacher.exam_reviews.view') }}" class="btn btn-light">إلغاء</a>
            </div>
        </div>
    @endif
</form>
@stop
