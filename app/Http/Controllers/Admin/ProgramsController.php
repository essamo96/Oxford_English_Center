<?php

namespace App\Http\Controllers\Admin;

use URL;
use App\Models\Groups;
use App\Models\Programs;
use Illuminate\Http\Request;
use App\Models\GroupStudents;
use App\Models\Students;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
////////////////////////////////////
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Encryption\DecryptException;

class ProgramsController extends AdminController
{

    const INSERT_SUCCESS_MESSAGE = "نجاح، تم الإضافة بتجاح";
    const UPDATE_SUCCESS = "نجاح، تم التعديل بنجاح";
    const DELETE_SUCCESS = "نجاح، تم الحذف بنجاح";
    const PASSWORD_SUCCESS = "نجاح، تم تغيير كلمة المرور بنجاح";
    const EXECUTION_ERROR = "عذراً، حدث خطأ أثناء تنفيذ العملية";
    const NOT_FOUND = "عذراً،لا يمكن العثور على البيانات";
    const ACTIVATION_SUCCESS = "نجاح، تم التفعيل بنجاح";
    const DISABLE_SUCCESS = "نجاح، تم التعطيل بنجاح";

    //////////////////////////////////////////////
    public function __construct()
    {
        parent::__construct();
        parent::$data['active_menu'] = 'programs';
    }

    //////////////////////////////////////////////
    public function getIndex()
    {
        return view('admin.programs.view', parent::$data);
    }

