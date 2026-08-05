<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\Groups;

class TeacherExamsController extends Controller
{
    const INSERT_SUCCESS_MESSAGE = "نجاح، تم الإضافة بنجاح";
    const UPDATE_SUCCESS = "نجاح، تم التعديل بنجاح";
    const DELETE_SUCCESS = "نجاح، تم الحذف بنجاح";
    const EXECUTION_ERROR = "عذراً، حدث خطأ أثناء تنفيذ العملية";
    const NOT_FOUND = "عذراً، لا يمكن العثور على البيانات";

    // A teacher may only ever touch Group Exams for groups they teach — never Placement Tests.
    private function ownedGroupIds(): array
    {
        return Groups::where('teacher_id', Auth::guard('teachers')->id())
            ->whereNull('deleted_at')
            ->pluck('id')
            ->toArray();
    }

    public function getIndex()
    {
        $exams = Exam::with('group')
            ->where('category', 'group')
            ->whereIn('group_id', $this->ownedGroupIds())
            ->orderBy('id', 'desc')
            ->get();

        return view('frontend.teachers.exams.view', array_merge(parent::$data, ['exams' => $exams]));
    }

    public function getAdd()
    {
        $groups = Groups::where('teacher_id', Auth::guard('teachers')->id())->where('status', 1)->orderBy('name')->get();
        // teachers see their own questions + the global admin bank, never other teachers' questions
        $questions = ExamQuestion::visibleToTeacher(Auth::guard('teachers')->id())
            ->where('status', 'active')
            ->with('skill')
            ->orderBy('id', 'desc')
            ->limit(300)
            ->get();

        return view('frontend.teachers.exams.add', array_merge(parent::$data, ['groups' => $groups, 'questions' => $questions]));
    }

    public function postAdd(Request $request)
    {
        $ownedGroupIds = $this->ownedGroupIds();

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:191',
            'group_id' => ['required', 'in:' . implode(',', $ownedGroupIds ?: [0])],
            'duration_minutes' => 'required|integer|min:1',
            'max_attempts' => 'required|integer|min:1',
            'passing_score' => 'required|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            $request->session()->flash('danger', $validator->messages());
            return redirect()->back()->withInput();
        }

        DB::beginTransaction();
        try {
            $exam = Exam::create([
                'branch_id' => null,
                'category' => 'group',
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'group_id' => $request->get('group_id'),
                'teacher_id' => Auth::guard('teachers')->id(),
                'duration_minutes' => $request->get('duration_minutes', 30),
                'max_attempts' => $request->get('max_attempts', 1),
                'passing_score' => $request->get('passing_score', 50),
                'start_date' => $request->get('start_date'),
                'end_date' => $request->get('end_date'),
                'status' => 'draft', // teachers submit as draft; admin/teacher must explicitly publish
                'shuffle_questions' => $request->boolean('shuffle_questions', true),
                'shuffle_answers' => $request->boolean('shuffle_answers', true),
                'review_available' => $request->boolean('review_available', true),
                'result_visibility' => $request->get('result_visibility', 'immediate'),
                'anti_cheat_enabled' => $request->boolean('anti_cheat_enabled', true),
                'anti_cheat_violation_limit' => $request->get('anti_cheat_violation_limit', 3),
                'anti_cheat_action' => $request->get('anti_cheat_action', 'warning'),
                'created_by_type' => 'teacher',
                'created_by_id' => Auth::guard('teachers')->id(),
            ]);

            $questionIds = array_intersect(
                $request->get('question_ids', []),
                ExamQuestion::visibleToTeacher(Auth::guard('teachers')->id())->pluck('id')->toArray()
            );
            foreach (array_values($questionIds) as $order => $questionId) {
                $exam->questions()->attach($questionId, ['sort_order' => $order]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $request->session()->flash('danger', self::EXECUTION_ERROR);
            return redirect()->back()->withInput();
        }

        $request->session()->flash('success', self::INSERT_SUCCESS_MESSAGE);
        return redirect(route('teacher.exams.view'));
    }

    public function getEdit(Request $request, $id)
    {
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('teacher.exams.view'));
        }

