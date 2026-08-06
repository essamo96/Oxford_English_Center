<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamAttemptAnswer;
use App\Models\ExamViolation;
use App\Models\ExamReviewRequest;
use App\Models\ExamLevelRange;
use App\Models\GroupStudents;

class StudentExamsController extends Controller
{
    const NOT_FOUND = "عذراً، لا يمكن العثور على البيانات";
    const EXECUTION_ERROR = "عذراً، حدث خطأ أثناء تنفيذ العملية";

    private function studentGroupIds(): array
    {
        return GroupStudents::where('student_id', Auth::guard('students')->id())->pluck('group_id')->toArray();
    }

    // Exams available to the logged-in student: published Placement Tests, plus published
    // Group Exams for groups the student is actually enrolled in.
    public function getIndex()
    {
        $studentId = Auth::guard('students')->id();
        $now = now();

        $exams = Exam::with('group')
            ->where('status', 'published')
            ->where(function ($q) use ($now) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            })
            ->where(function ($q) {
                $q->where('category', 'placement')
                  ->orWhere(function ($qq) {
                      $qq->where('category', 'group')->whereIn('group_id', $this->studentGroupIds());
                  });
            })
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($exam) use ($studentId) {
                $exam->my_attempts_count = ExamAttempt::where('exam_id', $exam->id)->where('student_id', $studentId)->count();
                return $exam;
            });

        return view('frontend.students.exams.view', array_merge(parent::$data, ['exams' => $exams]));
    }

    // Creates (or resumes) an attempt and redirects to the taking screen.
    public function start(Request $request, $id)
    {
        try {
            $examId = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            abort(404);
        }

        $studentId = Auth::guard('students')->id();
        $exam = Exam::where('status', 'published')->find($examId);
        if (!$exam) {
            abort(404, self::NOT_FOUND);
        }

        if ($exam->category === 'group' && !in_array($exam->group_id, $this->studentGroupIds())) {
            abort(403);
        }

        $existing = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', $studentId)
            ->where('status', 'in_progress')
            ->first();

        if ($existing) {
            return redirect(route('student.exams.take', ['attempt' => Crypt::encrypt($existing->id)]));
        }

        $attemptsUsed = ExamAttempt::where('exam_id', $exam->id)->where('student_id', $studentId)->count();
        if ($attemptsUsed >= $exam->max_attempts) {
            return redirect(route('student.exams.view'))->with('danger', 'لقد استنفذت عدد المحاولات المسموح بها لهذا الامتحان');
        }

        $questions = $exam->questions;
        if ($exam->shuffle_questions) {
            $questions = $questions->shuffle();
        }

        $attempt = ExamAttempt::create([
            'exam_id' => $exam->id,
            'student_id' => $studentId,
            'attempt_number' => $attemptsUsed + 1,
            'status' => 'in_progress',
            'started_at' => now(),
            'expires_at' => now()->addMinutes($exam->duration_minutes),
            'total_marks' => $questions->sum(fn($q) => $q->pivot->marks_override ?? $q->marks),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect(route('student.exams.take', ['attempt' => Crypt::encrypt($attempt->id)]));
    }

    public function take(Request $request, $id)
    {
        try {
            $attemptId = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            abort(404);
        }

        $attempt = ExamAttempt::with(['exam', 'student'])->where('student_id', Auth::guard('students')->id())->find($attemptId);
        if (!$attempt) {
            abort(404, self::NOT_FOUND);
        }

        if (!$attempt->exam) {
            return redirect(route('student.exams.view'))->with('danger', 'هذا الامتحان لم يعد متاحاً.');
        }

        if ($attempt->status !== 'in_progress') {
            return redirect(route('student.exams.result', ['attempt' => Crypt::encrypt($attempt->id)]));
        }

        if ($attempt->expires_at && now()->greaterThan($attempt->expires_at)) {
            $this->finalizeAttempt($attempt, true);
            return redirect(route('student.exams.result', ['attempt' => Crypt::encrypt($attempt->id)]));
        }

        $exam = $attempt->exam;
        $questions = $exam->questions()->get();
        if ($exam->shuffle_questions) {
            $questions = $questions->shuffle();
        }
        if ($exam->shuffle_answers) {
            $questions->each(function ($q) {
                $q->setRelation('options', $q->options->shuffle());
            });
        }

        $existingAnswers = ExamAttemptAnswer::where('attempt_id', $attempt->id)->pluck('selected_option_id', 'question_id');

        return view('frontend.students.exams.take', array_merge(parent::$data, [
            'attempt' => $attempt,
            'exam' => $exam,
            'questions' => $questions,
            'existingAnswers' => $existingAnswers,
        ]));
    }

    // AJAX: called periodically while an exam is open (long exams — e.g. 2 hours — can outlive
    // the default session lifetime). Touching the session here resets its expiry clock and
    // returns a fresh CSRF token for the page's JS to swap in, so autosave/submit never start
    // silently failing with 419 errors partway through a long exam.
    public function pingSession(Request $request)
    {
        $request->session()->put('exam_keepalive_at', now());
        return response()->json(['status' => 'success', 'csrf_token' => csrf_token()]);
    }

    // AJAX: save a single answer as the student progresses through the exam.
    public function saveAnswer(Request $request, $id)
    {
        try {
            $attemptId = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error']);
        }

        $attempt = ExamAttempt::where('student_id', Auth::guard('students')->id())->where('status', 'in_progress')->find($attemptId);
        if (!$attempt) {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }

        $questionId = $request->get('question_id');

        ExamAttemptAnswer::updateOrCreate(
            ['attempt_id' => $attempt->id, 'question_id' => $questionId],
            [
                'selected_option_id' => $request->get('selected_option_id'),
                'answer_text' => $request->get('answer_text'),
            ]
        );

        return response()->json(['status' => 'success']);
    }

    // AJAX: student uploads a voice answer recording for a 'voice' question.
    public function saveVoiceAnswer(Request $request, $id)
    {
        try {
            $attemptId = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error']);
        }

        $attempt = ExamAttempt::where('student_id', Auth::guard('students')->id())->where('status', 'in_progress')->find($attemptId);
        if (!$attempt || !$request->hasFile('audio')) {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }

        $audio = $request->file('audio');
        $audioName = 'ans_' . $attempt->id . '_' . $request->get('question_id') . '_' . time() . '.' . $audio->getClientOriginalExtension();
        $audio->move('File/exam_answers/voice/', $audioName);

        ExamAttemptAnswer::updateOrCreate(
            ['attempt_id' => $attempt->id, 'question_id' => $request->get('question_id')],
            ['answer_audio_path' => 'File/exam_answers/voice/' . $audioName]
        );

        return response()->json(['status' => 'success']);
    }

    // AJAX: log an anti-cheat event (copy/paste/cut/right_click/tab_switch/window_blur/window_focus).
    public function logViolation(Request $request, $id)
    {
        try {
            $attemptId = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error']);
        }

        $attempt = ExamAttempt::with('exam')->where('student_id', Auth::guard('students')->id())->where('status', 'in_progress')->find($attemptId);
        if (!$attempt || !$attempt->exam) {
            return response()->json(['status' => 'error']);
        }

        ExamViolation::create([
            'attempt_id' => $attempt->id,
            'type' => $request->get('type'),
            'occurred_at' => now(),
            'meta' => $request->get('question_id') ? ['question_id' => $request->get('question_id')] : null,
        ]);

        $attempt->increment('violations_count');

        $exceeded = $attempt->exam->anti_cheat_enabled
            && $attempt->violations_count >= $attempt->exam->anti_cheat_violation_limit;

        // Violations are tracked and reported, but NEVER force-submit or otherwise interrupt the
        // student's ability to keep answering and to save/finish the exam normally — exceeding
        // the limit only triggers a notification, it must not risk cutting the attempt short or
        // interfering with saving answers/submission. (Deliberately not calling finalizeAttempt()
        // here even when the exam's configured action is 'auto_submit'.)
        if ($exceeded && in_array($attempt->exam->anti_cheat_action, ['notify_teacher', 'auto_submit'])) {
            ExamNotifier::notifyCheatingSuspected($attempt->fresh(['exam', 'student']));
        }

        return response()->json([
            'status' => 'success',
            'violations_count' => $attempt->violations_count,
            'limit' => $attempt->exam->anti_cheat_violation_limit,
            'exceeded' => $exceeded,
            'action' => $attempt->exam->anti_cheat_action,
        ]);
    }

    public function submit(Request $request, $id)
    {
        try {
            $attemptId = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            abort(404);
        }

        $attempt = ExamAttempt::where('student_id', Auth::guard('students')->id())->where('status', 'in_progress')->find($attemptId);
        if (!$attempt || !$attempt->exam) {
            abort(404, self::NOT_FOUND);
        }

        $this->finalizeAttempt($attempt, false);

        return redirect(route('student.exams.result', ['attempt' => Crypt::encrypt($attempt->id)]));
    }

    // Auto-grades mcq/true_false answers immediately; text/voice answers stay ungraded until a
    // teacher/admin reviews them (Phase 6). For Placement Tests, also computes the level recommendation.
    private function finalizeAttempt(ExamAttempt $attempt, bool $autoSubmitted): void
    {
        DB::transaction(function () use ($attempt, $autoSubmitted) {
            $answers = ExamAttemptAnswer::with('question')->where('attempt_id', $attempt->id)->get();
            $autoScore = 0;

            foreach ($answers as $answer) {
                $question = $answer->question;
                if (!$question || !in_array($question->type, ['mcq', 'true_false'])) {
                    continue;
                }

                $correctOptionIds = $question->options()->where('is_correct', true)->pluck('id')->toArray();
                $isCorrect = $answer->selected_option_id && in_array($answer->selected_option_id, $correctOptionIds);
                $marks = $isCorrect ? ($question->marks) : 0;

                $answer->update(['is_correct' => $isCorrect, 'marks_awarded' => $marks]);
                $autoScore += $marks;
            }

            $hasManualQuestions = $attempt->exam->questions()->whereIn('type', ['text', 'voice'])->exists();

            $attempt->update([
                'status' => $hasManualQuestions ? 'submitted' : 'graded',
                'submitted_at' => now(),
                'auto_score' => $autoScore,
                'final_score' => $hasManualQuestions ? null : $autoScore,
                'percentage' => (!$hasManualQuestions && $attempt->total_marks > 0) ? round($autoScore / $attempt->total_marks * 100, 2) : null,
                'is_auto_submitted' => $autoSubmitted,
            ]);

            if (!$hasManualQuestions && $attempt->exam->category === 'placement' && $attempt->percentage !== null) {
                $attempt->update(['recommended_level' => ExamLevelRange::recommendLevel($attempt->percentage)]);
            }
        });

        if ($attempt->fresh()->status === 'submitted') {
            ExamNotifier::notifyAttemptSubmitted($attempt->fresh(['exam', 'student']));
        }
    }

    public function result(Request $request, $id)
    {
        try {
            $attemptId = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            abort(404);
        }

        $attempt = ExamAttempt::with(['exam', 'answers.question.options'])->where('student_id', Auth::guard('students')->id())->find($attemptId);
        if (!$attempt) {
            abort(404, self::NOT_FOUND);
        }

        return view('frontend.students.exams.result', array_merge(parent::$data, ['attempt' => $attempt]));
    }

    public function requestReview(Request $request, $id)
    {
        try {
            $attemptId = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error']);
        }

        $attempt = ExamAttempt::where('student_id', Auth::guard('students')->id())->find($attemptId);
        if (!$attempt) {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }

        $review = ExamReviewRequest::create([
            'attempt_id' => $attempt->id,
            'student_id' => Auth::guard('students')->id(),
            'message' => $request->get('message'),
            'status' => 'pending',
        ]);

        ExamNotifier::notifyReviewRequested($review->fresh(['attempt.exam', 'student']));

        return response()->json(['status' => 'success', 'message' => 'تم إرسال طلب المراجعة بنجاح']);
    }
}