    //////////////////////////////////////////////
    public function getList(Request $request)
    {
        $title = $request->get('title', NULL);
        $status = $request->get('status', NULL);
        $group_name = $request->get('group_name', NULL);

        $programs = new Programs();
        $info = $programs->getSearchPrograms($title, $status, $group_name);
        $datatable = Datatables::of($info);

        $datatable->editColumn('title', function ($row) {
            $title = !empty($row->title) ? $row->title : 'N/A';
            $imageHtml = '';
            if (!empty($row->image)) {
                $imageUrl = asset($row->image);
                $imageHtml = '<div class="symbol symbol-50px me-3">
                                <img src="' . $imageUrl . '" alt="' . $title . '" onerror="this.style.display=\'none\'">
                            </div>';
            } else {
                $imageHtml = '<div class="symbol symbol-50px me-3">
                                <span class="symbol-label bg-light-primary">
                                    <i class="ki-duotone ki-book-open fs-2x text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                </span>
                            </div>';
            }

            return '<div class="d-flex align-items-center view-program-details" data-id="' . Crypt::encrypt($row->id) . '" style="cursor: pointer;">
                        ' . $imageHtml . '
                        <div class="d-flex justify-content-start flex-column">
                            <span class="text-dark fw-bold text-hover-primary mb-1 fs-6">' . $title . '</span>
                        </div>
                    </div>';
        });

        $datatable->editColumn('status', function ($row) {
            $data['id'] = $row->id;
            $data['status'] = $row->status;

            return view('admin.programs.parts.status', $data)->render();
        });
        $datatable->editColumn('grope_no', function ($row) {
            $group = new Groups();
            $group_count = $group->countGroup($row->id);
            if ($group_count > 0) {
                return '<a href="' . URL("admin/program/groups/" . Crypt::encrypt($row->id)) . '" class="btn btn-sm btn-light-primary fw-bold" title="عرض المجموعات">
                    <i class="ki-duotone ki-element-11 fs-2 me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                    <span>' . $group_count . ' مجموعة</span>
                </a>';
            } else {
                return '<span class="badge badge-light-danger fs-5 fw-bold me-1 p-3">لا يوجد مجموعات</span>';
            }
        });

        $datatable->addColumn('students_count', function ($row) {
            $count = $row->students_count ?? 0;
            if ($count > 0) {
                return '<span class="badge badge-light-success fs-5 fw-bold p-3">
                            <i class="bi bi-people-fill text-success me-1 fs-5 "></i> ' . $count . ' -  طالب
                        </span>';
            }
            return '<span class="badge badge-light-dark fs-5 fw-bold fs-5 me-1 p-3">0 - طلاب</span>';
        });

        $datatable->addColumn('min_payment', function ($row) {
            $pct   = $row->min_payment_percent;
            $fixed = $row->min_payment_fixed;

            if (is_null($pct) && is_null($fixed)) {
                return '<span class="text-muted fst-italic fs-7">— غير محدد —</span>';
            }

            $html = '<div class="d-flex flex-column gap-1 align-items-center">';
            if (!is_null($pct)) {
                $pctStr = rtrim(rtrim(number_format($pct, 2), '0'), '.');
                $html .= '<span class="badge badge-light-info fw-bold fs-7"><i class="bi bi-percent me-1"></i> ' . $pctStr . '% نسبة</span>';
            }
            if (!is_null($fixed)) {
                $html .= '<span class="badge badge-light-warning fw-bold fs-7"><i class="bi bi-cash-coin me-1"></i> ' . number_format($fixed, 2) . ' ILS ثابت</span>';
            }
            $html .= '<small class="text-muted fs-9 mt-1">يُطبَّق الأعلى منهما</small>';
            $html .= '</div>';
            return $html;
        });

        $datatable->addColumn('actions', function ($row) {
            $data['id'] = $row->id;
            $data['btn_class'] = parent::$data['btn_class'];

            return view('admin.programs.parts.actions', $data)->render();
        });
        $datatable->escapeColumns(['*']);
        return $datatable->make(true);
    }
    public function getProgramGrope(Request $request, $id)
    {

        parent::$data['programs_id'] = $id;
        return view('admin.groups.program_grops_view', parent::$data);
    }
    /////////////////////////////////////////
    public function listProgramGrope(Request $request)
    {
        $program_id = $request->get('programs_id', NULL);
        $activeG = $request->get('activeG', NULL);
        try {
            $program_id = Crypt::decrypt($program_id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('news.view'));
        }
        $name = $request->get('name', NULL);
        $groups = new Groups();
        $info = $groups->getGropeByIdSearch($name, $program_id, $activeG);

        $datatable = Datatables::of($info);

        $datatable->editColumn('name', function ($row) {
            $icon = '';
            if ($row->image) {
                $icon = '<img src="' . url($row->image) . '" style="object-fit:cover;"/>';
            } else {
                $icon = '<div class="symbol-label fs-3 bg-light-info text-info">
                            <i class="ki-duotone ki-people fs-3 text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                         </div>';
            }

            return '<div class="d-flex align-items-center">
                        <div class="symbol symbol-40px symbol-circle me-3">
                            ' . $icon . '
                        </div>
                        <div class="d-flex flex-column text-start">
                            <a href="javascript:;" onclick="showGroupModal(' . $row->id . ')" class="text-gray-900 text-hover-primary fw-bold fs-6 lh-1 mb-1">' . $row->name . '</a>
                            <span class="text-muted fw-semibold fs-8">' . ($row->ctime ? $row->ctime->days : 'لم يحدد موعد') . '</span>
                        </div>
                    </div>';
        });
        $datatable->editColumn('teacher_name', function ($row) {
            return ($row->teacher ? $row->teacher->name : 'N/A');
        });
        $datatable->editColumn('program_name', function ($row) {
            return ($row->program ? $row->program->title : 'N/A');
        });

        $datatable->editColumn('studens_no', function ($row) {
            $groupStudents = new GroupStudents();
            $studens_ids = $groupStudents->countStudentGroup($row->id);
            $studens_no = Students::whereIn('id', $studens_ids)->where('delaying', 0)->where('status', 1)->count();
            return '<a href="' . URL("admin/groups/teacher/students/" . Crypt::encrypt($row->id)) . '" class="btn btn-sm btn-light-info fw-bold" title="عرض الطلاب">
                <i class="ki-duotone ki-people fs-2 me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                <span>' . $studens_no . ' طالب</span>
            </a>';
        });

        $datatable->editColumn('time_day', function ($row) {
            return ($row->ctime ? $row->ctime->days . '::' . $row->ctime->times : 'N/A');
        });

        $datatable->editColumn('status', function ($row) {
            $data['id'] = $row->id;
            $data['status'] = $row->status;


            return view('admin.groups.parts.status', $data)->render();
        });

        $datatable->addColumn('actions', function ($row) {
            $data['x'] = 2;
            $data['id'] = $row->id;
            $data['btn_class'] = parent::$data['btn_class'];
            $data['teacher_id'] = $row->teacher_id;
            return view('admin.groups.parts.actions', $data)->render();
        });
        $datatable->escapeColumns(['*']);
        return $datatable->make(true);
    }

    //////////////////////////////////////////////
    public function getAdd()
    {
        return view('admin.programs.add', parent::$data);
    }

    //////////////////////////////////////////////
    public function postAdd(Request $request)
    {

        $title = $request->get('title');
        $short = $request->get('short');
        $image = $request->get('image');
        $exam = (int) $request->get('exam');
        $status = (int) $request->get('status');

        $validator = Validator::make([
            'title' => $title,
            'image' => $image,
            'short' => $short,
        ], [
            'title' => 'required',
            'image' => 'required',
            'short' => 'required',
        ]);
        //////////////////////////////////////////////////////////
        if ($validator->fails()) {
            $request->session()->flash('danger', $validator->messages());
            return redirect(route('programs.add'))->withInput();
        } else {
            $programs = new Programs();
            $add = $programs->addProgram($title, $short, $exam, $status, $image);
            if ($add) {
                if (is_object($add) && isset($add->id)) {
                    $this->applyPlacementDefault($request, $add->id);
                }
                $request->session()->flash('success', self::INSERT_SUCCESS_MESSAGE);
                return redirect(route('programs.view'));
            } else {
                $request->session()->flash('danger', self::EXECUTION_ERROR);
                return redirect(route('programs.add'))->withInput();
            }
        }
    }

