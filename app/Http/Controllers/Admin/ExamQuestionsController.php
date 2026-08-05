<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Encryption\DecryptException;
use Yajra\DataTables\DataTables;
use App\Models\ExamQuestion;
use App\Models\ExamQuestionOption;
use App\Models\ExamSkill;
use App\Models\Programs;

class ExamQuestionsController extends AdminController
{
    const INSERT_SUCCESS_MESSAGE = "نجاح، تم الإضافة بنجاح";
    const UPDATE_SUCCESS = "نجاح، تم التعديل بنجاح";
    const DELETE_SUCCESS = "نجاح، تم الحذف بنجاح";
    const EXECUTION_ERROR = "عذراً، حدث خطأ أثناء تنفيذ العملية";
    const NOT_FOUND = "عذراً، لا يمكن العثور على البيانات";
    const ACTIVATION_SUCCESS = "نجاح، تم التفعيل بنجاح";
    const DISABLE_SUCCESS = "نجاح، تم التعطيل بنجاح";

    public function __construct()
    {
        parent::__construct();
        parent::$data['active_menu'] = 'exam_questions';
    }

    public function getIndex()
    {
        parent::$data['skills'] = ExamSkill::where('status', 1)->orderBy('name_ar')->get();
        parent::$data['programs'] = Programs::orderBy('title')->get();
        return view('admin.exam_questions.view', parent::$data);
    }

    public function getList(Request $request)
    {
        $keyword = $request->get('title', null);
        $skillId = $request->get('skill_id', null);
        $type = $request->get('type', null);
        $difficulty = $request->get('difficulty', null);

        $info = ExamQuestion::with(['skill', 'program'])
            ->when($keyword, fn($q) => $q->where('question_text', 'LIKE', "%{$keyword}%"))
            ->when($skillId, fn($q) => $q->where('skill_id', $skillId))
            ->when($type, fn($q) => $q->where('type', $type))
            ->when($difficulty, fn($q) => $q->where('difficulty', $difficulty))
            ->orderBy('id', 'desc');

        $datatable = Datatables::of($info);

        $datatable->editColumn('question_text', function ($row) {
            return \Illuminate\Support\Str::limit(strip_tags($row->question_text), 80);
        });

        $datatable->editColumn('type', function ($row) {
            $labels = ['mcq' => 'اختيار من متعدد', 'true_false' => 'صح/خطأ', 'text' => 'إجابة نصية', 'voice' => 'إجابة صوتية'];
            return $labels[$row->type] ?? $row->type;
        });

        $datatable->editColumn('difficulty', function ($row) {
            $labels = ['easy' => 'سهل', 'medium' => 'متوسط', 'hard' => 'صعب', 'custom' => 'مخصص'];
            $classes = ['easy' => 'success', 'medium' => 'warning', 'hard' => 'danger', 'custom' => 'info'];
            return '<span class="badge badge-light-' . ($classes[$row->difficulty] ?? 'secondary') . '">' . ($labels[$row->difficulty] ?? $row->difficulty) . '</span>';
        });

        $datatable->editColumn('skill', function ($row) {
            return $row->skill?->name_ar ?? '—';
        });

        $datatable->editColumn('status', function ($row) {
            return view('admin.exam_questions.parts.status', ['id' => $row->id, 'status' => $row->status])->render();
        });

        $datatable->addColumn('actions', function ($row) {
            return view('admin.exam_questions.parts.actions', ['id' => $row->id])->render();
        });

        $datatable->rawColumns(['difficulty']);
        $datatable->escapeColumns(['question_text', 'type', 'skill']);
        return $datatable->make(true);
    }

    public function getAdd()
    {
        parent::$data['skills'] = ExamSkill::where('status', 1)->orderBy('name_ar')->get();
        parent::$data['programs'] = Programs::orderBy('title')->get();
        return view('admin.exam_questions.add', parent::$data);
    }

    public function postAdd(Request $request)
    {
        $validator = $this->validateQuestion($request);

        if ($validator->fails()) {
            $request->session()->flash('danger', $validator->messages());
            return redirect(route('exam_questions.add'))->withInput();
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'q_' . time() . '.' . $image->getClientOriginalExtension();
            $image->move('File/exam_questions/', $imageName);
            $imagePath = 'File/exam_questions/' . $imageName;
        }

        $audioPath = null;
        if ($request->hasFile('audio')) {
            $audio = $request->file('audio');
            $audioName = 'q_audio_' . time() . '.' . $audio->getClientOriginalExtension();
            $audio->move('File/exam_questions/audio/', $audioName);
            $audioPath = 'File/exam_questions/audio/' . $audioName;
        }

        $question = ExamQuestion::create([
            'branch_id' => auth()->guard('admin')->user()->branch_id ?? null,
            'program_id' => $request->get('program_id') ?: null,
            'skill_id' => $request->get('skill_id') ?: null,
            'type' => $request->get('type'),
            'difficulty' => $request->get('difficulty'),
            'question_text' => $request->get('question_text'),
            'image_path' => $imagePath,
            'audio_path' => $audioPath,
            'explanation' => $request->get('explanation'),
            'estimated_time_seconds' => $request->get('estimated_time_seconds', 60),
            'marks' => $request->get('marks', 1),
            'tags' => $request->get('tags') ? array_map('trim', explode(',', $request->get('tags'))) : null,
            'status' => $request->get('status', 'active'),
            'created_by_type' => 'admin',
            'created_by_id' => Auth::guard('admin')->id(),
        ]);

        $this->saveOptions($question, $request);

        $request->session()->flash('success', self::INSERT_SUCCESS_MESSAGE);
        return redirect(route('exam_questions.view'));
    }

