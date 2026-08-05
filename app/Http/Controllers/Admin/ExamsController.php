<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Encryption\DecryptException;
use Yajra\DataTables\DataTables;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\Programs;
use App\Models\Groups;
use App\Models\Teachers;

class ExamsController extends AdminController
{
    const INSERT_SUCCESS_MESSAGE = "نجاح، تم الإضافة بنجاح";
    const UPDATE_SUCCESS = "نجاح، تم التعديل بنجاح";
    const DELETE_SUCCESS = "نجاح، تم الحذف بنجاح";
    const EXECUTION_ERROR = "عذراً، حدث خطأ أثناء تنفيذ العملية";
    const NOT_FOUND = "عذراً، لا يمكن العثور على البيانات";

    // 'placement' or 'group', resolved per-request from the route
    private function category(Request $request): string
    {
        return str_starts_with($request->route()->getName(), 'exam_placement_tests.') ? 'placement' : 'group';
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function getIndex(Request $request)
    {
        $category = $this->category($request);
        parent::$data['active_menu'] = $category === 'placement' ? 'exam_placement_tests' : 'group_exams';
        parent::$data['category'] = $category;
        parent::$data['programs'] = Programs::orderBy('title')->get();
        return view('admin.exams.view', parent::$data);
    }

    public function getList(Request $request)
    {
        $category = $this->category($request);
        $keyword = $request->get('title', null);
        $status = $request->get('status', null);
        $programId = $request->get('program_id', null);

        $info = Exam::with(['program', 'group', 'teacher'])
            ->where('category', $category)
            ->when($keyword, fn($q) => $q->where('title', 'LIKE', "%{$keyword}%"))
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($programId, fn($q) => $q->where('program_id', $programId))
            ->orderBy('id', 'desc');

        $datatable = Datatables::of($info);

        $datatable->editColumn('group', fn($row) => $row->group?->title ?? '—');
        $datatable->editColumn('program', fn($row) => $row->program?->title ?? '—');

        $datatable->editColumn('status', function ($row) {
            return view('admin.exams.parts.status', ['id' => $row->id, 'status' => $row->status, 'category' => $row->category])->render();
        });

        $datatable->addColumn('actions', function ($row) {
            return view('admin.exams.parts.actions', ['id' => $row->id, 'category' => $row->category])->render();
        });

        $datatable->escapeColumns(['*']);
        return $datatable->make(true);
    }

    public function getAdd(Request $request)
    {
        $category = $this->category($request);
        parent::$data['active_menu'] = $category === 'placement' ? 'exam_placement_tests' : 'group_exams';
        parent::$data['category'] = $category;
        parent::$data['programs'] = Programs::orderBy('title')->get();
        parent::$data['groups'] = $category === 'group' ? Groups::where('status', 1)->orderBy('title')->get() : collect();
        parent::$data['questions'] = ExamQuestion::where('status', 'active')->with('skill')->orderBy('id', 'desc')->limit(300)->get();
        return view('admin.exams.add', parent::$data);
    }

    public function postAdd(Request $request)
    {
        $category = $request->get('category', 'group');
        $validator = $this->validateExam($request, $category);

        if ($validator->fails()) {
            $request->session()->flash('danger', $validator->messages());
            return redirect()->back()->withInput();
        }

        DB::beginTransaction();
        try {
            $exam = Exam::create([
                'branch_id' => auth()->guard('admin')->user()->branch_id ?? null,
                'category' => $category,
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'program_id' => $request->get('program_id') ?: null,
                'group_id' => $category === 'group' ? $request->get('group_id') : null,
                'teacher_id' => $category === 'group' ? ($request->get('teacher_id') ?: null) : null,
                'duration_minutes' => $request->get('duration_minutes', 30),
                'max_attempts' => $request->get('max_attempts', 1),
                'passing_score' => $request->get('passing_score', 50),
                'start_date' => $request->get('start_date'),
                'end_date' => $request->get('end_date'),
                'status' => $request->get('status', 'draft'),
                'shuffle_questions' => $request->boolean('shuffle_questions'),
                'shuffle_answers' => $request->boolean('shuffle_answers'),
                'review_available' => $request->boolean('review_available'),
                'result_visibility' => $request->get('result_visibility', 'immediate'),
                'anti_cheat_enabled' => $request->boolean('anti_cheat_enabled'),
                'anti_cheat_violation_limit' => $request->get('anti_cheat_violation_limit', 3),
                'anti_cheat_action' => $request->get('anti_cheat_action', 'warning'),
                'created_by_type' => 'admin',
                'created_by_id' => Auth::guard('admin')->id(),
            ]);

            $this->attachQuestions($exam, $request);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $request->session()->flash('danger', self::EXECUTION_ERROR . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }

        $request->session()->flash('success', self::INSERT_SUCCESS_MESSAGE);
        return redirect($category === 'placement' ? route('exam_placement_tests.view') : route('group_exams.view'));
    }

    public function getEdit(Request $request, $id)
    {
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect()->back();
        }

        $exam = Exam::with('questions')->find($id);
        if (!$exam) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect()->back();
        }

        parent::$data['active_menu'] = $exam->category === 'placement' ? 'exam_placement_tests' : 'group_exams';
        parent::$data['category'] = $exam->category;
        parent::$data['info'] = $exam;
        parent::$data['programs'] = Programs::orderBy('title')->get();
        parent::$data['groups'] = $exam->category === 'group' ? Groups::where('status', 1)->orderBy('title')->get() : collect();
        parent::$data['questions'] = ExamQuestion::where('status', 'active')->with('skill')->orderBy('id', 'desc')->limit(300)->get();
        return view('admin.exams.edit', parent::$data);
    }