    /**
     * Set/clear the single "default placement-test program" flag. When turning it on for a
     * program, it is cleared from every other program (only one default at a time).
     */
    private function applyPlacementDefault(Request $request, $programId)
    {
        if (!$programId) return;
        if ($request->boolean('is_placement_test_default')) {
            Programs::where('id', '!=', $programId)->update(['is_placement_test_default' => 0]);
            Programs::where('id', $programId)->update(['is_placement_test_default' => 1]);
        } else {
            Programs::where('id', $programId)->update(['is_placement_test_default' => 0]);
        }
    }

    //////////////////////////////////////////////
    public function getEdit(Request $request, $id)
    {
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('news.view'));
        }
        //////////////////////////////////////////////
        $programs = new Programs();
        $info = $programs->getProgram($id);
        if ($info) {
            parent::$data['info'] = $info;
            return view('admin.programs.edit', parent::$data);
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('programs.view'));
        }
    }

    ////////////////////////////////////////////////
    public function postEdit(Request $request, $id)
    {
        try {
            $encrypted_id = $id;
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('pages.view'));
        }
        /////////////////////////////
        $programs = new Programs();
        $info = $programs->getProgram($id);
        if ($info) {
            $title = $request->get('title');
            $short = $request->get('short');
            $image = $request->get('image');
            $exam = (int) $request->get('exam');
            $status = (int) $request->get('status');

            $validator = Validator::make([
                'title' => $title,
                'image' => $image,
                'short' => $short,
            ], [
                'title' => 'required',
                'image' => 'required',
                'short' => 'required',
            ]);
            if ($validator->fails()) {
                $request->session()->flash('danger', $validator->messages());
                return redirect(route('programs.edit', ['id' => $encrypted_id]))->withInput();
            } else {
                $update = $programs->updateProgram($info, $title, $short, $exam, $status, $image);
                if ($update) {
                    $this->applyPlacementDefault($request, $id);
                    $request->session()->flash('success', self::UPDATE_SUCCESS);
                    return redirect(route('programs.view'));
                } else {
                    $request->session()->flash('danger', self::EXECUTION_ERROR);
                    return redirect(route('programs.edit', ['id' => $encrypted_id]))->withInput();
                }
            }
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('programs.view'));
        }
    }

    ////////////////////////////////////////////////
    public function postDelete(Request $request)
    {
        $id = $request->get('id');
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }
        /////////////////////////////////////
        $programs = new Programs();
        $info = $programs->getProgram($id);
        if ($info) {
            $delete = $programs->deleteProgram($info);
            if ($delete) {

                return response()->json(['status' => 'success', 'message' => self::DELETE_SUCCESS]);
            } else {
                return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }
    }

    //////////////////////////////////////////////
    public function postStatus(Request $request)
    {
        $id = $request->get('id');
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }
        /////////////////////////////////////
        $programs = new Programs();
        $info = $programs->getProgram($id);
        if ($info) {
            $status = $info->status;
            if ($status == 0) {
                $update = $programs->updateStatus($id, 1);
                if ($update) {
                    return response()->json(['status' => 'success', 'message' => self::ACTIVATION_SUCCESS, 'type' => 'yes']);
                } else {
                    return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
                }
            } else {
                $update = $programs->updateStatus($id, 0);
                if ($update) {
                    return response()->json(['status' => 'success', 'message' => self::DISABLE_SUCCESS, 'type' => 'no']);
                } else {
                    return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
                }
            }
        } else {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }
    }
    public function getProgramGroupsDetails(Request $request, $id) {
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }

        $programs = new Programs();
        $info = $programs->getProgram($id);

        if ($info) {
            $groups = Groups::where('program_id', $id)
                ->with(['teacher', 'ctime'])
                ->whereNull('deleted_at')
                ->get();

            if ($groups->count() > 0) {
                $html = view('admin.programs.parts.program_details', ['groups' => $groups, 'program' => $info])->render();
                return response()->json(['status' => 'success', 'html' => $html, 'title' => $info->title]);
            } else {
                return response()->json(['status' => 'empty', 'message' => 'لا يوجد مجموعات لهذا البرنامج حالياً']);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }
    }
}
