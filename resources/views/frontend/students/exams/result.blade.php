@extends('frontend.layouts.exam')
@section('title', 'نتيجة الامتحان')

@php
    $exam = $attempt->exam;
    $canSeeResult = $exam->result_visibility === 'immediate' || $attempt->status === 'graded';
    $passed = $attempt->percentage !== null && $attempt->percentage >= $exam->passing_score;
@endphp

@section('css')
<style>
    .exam-result-card {
        background: #fff; border-radius: 14px; padding: 28px;
        box-shadow: 0 1px 4px rgba(20,33,61,.08); border: 1px solid #e7eaf0;
        max-width: 680px; margin: 0 auto 20px;
    }
    .exam-result-score { text-align: center; padding: 10px 0 24px; }
    .exam-result-score__value { font-size: 44px; font-weight: 800; color: #14213d; line-height: 1; }
    .exam-result-score__badge {
        display: inline-block; margin-top: 10px; padding: 6px 20px; border-radius: 20px;
        font-weight: 700; font-size: 14px;
    }
    .exam-result-score__badge--pass { background: #e6f6ec; color: #1e8e5a; }
    .exam-result-score__badge--fail { background: #fdeaea; color: #c0392b; }
    .exam-result-grid { display: flex; justify-content: center; gap: 40px; margin-top: 16px; flex-wrap: wrap; }
    .exam-result-grid__item { text-align: center; }
    .exam-result-grid__value { font-size: 20px; font-weight: 700; color: #14213d; }
    .exam-result-grid__label { font-size: 13px; color: #7a8296; }

    .exam-review-toggle-btn {
        display: block; width: 100%; background: #ffcc00; color: #14213d; border: none;
        border-radius: 10px; padding: 14px; font-weight: 800; font-size: 15px; cursor: pointer; margin-top: 10px;
    }
    .exam-review-toggle-btn:hover { background: #e0b400; }

    .exam-review-answer-card {
        background: #fff; border-radius: 12px; padding: 20px; margin-bottom: 14px;
        border: 1px solid #e7eaf0;
    }
    .exam-review-answer-card__q { font-weight: 700; color: #14213d; margin-bottom: 10px; }
    .exam-back-link { display: inline-block; margin-top: 20px; color: #14213d; font-weight: 600; text-decoration: none; }
    .exam-back-link:hover { text-decoration: underline; }
</style>
@stop

@section('content')
<div class="exam-shell__header">
    <h1 class="exam-shell__title">{{ $exam->title }}</h1>
</div>

<div class="exam-shell__body">
    @if($attempt->status === 'submitted' && !$canSeeResult)
        <div class="exam-result-card text-center">
            <i class="bi bi-check-circle-fill fs-1 text-success mb-3 d-block"></i>
            <h4 class="mb-2">تم تسليم إجاباتك بنجاح</h4>
            <p class="text-muted mb-0">سيتم إعلان النتيجة بعد مراجعة المدرس للأسئلة المقالية/الصوتية.</p>
        </div>
    @else
        <div class="exam-result-card">
            <div class="exam-result-score">
                <div class="exam-result-score__value">{{ $attempt->final_score ?? $attempt->auto_score }} / {{ $attempt->total_marks }}</div>
                @if($attempt->percentage !== null)
                    <span class="exam-result-score__badge {{ $passed ? 'exam-result-score__badge--pass' : 'exam-result-score__badge--fail' }}">
                        {{ $passed ? 'ناجح' : 'راسب' }} — {{ $attempt->percentage }}%
                    </span>
                @endif
            </div>

            <div class="exam-result-grid">
                <div class="exam-result-grid__item">
                    <div class="exam-result-grid__value">{{ $attempt->answers->count() }}</div>
                    <div class="exam-result-grid__label">عدد الأسئلة</div>
                </div>
                <div class="exam-result-grid__item">
                    <div class="exam-result-grid__value">{{ $exam->passing_score }}%</div>
                    <div class="exam-result-grid__label">درجة النجاح</div>
                </div>
                @if($attempt->recommended_level)
                <div class="exam-result-grid__item">
                    <div class="exam-result-grid__value">{{ $attempt->recommended_level }}</div>
                    <div class="exam-result-grid__label">المستوى الموصى به</div>
                </div>
                @endif
            </div>

            @if($exam->review_available)
                <button type="button" id="toggle_review_btn" class="exam-review-toggle-btn">
                    <i class="bi bi-list-check"></i> عرض تفاصيل الإجابات والأخطاء
                </button>
            @endif
        </div>

        @if($exam->review_available)
        <div id="review_details" style="display:none; max-width:680px; margin:0 auto;">
            @foreach($attempt->answers as $answer)
                @php $q = $answer->question; @endphp
                <div class="exam-review-answer-card">
                    <div class="exam-review-answer-card__q" dir="auto">{!! $q->question_text !!}</div>
                    @if(in_array($q->type, ['mcq', 'true_false']))
                        @foreach($q->options as $opt)
                            <div class="{{ $opt->is_correct ? 'text-success fw-bold' : ($opt->id == $answer->selected_option_id ? 'text-danger' : '') }}" dir="auto">
                                {{ $opt->id == $answer->selected_option_id ? '➜' : '' }} {{ $opt->option_text }}
                                @if($opt->is_correct) <i class="bi bi-check-circle text-success"></i> @endif
                            </div>
                        @endforeach
                    @elseif($q->type === 'text')
                        <div class="border rounded p-2 bg-light" dir="auto">{{ $answer->answer_text }}</div>
                    @elseif($q->type === 'voice' && $answer->answer_audio_path)
                        <audio controls class="w-100"><source src="{{ asset($answer->answer_audio_path) }}"></audio>
                    @endif

                    @if($q->explanation)
                        <div class="alert alert-light border mt-2 mb-0"><strong>الشرح:</strong> {!! $q->explanation !!}</div>
                    @endif
                    @if($answer->teacher_comment)
                        <div class="alert alert-info mt-2 mb-0"><strong>تعليق المدرس:</strong> {{ $answer->teacher_comment }}</div>
                    @endif
                </div>
            @endforeach

            <div class="exam-result-card">
                <label class="fw-bold mb-2 d-block">طلب مراجعة</label>
                <textarea id="review_request_message" class="form-control mb-2" rows="2" placeholder="اشرح سبب طلب المراجعة..."></textarea>
                <button type="button" id="send_review_request" class="btn btn-outline-dark btn-sm">إرسال طلب المراجعة</button>
            </div>
        </div>
        @endif
    @endif

    <div class="text-center">
        <a href="{{ route('student.exams.view') }}" class="exam-back-link"><i class="bi bi-arrow-right"></i> رجوع لقائمة الامتحانات</a>
    </div>
</div>
@stop

@section('js')
<script>
$('#toggle_review_btn').on('click', function () {
    var $details = $('#review_details');
    var opening = $details.is(':hidden');
    $details.slideToggle(200);
    $(this).html(opening
        ? '<i class="bi bi-eye-slash"></i> إخفاء تفاصيل الإجابات'
        : '<i class="bi bi-list-check"></i> عرض تفاصيل الإجابات والأخطاء');
    if (opening) $details[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
});

$(document).on('click', '#send_review_request', function () {
    var message = $('#review_request_message').val();
    $.post("{{ route('student.exams.request_review', ['attempt' => Crypt::encrypt($attempt->id)]) }}", {
        message: message, _token: '{{ csrf_token() }}'
    }, function (data) {
        Swal.fire({
            toast: true, position: 'top', showConfirmButton: false, timer: 3000,
            icon: data.status, title: data.message
        });
    });
});
</script>
@stop