        $exam = Exam::with('questions')->where('category', 'group')->whereIn('group_id', $this->ownedGroupIds())->find($id);
        if (!$exam) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('teacher.exams.view'));
        }

        $groups = Groups::where('teacher_id', Auth::guard('teachers')->id())->where('status', 1)->orderBy('name')->get();
        $questions = ExamQuestion::visibleToTeacher(Auth::guard('teachers')->id())->where('status', 'active')->with('skill')->orderBy('id', 'desc')->limit(300)->get();

        return view('frontend.teachers.exams.edit', array_merge(parent::$data, ['info' => $exam, 'groups' => $groups, 'questions' => $questions]));
    }

    public function postEdit(Request $request, $id)
    {
        $encrypted_id = $id;
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('teacher.exams.view'));
        }

        $exam = Exam::where('category', 'group')->whereIn('group_id', $this->ownedGroupIds())->find($id);
        if (!$exam) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('teacher.exams.view'));
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:191',
            'duration_minutes' => 'required|integer|min:1',
            'max_attempts' => 'required|integer|min:1',
            'passing_score' => 'required|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            $request->session()->flash('danger', $validator->messages());
            return redirect()->back()->withInput();
        }

        DB::beginTransaction();
        try {
            $exam->update([
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'duration_minutes' => $request->get('duration_minutes', 30),
                'max_attempts' => $request->get('max_attempts', 1),
                'passing_score' => $request->get('passing_score', 50),
                'start_date' => $request->get('start_date'),
                'end_date' => $request->get('end_date'),
                'shuffle_questions' => $request->boolean('shuffle_questions', true),
                'shuffle_answers' => $request->boolean('shuffle_answers', true),
                'review_available' => $request->boolean('review_available', true),
                'result_visibility' => $request->get('result_visibility', 'immediate'),
                'anti_cheat_enabled' => $request->boolean('anti_cheat_enabled', true),
                'anti_cheat_violation_limit' => $request->get('anti_cheat_violation_limit', 3),
                'anti_cheat_action' => $request->get('anti_cheat_action', 'warning'),
            ]);

            $exam->questions()->detach();
            $questionIds = array_intersect(
                $request->get('question_ids', []),
                ExamQuestion::visibleToTeacher(Auth::guard('teachers')->id())->pluck('id')->toArray()
            );
            foreach (array_values($questionIds) as $order => $questionId) {
                $exam->questions()->attach($questionId, ['sort_order' => $order]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $request->session()->flash('danger', self::EXECUTION_ERROR);
            return redirect()->back()->withInput();
        }

        $request->session()->flash('success', self::UPDATE_SUCCESS);
        return redirect(route('teacher.exams.view'));
    }

    public function postDelete(Request $request)
    {
        try {
            $id = Crypt::decrypt($request->get('id'));
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }

        $exam = Exam::where('category', 'group')->whereIn('group_id', $this->ownedGroupIds())->find($id);
        if (!$exam) {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }

        $exam->delete();
        return response()->json(['status' => 'success', 'message' => self::DELETE_SUCCESS]);
    }

    // teacher publishing toggle: draft <-> published only (scheduling/closing stays with the admin flow)
    public function postPublish(Request $request)
    {
        try {
            $id = Crypt::decrypt($request->get('id'));
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }

        $exam = Exam::where('category', 'group')->whereIn('group_id', $this->ownedGroupIds())->find($id);
        if (!$exam) {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }

        $newStatus = $exam->status === 'published' ? 'draft' : 'published';
        $exam->update(['status' => $newStatus]);

        if ($newStatus === 'published') {
            ExamNotifier::notifyGroupExamPublished($exam);
        }

        return response()->json(['status' => 'success', 'message' => self::UPDATE_SUCCESS, 'new_status' => $exam->status]);
    }
}
