@extends('frontend.layouts.dashboard')
@section('title', $exam->title)
@section('page-title', $exam->title)

@section('css')
<style>
    .exam-question-card { border-radius: 12px; }
    .exam-timer { font-variant-numeric: tabular-nums; font-weight: 800; font-size: 20px; }
    .exam-option-label { cursor: pointer; display: block; padding: 10px 14px; border: 1px solid #dee2e6; border-radius: 8px; margin-bottom: 8px; }
    .exam-option-label:hover { background: #f8f9fa; }
    .exam-option-label input:checked + span { font-weight: 700; }
    body.exam-locked { user-select: none; }
</style>
@stop

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">{{ $exam->title }}</h5>
    <div class="alert alert-warning py-2 px-3 mb-0 exam-timer" id="exam_timer">--:--</div>
</div>

@if($exam->anti_cheat_enabled)
<div class="alert alert-info">
    <i class="bi bi-shield-exclamation"></i>
    هذا الامتحان مراقب: يُمنع النسخ واللصق والقص والنقر بزر الفأرس الأيمن وتبديل النوافذ أثناء الامتحان.
    عدد المخالفات المسموح: {{ $exam->anti_cheat_violation_limit }}
</div>
@endif

<form id="exam_form">
    @csrf
    @foreach($questions as $index => $q)
    <div class="card exam-question-card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <span class="fw-bold">السؤال {{ $index + 1 }}</span>
                <span class="badge bg-light text-dark">{{ $q->marks }} درجة</span>
            </div>
            <p class="fs-6">{{ $q->question_text }}</p>

            @if($q->image_path)
                <img src="{{ asset($q->image_path) }}" class="img-fluid rounded mb-3" style="max-height:220px">
            @endif
            @if($q->audio_path)
                <audio controls class="w-100 mb-3"><source src="{{ asset($q->audio_path) }}"></audio>
            @endif

            @if(in_array($q->type, ['mcq', 'true_false']))
                @foreach($q->options as $opt)
                    <label class="exam-option-label">
                        <input type="radio" name="q_{{ $q->id }}" class="answer-input" data-question="{{ $q->id }}" value="{{ $opt->id }}"
                            {{ ($existingAnswers[$q->id] ?? null) == $opt->id ? 'checked' : '' }}>
                        <span>{{ $opt->option_text }}</span>
                    </label>
                @endforeach
            @elseif($q->type === 'text')
                <textarea class="form-control answer-text" data-question="{{ $q->id }}" rows="4" placeholder="اكتب إجابتك هنا..."></textarea>
            @elseif($q->type === 'voice')
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-danger btn-sm record-btn" data-question="{{ $q->id }}"><i class="bi bi-mic-fill"></i> تسجيل</button>
                    <span class="record-status text-muted small" data-question="{{ $q->id }}">لم يتم التسجيل بعد</span>
                </div>
                <audio class="mt-2 d-none w-100 voice-preview" data-question="{{ $q->id }}" controls></audio>
            @endif
        </div>
    </div>
    @endforeach

    <div class="d-flex justify-content-end">
        <button type="button" id="submit_exam_btn" class="btn btn-success btn-lg">
            <i class="bi bi-check-circle"></i> تسليم الامتحان
        </button>
    </div>
</form>
@stop

@section('js')
<script>
(function () {
    var attemptEnc = "{{ Crypt::encrypt($attempt->id) }}";
    var expiresAt = new Date("{{ $attempt->expires_at->toIso8601String() }}").getTime();
    var antiCheatEnabled = {{ $exam->anti_cheat_enabled ? 'true' : 'false' }};
    var violationAction = "{{ $exam->anti_cheat_action }}";
    var csrf = "{{ csrf_token() }}";

    function urlFor(name) {
        var routes = {
            answer: "{{ route('student.exams.answer', ['attempt' => 'ATTEMPT']) }}",
            voice: "{{ route('student.exams.voice_answer', ['attempt' => 'ATTEMPT']) }}",
            violation: "{{ route('student.exams.violation', ['attempt' => 'ATTEMPT']) }}",
            submit: "{{ route('student.exams.submit', ['attempt' => 'ATTEMPT']) }}"
        };
        return routes[name].replace('ATTEMPT', attemptEnc);
    }

    // ── Countdown timer ──
    var timerEl = document.getElementById('exam_timer');
    function tick() {
        var remainingMs = expiresAt - Date.now();
        if (remainingMs <= 0) {
            timerEl.textContent = '00:00';
            doSubmit();
            return;
        }
        var totalSec = Math.floor(remainingMs / 1000);
        var m = Math.floor(totalSec / 60), s = totalSec % 60;
        timerEl.textContent = (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
    }
    tick();
    var timerInterval = setInterval(tick, 1000);

    // ── Answer autosave ──
    document.querySelectorAll('.answer-input').forEach(function (el) {
        el.addEventListener('change', function () {
            $.post(urlFor('answer'), { question_id: el.dataset.question, selected_option_id: el.value, _token: csrf });
        });
    });
    document.querySelectorAll('.answer-text').forEach(function (el) {
        var timeout;
        el.addEventListener('input', function () {
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                $.post(urlFor('answer'), { question_id: el.dataset.question, answer_text: el.value, _token: csrf });
            }, 800);
        });
    });

    // ── Voice recording ──
    document.querySelectorAll('.record-btn').forEach(function (btn) {
        var questionId = btn.dataset.question;
        var mediaRecorder, chunks = [];
        btn.addEventListener('click', function () {
            if (btn.classList.contains('recording')) {
                mediaRecorder.stop();
                return;
            }
            navigator.mediaDevices.getUserMedia({ audio: true }).then(function (stream) {
                chunks = [];
                mediaRecorder = new MediaRecorder(stream);
                mediaRecorder.ondataavailable = function (e) { chunks.push(e.data); };
                mediaRecorder.onstop = function () {
                    stream.getTracks().forEach(function (t) { t.stop(); });
                    var blob = new Blob(chunks, { type: 'audio/webm' });
                    var preview = document.querySelector('.voice-preview[data-question="' + questionId + '"]');
                    preview.src = URL.createObjectURL(blob);
                    preview.classList.remove('d-none');

                    var fd = new FormData();
                    fd.append('question_id', questionId);
                    fd.append('audio', blob, 'answer.webm');
                    fd.append('_token', csrf);
                    $.ajax({ url: urlFor('voice'), type: 'POST', data: fd, processData: false, contentType: false });

                    document.querySelector('.record-status[data-question="' + questionId + '"]').textContent = 'تم التسجيل بنجاح';
                    btn.classList.remove('recording');
                    btn.innerHTML = '<i class="bi bi-mic-fill"></i> إعادة التسجيل';
                };
                mediaRecorder.start();
                btn.classList.add('recording');
                btn.innerHTML = '<i class="bi bi-stop-fill"></i> إيقاف التسجيل';
            }).catch(function () {
                toastr.error('تعذر الوصول إلى الميكروفون');
            });
        });
    });

    // ── Lightweight anti-cheat (no AI proctoring): copy/paste/cut/right-click/tab-switch/blur/focus ──
    var submitted = false;
    function reportViolation(type) {
        if (!antiCheatEnabled || submitted) return;
        $.post(urlFor('violation'), { type: type, _token: csrf }, function (data) {
            if (data.exceeded) {
                if (data.action === 'auto_submit') {
                    toastr.error('تم تجاوز عدد المخالفات المسموح، سيتم تسليم الامتحان تلقائياً');
                    doSubmit();
                } else if (data.action === 'warning') {
                    toastr.warning('تحذير: تم رصد ' + data.violations_count + ' مخالفة من أصل ' + data.limit + ' مسموح بها');
                } else if (data.action === 'notify_teacher') {
                    toastr.warning('تم إبلاغ المدرس بمحاولة غش محتملة');
                }
            }
        });
    }

    if (antiCheatEnabled) {
        document.addEventListener('copy', function () { reportViolation('copy'); });
        document.addEventListener('paste', function (e) { e.preventDefault(); reportViolation('paste'); });
        document.addEventListener('cut', function () { reportViolation('cut'); });
        document.addEventListener('contextmenu', function (e) { e.preventDefault(); reportViolation('right_click'); });
        document.addEventListener('visibilitychange', function () {
            if (document.hidden) reportViolation('tab_switch');
        });
        window.addEventListener('blur', function () { reportViolation('window_blur'); });
        window.addEventListener('focus', function () { reportViolation('window_focus'); });
    }

    // ── Submit ──
    document.getElementById('submit_exam_btn').addEventListener('click', function () {
        if (!confirm('هل أنت متأكد من تسليم الامتحان؟ لن تتمكن من التعديل بعد التسليم.')) return;
        doSubmit();
    });

    function doSubmit() {
        if (submitted) return;
        submitted = true;
        clearInterval(timerInterval);
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = urlFor('submit');
        form.innerHTML = '<input type="hidden" name="_token" value="' + csrf + '">';
        document.body.appendChild(form);
        form.submit();
    }

})();
</script>
@stop