    public function getEdit(Request $request, $id)
    {
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('exam_questions.view'));
        }

        $info = ExamQuestion::with('options')->find($id);
        if (!$info) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('exam_questions.view'));
        }

        parent::$data['info'] = $info;
        parent::$data['skills'] = ExamSkill::where('status', 1)->orderBy('name_ar')->get();
        parent::$data['programs'] = Programs::orderBy('title')->get();
        return view('admin.exam_questions.edit', parent::$data);
    }

    public function postEdit(Request $request, $id)
    {
        $encrypted_id = $id;
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('exam_questions.view'));
        }

        $question = ExamQuestion::find($id);
        if (!$question) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('exam_questions.view'));
        }

        $validator = $this->validateQuestion($request);
        if ($validator->fails()) {
            $request->session()->flash('danger', $validator->messages());
            return redirect(route('exam_questions.edit', ['id' => $encrypted_id]))->withInput();
        }

        $imagePath = $question->image_path;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'q_' . time() . '.' . $image->getClientOriginalExtension();
            $image->move('File/exam_questions/', $imageName);
            @unlink($question->image_path);
            $imagePath = 'File/exam_questions/' . $imageName;
        }

        $audioPath = $question->audio_path;
        if ($request->hasFile('audio')) {
            $audio = $request->file('audio');
            $audioName = 'q_audio_' . time() . '.' . $audio->getClientOriginalExtension();
            $audio->move('File/exam_questions/audio/', $audioName);
            @unlink($question->audio_path);
            $audioPath = 'File/exam_questions/audio/' . $audioName;
        }

        $question->update([
            'program_id' => $request->get('program_id') ?: null,
            'skill_id' => $request->get('skill_id') ?: null,
            'type' => $request->get('type'),
            'difficulty' => $request->get('difficulty'),
            'question_text' => $request->get('question_text'),
            'image_path' => $imagePath,
            'audio_path' => $audioPath,
            'explanation' => $request->get('explanation'),
            'estimated_time_seconds' => $request->get('estimated_time_seconds', 60),
            'marks' => $request->get('marks', 1),
            'tags' => $request->get('tags') ? array_map('trim', explode(',', $request->get('tags'))) : null,
            'status' => $request->get('status', 'active'),
        ]);

        $question->options()->delete();
        $this->saveOptions($question, $request);

        $request->session()->flash('success', self::UPDATE_SUCCESS);
        return redirect(route('exam_questions.view'));
    }

    public function postDelete(Request $request)
    {
        try {
            $id = Crypt::decrypt($request->get('id'));
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }

        $info = ExamQuestion::find($id);
        if (!$info) {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }

        $info->delete();
        return response()->json(['status' => 'success', 'message' => self::DELETE_SUCCESS]);
    }

    public function postStatus(Request $request)
    {
        try {
            $id = Crypt::decrypt($request->get('id'));
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }

        $info = ExamQuestion::find($id);
        if (!$info) {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }

        $newStatus = $info->status == 'active' ? 'archived' : 'active';
        $info->update(['status' => $newStatus]);

        if ($newStatus == 'active') {
            return response()->json(['status' => 'success', 'message' => self::ACTIVATION_SUCCESS, 'type' => 'yes']);
        }
        return response()->json(['status' => 'success', 'message' => self::DISABLE_SUCCESS, 'type' => 'no']);
    }

    private function validateQuestion(Request $request)
    {
        return Validator::make($request->all(), [
            'type' => 'required|in:mcq,true_false,text,voice',
            'difficulty' => 'required|in:easy,medium,hard,custom',
            'question_text' => 'required|string',
            'marks' => 'required|numeric|min:0.5',
            'estimated_time_seconds' => 'required|integer|min:5',
            'status' => 'required|in:draft,active,archived',
            'image' => 'nullable|image|max:4096',
            'audio' => 'nullable|mimes:mp3,wav,ogg,m4a|max:10240',
            'options' => 'required_if:type,mcq,true_false|array|min:2',
            'options.*' => 'nullable|string',
            'correct_options' => 'required_if:type,mcq,true_false|array|min:1',
        ]);
    }

    private function saveOptions(ExamQuestion $question, Request $request)
    {
        if (!in_array($question->type, ['mcq', 'true_false'])) {
            return;
        }

        $options = $request->get('options', []);
        $correct = $request->get('correct_options', []);

        foreach ($options as $index => $text) {
            if (trim((string) $text) === '') {
                continue;
            }
            ExamQuestionOption::create([
                'question_id' => $question->id,
                'option_text' => $text,
                'is_correct' => in_array((string) $index, array_map('strval', $correct)),
                'sort_order' => $index,
            ]);
        }
    }
}
