<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\ExamAttempt;
use App\Models\ExamAttemptAnswer;
use App\Models\ExamReviewRequest;
use App\Models\ExamLevelRange;

class ExamReviewsController extends AdminController
{
    const UPDATE_SUCCESS = "نجاح، تم حفظ التصحيح بنجاح";
    const NOT_FOUND = "عذراً، لا يمكن العثور على البيانات";

    public function __construct()
    {
        parent::__construct();
        parent::$data['active_menu'] = 'exam_reviews';
    }

    // Attempts that contain at least one ungraded text/voice answer.
    public function getIndex()
    {
        parent::$data['pendingAttempts'] = ExamAttempt::with(['exam', 'student'])
            ->whereIn('status', ['submitted'])
            ->orderBy('submitted_at')
            ->get();

        parent::$data['reviewRequests'] = ExamReviewRequest::with(['attempt.exam', 'student'])
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->get();

        return view('admin.exam_reviews.view', parent::$data);
    }

    public function getGrade(Request $request, $id)
    {
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('exam_reviews.view'));
        }

        $attempt = ExamAttempt::with(['exam', 'student', 'answers.question.options'])->find($id);
        if (!$attempt) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('exam_reviews.view'));
        }

        parent::$data['attempt'] = $attempt;
        return view('admin.exam_reviews.grade', parent::$data);
    }

    public function postGrade(Request $request, $id)
    {
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return redirect(route('exam_reviews.view'))->with('danger', self::NOT_FOUND);
        }

        $attempt = ExamAttempt::with(['exam', 'answers.question'])->find($id);
        if (!$attempt) {
            return redirect(route('exam_reviews.view'))->with('danger', self::NOT_FOUND);
        }

        DB::transaction(function () use ($attempt, $request) {
            $marks = $request->get('marks', []);
            $comments = $request->get('comments', []);

            foreach ($attempt->answers as $answer) {
                if (!in_array($answer->question->type, ['text', 'voice'])) {
                    continue;
                }
                if (!array_key_exists($answer->id, $marks)) {
                    continue;
                }

                $awarded = min((float) $marks[$answer->id], (float) $answer->question->marks);
                $answer->update([
                    'marks_awarded' => $awarded,
                    'is_correct' => $awarded >= $answer->question->marks,
                    'teacher_comment' => $comments[$answer->id] ?? null,
                    'graded_by_id' => Auth::guard('admin')->id(),
                    'graded_at' => now(),
                ]);
            }

            $manualScore = $attempt->answers()->whereNotNull('marks_awarded')
                ->whereHas('question', fn($q) => $q->whereIn('type', ['text', 'voice']))
                ->sum('marks_awarded');

            $finalScore = $attempt->auto_score + $manualScore;
            $percentage = $attempt->total_marks > 0 ? round($finalScore / $attempt->total_marks * 100, 2) : null;

            $attempt->update([
                'manual_score' => $manualScore,
                'final_score' => $finalScore,
                'percentage' => $percentage,
                'status' => 'graded',
            ]);

            if ($attempt->exam->category === 'placement' && $percentage !== null) {
                $attempt->update(['recommended_level' => ExamLevelRange::recommendLevel($percentage)]);
            }
        });

        $request->session()->flash('success', self::UPDATE_SUCCESS);
        return redirect(route('exam_reviews.view'));
    }

    public function postApproveReview(Request $request)
    {
        try {
            $id = Crypt::decrypt($request->get('id'));
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error']);
        }

        $review = ExamReviewRequest::find($id);
        if (!$review) {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }

        $review->update([
            'status' => $request->get('decision') === 'approved' ? 'approved' : 'rejected',
            'teacher_comment' => $request->get('teacher_comment'),
            'reviewed_by_id' => Auth::guard('admin')->id(),
            'reviewed_at' => now(),
        ]);

        \App\Http\Controllers\ExamNotifier::notifyReviewDecision($review->fresh(['attempt.exam', 'student']));

        return response()->json(['status' => 'success', 'message' => self::UPDATE_SUCCESS]);
    }
}
