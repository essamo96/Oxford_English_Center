<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\ExamAttempt;
use App\Models\ExamReviewRequest;
use App\Models\ExamLevelRange;
use App\Models\Groups;

class TeacherExamReviewsController extends Controller
{
    const UPDATE_SUCCESS = "نجاح، تم حفظ التصحيح بنجاح";
    const NOT_FOUND = "عذراً، لا يمكن العثور على البيانات";

    private function ownedGroupIds(): array
    {
        return Groups::where('teacher_id', Auth::guard('teachers')->id())->pluck('id')->toArray();
    }

    private function ownedAttemptsQuery()
    {
        return ExamAttempt::whereHas('exam', function ($q) {
            $q->where('category', 'group')->whereIn('group_id', $this->ownedGroupIds());
        });
    }

    public function getIndex()
    {
        $pendingAttempts = $this->ownedAttemptsQuery()->with(['exam', 'student'])
            ->where('status', 'submitted')->orderBy('submitted_at')->get();

        $reviewRequests = ExamReviewRequest::whereHas('attempt', function ($q) {
            $q->whereHas('exam', fn($qq) => $qq->where('category', 'group')->whereIn('group_id', $this->ownedGroupIds()));
        })->with(['attempt.exam', 'student'])->where('status', 'pending')->orderBy('created_at')->get();

        return view('frontend.teachers.exams.reviews', array_merge(parent::$data, compact('pendingAttempts', 'reviewRequests')));
    }

    public function getGrade(Request $request, $id)
    {
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return redirect(route('teacher.exam_reviews.view'))->with('danger', self::NOT_FOUND);
        }

        $attempt = $this->ownedAttemptsQuery()->with(['exam', 'student', 'answers.question.options'])->find($id);
        if (!$attempt) {
            return redirect(route('teacher.exam_reviews.view'))->with('danger', self::NOT_FOUND);
        }

        return view('frontend.teachers.exams.grade', array_merge(parent::$data, compact('attempt')));
    }

    public function postGrade(Request $request, $id)
    {
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return redirect(route('teacher.exam_reviews.view'))->with('danger', self::NOT_FOUND);
        }

        $attempt = $this->ownedAttemptsQuery()->with(['exam', 'answers.question'])->find($id);
        if (!$attempt) {
            return redirect(route('teacher.exam_reviews.view'))->with('danger', self::NOT_FOUND);
        }

        DB::transaction(function () use ($attempt, $request) {
            $marks = $request->get('marks', []);
            $comments = $request->get('comments', []);

            foreach ($attempt->answers as $answer) {
                if (!in_array($answer->question->type, ['text', 'voice']) || !array_key_exists($answer->id, $marks)) {
                    continue;
                }
                $awarded = min((float) $marks[$answer->id], (float) $answer->question->marks);
                $answer->update([
                    'marks_awarded' => $awarded,
                    'is_correct' => $awarded >= $answer->question->marks,
                    'teacher_comment' => $comments[$answer->id] ?? null,
                    'graded_by_id' => Auth::guard('teachers')->id(),
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
        });

        return redirect(route('teacher.exam_reviews.view'))->with('success', self::UPDATE_SUCCESS);
    }

    public function postApproveReview(Request $request)
    {
        try {
            $id = Crypt::decrypt($request->get('id'));
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error']);
        }

        $review = ExamReviewRequest::whereHas('attempt', function ($q) {
            $q->whereHas('exam', fn($qq) => $qq->where('category', 'group')->whereIn('group_id', $this->ownedGroupIds()));
        })->find($id);

        if (!$review) {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }

        $review->update([
            'status' => $request->get('decision') === 'approved' ? 'approved' : 'rejected',
            'teacher_comment' => $request->get('teacher_comment'),
            'reviewed_by_id' => Auth::guard('teachers')->id(),
            'reviewed_at' => now(),
        ]);

        ExamNotifier::notifyReviewDecision($review->fresh(['attempt.exam', 'student']));

        return response()->json(['status' => 'success', 'message' => self::UPDATE_SUCCESS]);
    }
}
