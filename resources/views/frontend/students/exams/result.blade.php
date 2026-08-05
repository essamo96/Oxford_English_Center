@extends('frontend.layouts.dashboard')
@section('title', 'نتيجة الامتحان')
@section('page-title', 'نتيجة الامتحان')

@section('content')
@php
    $exam = $attempt->exam;
    $canSeeResult = $exam->result_visibility === 'immediate' || $attempt->status === 'graded';
@endphp
<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">{{ $exam->title }}</h5>
    </div>
    <div class="card-body">
        @if($attempt->status === 'submitted' && !$canSeeResult)
            <div class="alert alert-info">تم تسليم إجاباتك بنجاح. سيتم إعلان النتيجة بعد مراجعة المدرس للأسئلة المقالية/الصوتية.</div>
        @else
            <div class="row text-center mb-4">
                <div class="col-md-4">
                    <div class="fs-2 fw-bold">{{ $attempt->final_score ?? $attempt->auto_score }}</div>
                    <div class="text-muted">الدرجة من {{ $attempt->total_marks }}</div>
                </div>
                <div class="col-md-4">
                    <div class="fs-2 fw-bold">{{ $attempt->percentage ?? '—' }}%</div>
                    <div class="text-muted">النسبة المئوية</div>
                </div>
                <div class="col-md-4">
                    @if($attempt->percentage !== null)
                        <div class="fs-2 fw-bold {{ $attempt->percentage >= $exam->passing_score ? 'text-success' : 'text-danger' }}">
                            {{ $attempt->percentage >= $exam->passing_score ? 'ناجح' : 'راسب' }}
                        </div>
                    @endif
                    <div class="text-muted">النتيجة</div>
                </div>
            </div>

            @if($attempt->recommended_level)
                <div class="alert alert-success text-center fs-5">
                    المستوى الموصى به: <strong>{{ $attempt->recommended_level }}</strong>
                </div>
            @endif

            @if($exam->review_available)
                <hr>
                <h6 class="fw-bold mb-3">مراجعة الإجابات</h6>
                @foreach($attempt->answers as $answer)
                    @php $q = $answer->question; @endphp
                    <div class="card mb-3">
                        <div class="card-body">
                            <p class="fw-bold">{{ $q->question_text }}</p>
                            @if(in_array($q->type, ['mcq', 'true_false']))
                                @foreach($q->options as $opt)
                                    <div class="{{ $opt->is_correct ? 'text-success fw-bold' : ($opt->id == $answer->selected_option_id ? 'text-danger' : '') }}">
                                        {{ $opt->id == $answer->selected_option_id ? '➜' : '' }} {{ $opt->option_text }}
                                        @if($opt->is_correct) <i class="bi bi-check-circle text-success"></i> @endif
                                    </div>
                                @endforeach
                            @elseif($q->type === 'text')
                                <div class="border rounded p-2 bg-light">{{ $answer->answer_text }}</div>
                            @elseif($q->type === 'voice' && $answer->answer_audio_path)
                                <audio controls class="w-100"><source src="{{ asset($answer->answer_audio_path) }}"></audio>
                            @endif

                            @if($q->explanation)
                                <div class="alert alert-light border mt-2 mb-0"><strong>الشرح:</strong> {{ $q->explanation }}</div>
                            @endif
                            @if($answer->teacher_comment)
                                <div class="alert alert-info mt-2 mb-0"><strong>تعليق المدرس:</strong> {{ $answer->teacher_comment }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach

                <form id="review_request_form" class="mt-4">
                    <label class="form-label fw-bold">طلب مراجعة</label>
                    <textarea name="message" class="form-control mb-2" rows="2" placeholder="اشرح سبب طلب المراجعة..."></textarea>
                    <button type="button" id="send_review_request" class="btn btn-outline-primary btn-sm">إرسال طلب المراجعة</button>
                </form>
            @endif
        @endif

        <a href="{{ route('student.exams.view') }}" class="btn btn-light mt-4">رجوع لقائمة الامتحانات</a>
    </div>
</div>
@stop

@section('js')
<script>
$(document).on('click', '#send_review_request', function () {
    var message = $('#review_request_form textarea[name="message"]').val();
    $.post("{{ route('student.exams.request_review', ['attempt' => Crypt::encrypt($attempt->id)]) }}", {
        message: message, _token: '{{ csrf_token() }}'
    }, function (data) {
        toastr[data.status](data.message);
    });
});
</script>
@stop
