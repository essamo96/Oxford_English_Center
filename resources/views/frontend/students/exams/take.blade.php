@extends('frontend.layouts.exam')
@section('title', $exam->title)

@section('css')
<style>
    .exam-question-card {
        background: #fff;
        border-radius: 14px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 1px 4px rgba(20,33,61,.08);
        border: 1px solid #e7eaf0;
    }
    .exam-question-card__head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
    .exam-question-card__number { font-weight: 700; color: #14213d; }
    .exam-question-card__marks { background: #eef1f7; color: #14213d; padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 600; }
    .exam-question-text { font-size: 16px; line-height: 1.8; color: #1b1f2a; margin-bottom: 16px; }

    .exam-option-label {
        cursor: pointer;
        display: block;
        padding: 12px 16px;
        border: 1px solid #dfe3ea;
        border-radius: 10px;
        margin-bottom: 10px;
        transition: background .15s, border-color .15s;
    }
    .exam-option-label:hover { background: #f7f9fc; }
    .exam-option-label input:checked + span { font-weight: 700; color: #14213d; }
    .exam-option-label:has(input:checked) { border-color: #14213d; background: #f2f4fa; }

    .exam-anti-cheat-notice {
        background: #fff7e6;
        border: 1px solid #ffe1a8;
        color: #7a5200;
        border-radius: 10px;
        padding: 12px 16px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .exam-submit-bar { display: flex; justify-content: flex-end; margin-top: 10px; }
    .exam-submit-btn {
        background: #1e8e5a; color: #fff; border: none; border-radius: 10px;
        padding: 12px 28px; font-weight: 700; font-size: 15px; cursor: pointer;
    }
    .exam-submit-btn:hover { background: #17703f; }

    .exam-violation-counter {
        background: rgba(255,255,255,.12);
        color: #fff;
        padding: 6px 14px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 14px;
    }
    .exam-violation-counter.exam-violation-counter--danger { background: #b3261e; }

    .exam-fullscreen-gate {
        position: fixed;
        inset: 0;
        background: #14213d;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        text-align: center;
    }
    .exam-fullscreen-gate__box { max-width: 420px; padding: 24px; }
    .exam-fullscreen-gate__box p { color: #cbd3e6; }

    /* Anti-cheat: block text selection on the exam content itself, but keep it
       usable inside actual answer inputs (typing/selecting your own answer is fine). */
    .exam-locked-area, .exam-locked-area * {
        user-select: none;
        -webkit-user-select: none;
    }
    .exam-locked-area textarea,
    .exam-locked-area input[type="text"],
    .exam-locked-area input[type="radio"] {
        user-select: text;
        -webkit-user-select: text;
    }
</style>
@stop

@section('content')
<div class="exam-shell__header">
    <h1 class="exam-shell__title">{{ $exam->title }}</h1>
    <div class="d-flex align-items-center gap-3">
        @if($exam->anti_cheat_enabled)
        <div class="exam-violation-counter" id="exam_violation_counter" title="عدد المخالفات المرصودة">
            <i class="bi bi-shield-exclamation"></i> <span id="exam_violation_count">0</span>/{{ $exam->anti_cheat_violation_limit }}
        </div>
        @endif
        <div class="exam-timer" id="exam_timer">--:--</div>
    </div>
</div>

@if($exam->anti_cheat_enabled)
<div class="exam-fullscreen-gate" id="exam_fullscreen_gate">
    <div class="exam-fullscreen-gate__box">
        <i class="bi bi-arrows-fullscreen fs-1 mb-3 d-block"></i>
        <h4 class="mb-2">اضغط للمتابعة بوضع ملء الشاشة</h4>
        <p class="text-muted mb-4">هذا الامتحان مراقب ويجب أن يبقى بملء الشاشة طوال المدة. الخروج من وضع ملء الشاشة يُحتسب كمخالفة.</p>
        <button type="button" id="exam_fullscreen_start_btn" class="exam-submit-btn">
            <i class="bi bi-play-fill"></i> متابعة الآن
        </button>
    </div>
</div>
@endif

<div class="exam-shell__body {{ $exam->anti_cheat_enabled ? 'exam-locked-area' : '' }}">
    @if($exam->anti_cheat_enabled)
    <div class="exam-anti-cheat-notice">
        <i class="bi bi-shield-exclamation"></i>
        هذا الامتحان مراقب: يُمنع النسخ واللصق والقص والنقر بزر الفأرة الأيمن وتبديل النوافذ أثناء الامتحان.
        عدد المخالفات المسموح: {{ $exam->anti_cheat_violation_limit }}
    </div>
    @endif

    <form id="exam_form">
        @csrf
        @foreach($questions as $index => $q)
        <div class="exam-question-card">
            <div class="exam-question-card__head">
                <span class="exam-question-card__number">السؤال {{ $index + 1 }}</span>
                <span class="exam-question-card__marks">{{ $q->marks }} درجة</span>
            </div>

            <div class="exam-question-text" dir="auto">{!! $q->question_text !!}</div>

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
                        <span dir="auto">{{ $opt->option_text }}</span>
                    </label>
                @endforeach
            @elseif($q->type === 'text')
                <textarea class="form-control answer-text" dir="auto" data-question="{{ $q->id }}" rows="4" placeholder="اكتب إجابتك هنا..."></textarea>
            @elseif($q->type === 'voice')
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-danger btn-sm record-btn" data-question="{{ $q->id }}"><i class="bi bi-mic-fill"></i> تسجيل</button>
                    <span class="record-status text-muted small" data-question="{{ $q->id }}">لم يتم التسجيل بعد</span>
                </div>
                <audio class="mt-2 d-none w-100 voice-preview" data-question="{{ $q->id }}" controls></audio>
            @endif
        </div>
        @endforeach

        <div class="exam-submit-bar">
            <button type="button" id="submit_exam_btn" class="exam-submit-btn">
                <i class="bi bi-check-circle"></i> تسليم الامتحان
            </button>
        </div>
    </form>
</div>
@stop

@section('js')
<script>
(function () {
    var attemptEnc = "{{ Crypt::encrypt($attempt->id) }}";
    var expiresAt = new Date("{{ $attempt->expires_at->toIso8601String() }}").getTime();
    var antiCheatEnabled = {{ $exam->anti_cheat_enabled ? 'true' : 'false' }};
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
        if (totalSec <= 60) timerEl.classList.add('exam-timer--danger');
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
                Swal.fire({ icon: 'error', title: 'تعذر الوصول إلى الميكروفون', confirmButtonText: 'حسناً' });
            });
        });
    });

    // ── Lightweight anti-cheat (no AI proctoring) ──
    var submitted = false;
    var violationLabels = {
        copy: 'نسخ', paste: 'لصق', cut: 'قص', right_click: 'زر الفأرة الأيمن / أدوات المطوّر',
        tab_switch: 'تبديل النافذة/التبويب', window_blur: 'الخروج من نافذة الامتحان',
        window_focus: 'العودة إلى نافذة الامتحان', fullscreen_exit: 'الخروج من وضع ملء الشاشة'
    };

    function reportViolation(type) {
        if (!antiCheatEnabled || submitted) return;
        $.post(urlFor('violation'), { type: type, _token: csrf }, function (data) {
            // always keep the on-screen counter (next to the timer) in sync, every single time
            var $counter = $('#exam_violation_count');
            var $counterBox = $('#exam_violation_counter');
            if ($counter.length) {
                $counter.text(data.violations_count);
                if (data.violations_count >= data.limit) $counterBox.addClass('exam-violation-counter--danger');
            }

            // always tell the student what just happened, not only once the limit is hit
            Swal.fire({
                toast: true, position: 'top', icon: 'warning', showConfirmButton: false, timer: 3000,
                title: 'تم رصد مخالفة: ' + (violationLabels[type] || type) + ' (' + data.violations_count + '/' + data.limit + ')'
            });

            if (data.exceeded) {
                if (data.action === 'auto_submit') {
                    Swal.fire({ icon: 'error', title: 'تم تجاوز عدد المخالفات المسموح', text: 'سيتم تسليم الامتحان تلقائياً', confirmButtonText: 'حسناً' });
                    doSubmit();
                } else if (data.action === 'notify_teacher') {
                    Swal.fire({
                        toast: true, position: 'top', icon: 'warning', showConfirmButton: false, timer: 3500,
                        title: 'تم إبلاغ المدرس بمحاولة غش محتملة'
                    });
                }
            }
        });
    }

    // ── Fullscreen gate: exam must run in fullscreen; leaving it counts as a violation ──
    var $gate = $('#exam_fullscreen_gate');
    function enterFullscreen() {
        var el = document.documentElement;
        var request = el.requestFullscreen || el.webkitRequestFullscreen || el.msRequestFullscreen;
        if (request) request.call(el).catch(function () {});
    }
    function isFullscreen() {
        return !!(document.fullscreenElement || document.webkitFullscreenElement || document.msFullscreenElement);
    }

    if (antiCheatEnabled && $gate.length) {
        $('#exam_fullscreen_start_btn').on('click', function () {
            enterFullscreen();
            $gate.fadeOut(150);
        });

        ['fullscreenchange', 'webkitfullscreenchange', 'msfullscreenchange'].forEach(function (evt) {
            document.addEventListener(evt, function () {
                if (submitted) return;
                if (!isFullscreen()) {
                    reportViolation('fullscreen_exit');
                    $gate.find('h4').text('غادرت وضع ملء الشاشة');
                    $gate.find('p').text('يجب العودة لوضع ملء الشاشة لمتابعة الامتحان. هذا يُحتسب كمخالفة.');
                    $gate.fadeIn(150);
                }
            });
        });
    }

    if (antiCheatEnabled) {
        document.addEventListener('copy', function (e) { e.preventDefault(); reportViolation('copy'); });
        document.addEventListener('paste', function (e) { e.preventDefault(); reportViolation('paste'); });
        document.addEventListener('cut', function (e) { e.preventDefault(); reportViolation('cut'); });
        document.addEventListener('contextmenu', function (e) { e.preventDefault(); reportViolation('right_click'); });
        document.addEventListener('visibilitychange', function () {
            if (document.hidden) reportViolation('tab_switch');
        });
        window.addEventListener('blur', function () { reportViolation('window_blur'); });
        window.addEventListener('focus', function () { reportViolation('window_focus'); });

        // extra layer: block common copy/paste/print/devtools keyboard shortcuts
        document.addEventListener('keydown', function (e) {
            var key = e.key ? e.key.toLowerCase() : '';
            var blockedCombo = (e.ctrlKey || e.metaKey) && ['c', 'v', 'x', 'u', 'p', 's'].indexOf(key) !== -1;
            var devTools = key === 'f12' || ((e.ctrlKey || e.metaKey) && e.shiftKey && ['i', 'j', 'c'].indexOf(key) !== -1);
            if (blockedCombo || devTools) {
                e.preventDefault();
                reportViolation(devTools ? 'right_click' : (key === 'c' ? 'copy' : (key === 'v' ? 'paste' : 'cut')));
            }
        });
    }

    // ── Submit ──
    document.getElementById('submit_exam_btn').addEventListener('click', function () {
        Swal.fire({
            title: 'هل أنت متأكد من تسليم الامتحان؟',
            text: 'لن تتمكن من التعديل بعد التسليم.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'نعم، سلّم الآن',
            cancelButtonText: 'إلغاء'
        }).then(function (result) {
            if (result.isConfirmed) doSubmit();
        });
    });

    function doSubmit() {
        if (submitted) return;
        submitted = true;
        clearInterval(timerInterval);
        if (document.exitFullscreen) { document.exitFullscreen().catch(function () {}); }
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
