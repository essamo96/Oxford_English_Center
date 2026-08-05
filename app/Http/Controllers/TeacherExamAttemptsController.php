<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\ExamAttempt;
use App\Models\Groups;

class TeacherExamAttemptsController extends Controller
{
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
        $attempts = $this->ownedAttemptsQuery()->with(['exam.group', 'student'])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($attempt) {
                $attempt->duration_taken = ($attempt->started_at && $attempt->submitted_at)
                    ? $attempt->started_at->diffInMinutes($attempt->submitted_at) . ' دقيقة'
                    : '—';
                return $attempt;
            });

        return view('frontend.teachers.exams.attempts', array_merge(parent::$data, compact('attempts')));
    }

    // Hierarchical report: every group this teacher owns -> its exams -> each student's
    // attempt/score/violations for that exam. One consolidated view instead of a flat table.
    public function getGroupsReport()
    {
        $groups = Groups::where('teacher_id', Auth::guard('teachers')->id())
            ->where('status', 1)
            ->with(['exams' => function ($q) {
                $q->where('category', 'group')->with(['attempts.student'])->orderBy('title');
            }])
            ->orderBy('name')
            ->get();

        return view('frontend.teachers.exams.groups_report', array_merge(parent::$data, compact('groups')));
    }

    public function getAnswers(Request $request)
    {
        try {
            $id = Crypt::decrypt($request->get('id'));
        } catch (DecryptException $e) {
            return '<div class="alert alert-danger">' . self::NOT_FOUND . '</div>';
        }

        $attempt = $this->ownedAttemptsQuery()->with(['exam', 'student', 'answers.question.options'])->find($id);
        if (!$attempt) {
            return '<div class="alert alert-danger">' . self::NOT_FOUND . '</div>';
        }

        return view('admin.exam_attempts.parts.answers', ['attempt' => $attempt, 'onlyWrong' => false])->render();
    }

    public function getWrongAnswers(Request $request)
    {
        try {
            $id = Crypt::decrypt($request->get('id'));
        } catch (DecryptException $e) {
            return '<div class="alert alert-danger">' . self::NOT_FOUND . '</div>';
        }

        $attempt = $this->ownedAttemptsQuery()->with(['exam', 'student', 'answers.question.options'])->find($id);
        if (!$attempt) {
            return '<div class="alert alert-danger">' . self::NOT_FOUND . '</div>';
        }

        return view('admin.exam_attempts.parts.answers', ['attempt' => $attempt, 'onlyWrong' => true])->render();
    }
}