    public function postEdit(Request $request, $id)
    {
        $encrypted_id = $id;
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect()->back();
        }

        $exam = Exam::find($id);
        if (!$exam) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect()->back();
        }

        $validator = $this->validateExam($request, $exam->category);
        if ($validator->fails()) {
            $request->session()->flash('danger', $validator->messages());
            return redirect()->back()->withInput();
        }

        DB::beginTransaction();
        try {
            $exam->update([
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'program_id' => $request->get('program_id') ?: null,
                'group_id' => $exam->category === 'group' ? $request->get('group_id') : null,
                'teacher_id' => $exam->category === 'group' ? ($request->get('teacher_id') ?: null) : null,
                'duration_minutes' => $request->get('duration_minutes', 30),
                'max_attempts' => $request->get('max_attempts', 1),
                'passing_score' => $request->get('passing_score', 50),
                'start_date' => $request->get('start_date'),
                'end_date' => $request->get('end_date'),
                'status' => $request->get('status', $exam->status),
                'shuffle_questions' => $request->boolean('shuffle_questions'),
                'shuffle_answers' => $request->boolean('shuffle_answers'),
                'review_available' => $request->boolean('review_available'),
                'result_visibility' => $request->get('result_visibility', 'immediate'),
                'anti_cheat_enabled' => $request->boolean('anti_cheat_enabled'),
                'anti_cheat_violation_limit' => $request->get('anti_cheat_violation_limit', 3),
                'anti_cheat_action' => $request->get('anti_cheat_action', 'warning'),
            ]);

            $exam->questions()->detach();
            $this->attachQuestions($exam, $request);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $request->session()->flash('danger', self::EXECUTION_ERROR . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }

        $request->session()->flash('success', self::UPDATE_SUCCESS);
        return redirect($exam->category === 'placement' ? route('exam_placement_tests.view') : route('group_exams.view'));
    }

    public function postDelete(Request $request)
    {
        try {
            $id = Crypt::decrypt($request->get('id'));
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }

        $exam = Exam::find($id);
        if (!$exam) {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }

        $exam->delete();
        return response()->json(['status' => 'success', 'message' => self::DELETE_SUCCESS]);
    }

    // cycles Draft -> Scheduled -> Published -> Closed on each click, used by the status badge in the list
    public function postStatus(Request $request)
    {
        try {
            $id = Crypt::decrypt($request->get('id'));
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }

        $exam = Exam::find($id);
        if (!$exam) {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }

        $next = match ($exam->status) {
            'draft' => 'scheduled',
            'scheduled' => 'published',
            'published' => 'closed',
            default => 'draft',
        };

        $exam->update(['status' => $next]);

        if ($next === 'published' && $exam->category === 'group') {
            \App\Http\Controllers\ExamNotifier::notifyGroupExamPublished($exam);
        }

        return response()->json(['status' => 'success', 'message' => self::UPDATE_SUCCESS, 'new_status' => $next]);
    }

    private function validateExam(Request $request, string $category)
    {
        $rules = [
            'title' => 'required|string|max:191',
            'duration_minutes' => 'required|integer|min:1',
            'max_attempts' => 'required|integer|min:1',
            'passing_score' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:draft,scheduled,published,closed',
            'result_visibility' => 'required|in:immediate,after_review,manual',
            'anti_cheat_action' => 'required|in:warning,notify_teacher,auto_submit,log',
        ];

        if ($category === 'group') {
            $rules['group_id'] = 'required|exists:groups,id';
        }

        return Validator::make($request->all(), $rules);
    }

    private function attachQuestions(Exam $exam, Request $request): void
    {
        $mode = $request->get('generation_mode', 'manual');

        if ($mode === 'auto') {
            $rules = [
                'easy' => (int) $request->get('gen_easy', 0),
                'medium' => (int) $request->get('gen_medium', 0),
                'hard' => (int) $request->get('gen_hard', 0),
            ];
            $skillId = $request->get('gen_skill_id') ?: null;
            $type = $request->get('gen_type') ?: null;

            $order = 0;
            foreach ($rules as $difficulty => $count) {
                if ($count <= 0) {
                    continue;
                }
                $picked = ExamQuestion::where('status', 'active')
                    ->where('difficulty', $difficulty)
                    ->when($skillId, fn($q) => $q->where('skill_id', $skillId))
                    ->when($type, fn($q) => $q->where('type', $type))
                    ->inRandomOrder()
                    ->limit($count)
                    ->pluck('id');

                foreach ($picked as $questionId) {
                    $exam->questions()->attach($questionId, ['sort_order' => $order++]);
                }
            }

            $exam->update(['generation_rules' => array_merge($rules, ['skill_id' => $skillId, 'type' => $type])]);
        } else {
            $questionIds = $request->get('question_ids', []);
            foreach ($questionIds as $order => $questionId) {
                $exam->questions()->attach($questionId, ['sort_order' => $order]);
            }
        }
    }
}
