<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Yajra\DataTables\DataTables;
use App\Models\ExamAttempt;
use App\Models\Exam;
use App\Models\Groups;

class ExamAttemptsController extends AdminController
{
    const NOT_FOUND = "عذراً، لا يمكن العثور على البيانات";

    public function __construct()
    {
        parent::__construct();
        parent::$data['active_menu'] = 'exam_attempts';
    }

    public function getIndex()
    {
        parent::$data['exams'] = Exam::orderBy('title')->get(['id', 'title']);
        parent::$data['groups'] = Groups::where('status', 1)->orderBy('name')->get(['id', 'name']);
        return view('admin.exam_attempts.view', parent::$data);
    }

    public function getList(Request $request)
    {
        $examId = $request->get('exam_id');
        $groupId = $request->get('group_id');
        $status = $request->get('status');
        $studentName = $request->get('title');

        $info = ExamAttempt::with(['exam.group', 'student'])
            ->when($examId, fn($q) => $q->where('exam_id', $examId))
            ->when($groupId, fn($q) => $q->whereHas('exam', fn($e) => $e->where('group_id', $groupId)))
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($studentName, fn($q) => $q->whereHas('student', fn($s) => $s->where('name', 'LIKE', "%{$studentName}%")))
            ->orderBy('id', 'desc');

        $datatable = Datatables::of($info);

        $datatable->editColumn('student', fn($row) => $row->student->name ?? '—');
        $datatable->editColumn('exam', fn($row) => $row->exam->title ?? '—');
        $datatable->editColumn('group', fn($row) => $row->exam->category === 'placement' ? 'تحديد مستوى' : ($row->exam->group->name ?? '—'));

        $datatable->editColumn('submitted_at', fn($row) => $row->submitted_at?->format('Y-m-d H:i') ?? '—');

        $datatable->editColumn('duration_taken', function ($row) {
            if (!$row->started_at || !$row->submitted_at) {
                return '—';
            }
            $minutes = $row->started_at->diffInMinutes($row->submitted_at);
            return $minutes . ' دقيقة';
        });

        $datatable->editColumn('score', function ($row) {
            if ($row->percentage === null) {
                return '<span class="text-muted">—</span>';
            }
            $passed = $row->exam && $row->percentage >= $row->exam->passing_score;
            $class = $passed ? 'success' : 'danger';
            return '<span class="badge badge-light-' . $class . '">' . $row->percentage . '% (' . ($row->final_score ?? $row->auto_score) . '/' . $row->total_marks . ')</span>';
        });

        $datatable->editColumn('status', function ($row) {
            $labels = ['in_progress' => 'جارٍ', 'submitted' => 'بانتظار التصحيح', 'graded' => 'تم التصحيح', 'expired' => 'منتهي الوقت'];
            $classes = ['in_progress' => 'warning', 'submitted' => 'info', 'graded' => 'success', 'expired' => 'secondary'];
            return '<span class="badge badge-light-' . ($classes[$row->status] ?? 'secondary') . '">' . ($labels[$row->status] ?? $row->status) . '</span>';
        });

        $datatable->editColumn('violations_count', function ($row) {
            if ($row->violations_count <= 0) {
                return '<span class="badge badge-light-success">0</span>';
            }
            return '<span class="badge badge-light-danger"><i class="bi bi-shield-exclamation"></i> ' . $row->violations_count . '</span>';
        });

        $datatable->addColumn('actions', function ($row) {
            return view('admin.exam_attempts.parts.actions', ['id' => $row->id])->render();
        });

        $datatable->rawColumns(['score', 'status', 'violations_count']);
        $datatable->escapeColumns(['student', 'exam', 'group']);
        return $datatable->make(true);
    }

    // Full answer review (all questions, correct answers marked) for one attempt.
    public function getAnswers(Request $request)
    {
        try {
            $id = Crypt::decrypt($request->get('id'));
        } catch (DecryptException $e) {
            return '<div class="alert alert-danger">' . self::NOT_FOUND . '</div>';
        }

        $attempt = ExamAttempt::with(['exam', 'student', 'answers.question.options'])->find($id);
        if (!$attempt) {
            return '<div class="alert alert-danger">' . self::NOT_FOUND . '</div>';
        }

        return view('admin.exam_attempts.parts.answers', ['attempt' => $attempt, 'onlyWrong' => false])->render();
    }

    // Same as above, filtered to only the questions the student got wrong.
    public function getWrongAnswers(Request $request)
    {
        try {
            $id = Crypt::decrypt($request->get('id'));
        } catch (DecryptException $e) {
            return '<div class="alert alert-danger">' . self::NOT_FOUND . '</div>';
        }

        $attempt = ExamAttempt::with(['exam', 'student', 'answers.question.options'])->find($id);
        if (!$attempt) {
            return '<div class="alert alert-danger">' . self::NOT_FOUND . '</div>';
        }

        return view('admin.exam_attempts.parts.answers', ['attempt' => $attempt, 'onlyWrong' => true])->render();
    }
}
