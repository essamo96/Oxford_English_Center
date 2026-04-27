<?php

namespace App\Http\Controllers\Admin;

use App\Models\Absent_Teacher;
use App\Models\PermissionsGroup;
use App\Models\RoleHasPermissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Encryption\DecryptException;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Cache;
use DB ;
class Absent_TeacherController extends AdminController
{

    const INSER_SUCCESS_MESSAGE = "نجاح، تم الإضافة بتجاح";
    const UPDATE_SUCCESS = "نجاح، تم التعديل بنجاح";
    const DELETE_SUCCESS = "نجاح، تم الحذف بنجاح";
    const PASSWORD_SUCCESS = "نجاح، تم تغيير كلمة المرور بنجاح";
    const EXECUTION_ERROR = "عذراً، حدث خطأ أثناء تنفيذ العملية";
    const NOT_FOUND = "عذراً،لا يمكن العثور على البيانات";
    const ACTIVATION_SUCCESS = "نجاح، تم التفعيل بنجاح";
    const DISABLE_SUCCESS = "نجاح، تم التعطيل بنجاح";

    protected $path;

    //////////////////////////////////////////////

    public function __construct()
    {
        parent::__construct();
        parent::$data['active_menu'] = 'absent_teacher';
        $this->path = 'absent_teacher';
    }

    //////////////////////////////////////////////
    public function getIndex(Request $request)
    {
//                $name = $request->get('name');
//        $grope = $request->get('grope') ?? '';
//        $info = new Absent_Teacher();
//        $info = $info->getAbsent_Teacher($name ,$grope);
//        dd($info);
//        $teacherData = DB::table('absent_teacher')
//    ->select('groups.name as group_name', 'absent_teacher.group_id')
//    ->selectRaw('COUNT(DISTINCT absent_teacher.days) as days_count')
//    ->join('groups', 'absent_teacher.group_id', '=', 'groups.id')
//    ->where('absent_teacher.teacher_id', $teacherId)
//    ->groupBy('groups.id', 'groups.name')
//    ->get();
//dd($teacherData);
// للحصول على قائمة الأيام مع تفاصيلها

//    $group->days = DB::table('absent_teacher')
//        ->select('days') // استبدل 'other_columns' بأسماء الأعمدة الأخرى التي تحتاجها
//        ->where('teacher_id', $teacherId)
//        ->where('group_id', $data->group_id)
//        ->distinct()
//        ->get();
//    
//    $teacherGroupsData[] = $group;
//}

        return view('admin.' . $this->path . '.view', parent::$data);
    }

    public function getList(Request $request)
    {
        $name = $request->get('title');
        $info = (new Absent_Teacher())->getAbsent_Teacher($name);

        $datatable = Datatables::of($info);
        
        $datatable->editColumn('teacher_id', function ($row) {
            return $row->teacher ? $row->teacher->name : '<span class="text-danger">مدرس محذوف</span>';
        });
        
        $datatable->editColumn('group_id', function ($row) {
            return '<span class="badge badge-light-info fw-bold">' . ($row->groups_count ?? 0) . '</span>';
        });
        
        $datatable->editColumn('dayes_number', function ($row) {
            return '<span class="badge badge-light-primary fw-bold">' . ($row->days_count ?? 0) . '</span>';
        });
        
        $datatable->editColumn('dayes', function ($row) {
            $teacherId = $row->teacher_id; 
            $url = route('absent_teacher.groups', ['teacher_id' => $teacherId]); 
            return '<a href="'.$url.'" class="btn btn-light-info btn-sm btn-icon-info fw-bold">
                        <i class="ki-duotone ki-calendar-search fs-4 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                        عرض التفاصيل
                    </a>';
        });

        $datatable->editColumn('checkbox', function ($row) {
            if ($row->teacher && !empty($row->teacher->mobile)) {
                $mobile = $row->teacher->mobile;
                $email = $row->teacher->email;
                $id = $row->teacher_id;
                return '<div class="d-flex align-items-center justify-content-center" style="height: 48px;">
                            <div class="form-check form-check-custom form-check-solid">
                                <input type="checkbox" class="form-check-input select-checkbox checkboxes h-20px w-20px" name="mobile[' . $id . ']" value="' . $mobile . '" data-mob="' . $mobile . '"  data-email="' . $email . '"  data-id="' . $id . '"/>
                            </div>
                        </div>';
            }
            return '';
        });

        $path = $this->path;
        $datatable->addColumn('actions', function ($row) use ($path) {
            $data['active_menu'] = $path;
            $data['id'] = $row->id;
            return view('admin.' . $this->path . '.parts.actions', $data)->render();
        });

        $start = $request->get('start');
        $datatable->addColumn('DT_RowIndex', function ($row) use (&$start) {
            return ++$start;
        });

        $datatable->rawColumns(['teacher_id', 'group_id', 'dayes_number', 'dayes', 'checkbox', 'actions']);
        
        return $datatable->make(true);
    }

    //////////////////////////////////////////////
//public function showGroups($teacher_id)
//{
//    $teacherGroups = Absent_Teacher::select('group_id', 'days')
//        ->where('teacher_id', $teacher_id)
//        ->get();
//
//    $teacherGroupsArray = [];
//        foreach ($teacherGroups as $group) {
//            $groupDetails = (object)[
//                'group_id' => $group['group_id'],
//                'days' => $group['days'],
//                'days_count' => $group['days_count'],
//            ];
//
//                   $teacherGroupsArray[] = $groupDetails;
//        }
//   
//        return view('admin.' . $this->path . '.show_grope', compact('groupedTeacherGroups'));
//}
public function showGroups($teacher_id)
 {
//        $teacherGroups = Absent_Teacher::select('group_id', 'days')
//                ->where('teacher_id', $teacher_id)
//                ->get();
//
//        $teacherGroupsArray = [];
//
//        foreach ($teacherGroups as $group) {
//            $groupDetails = [
//                'group_id' => $group->group_id,
//                'days' => explode(',', $group->days),
//                'days_count' => count(explode(',', $group->days)),
//            ];
//
//            $teacherGroupsArray[] = $groupDetails;
//             parent::$data['teacherGroupsArray'] = $teacherGroupsArray;
//             dd(parent::$data['teacherGroupsArray']);
//        }
     $teacherData = DB::table('absent_teacher')
    ->select('groups.name as group_name', 'absent_teacher.group_id','absent_teacher.teacher_id')
    ->selectRaw('COUNT(DISTINCT absent_teacher.days) as days_count')
    ->join('groups', 'absent_teacher.group_id', '=', 'groups.id')
    ->where('absent_teacher.teacher_id', $teacher_id)
    ->groupBy('groups.id', 'groups.name')
    ->get();
//dd($teacherData);
     
     parent::$data['info'] = $teacherData;
        return view('admin.' . $this->path . '.show_grope', parent::$data);
    }

    //////////////////////////////////////////////
    public function getAdd(Request $request)
    {
        // dd(123);
        parent::$data['info'] = NULL;
        // parent::$data['form_class'] = NULL;
        return view('admin.' . $this->path . '.add', parent::$data);
    }

    //////////////////////////////////////////////
    public function postAdd(Request $request)
    {
        $name = $request->get('name');
        // $is_user = $request->get('is_user');
        $status = $request->has('status') && $request->input('status') === '1' ? 1 : 0;
        $validator = Validator::make([
            'name' => $name,
            // 'is_user' => $is_user,
            'status' => $status
        ], [
            'name' => 'required',
            'status' => 'required|numeric|in:0,1',
            // 'is_user' => 'required|numeric|in:0,1'
        ]);

        if ($validator->fails()) {
            $request->session()->flash('danger', $validator->messages());
            return redirect(route($this->path . '.add'))->withInput();
        } else {
            $role = new Absent_Teacher();
            $add = $role->addRole($name, $status);

            if ($add) {
                Cache::forget('spatie.permission.cache');
                $request->session()->flash('success', self::INSERT_SUCCESS_MESSAGE);
                return redirect(route($this->path . '.view'));
            } else {
                $request->session()->flash('danger', self::EXECUTION_ERROR);
                return redirect(route($this->path . '.add'))->withInput();
            }
        }
    }

    //////////////////////////////////////////////
    public function getEdit(Request $request, $id)
    {
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route($this->path . '.view'));
        }
        if ($id == 1) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route($this->path . '.view'));
        }

        $role = new Absent_Teacher();
        $info = $role->getRole($id);
        if ($info) {
            parent::$data['info'] = $info;
            return view('admin.' . $this->path . '.add', parent::$data);
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route($this->path . '.view'));
        }
    }

    //////////////////////////////////////////////
    public function postEdit(Request $request, $id)
    {
        try {
            $encrypted_id = $id;
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route($this->path . '.view'));
        }
        if ($id == 1) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route($this->path . '.view'));
        }

        $role = new Absent_Teacher();
        $info = $role->getRole($id);
        if ($info) {
            $name = $request->get('name');
            
            $status = $request->has('status') && $request->input('status') === '1' ? 1 : 0;
            $validator = Validator::make([
                'name' => $name,
                // 'is_user' => $is_user,
                'status' => $status
            ], [
                'name' => 'required',
                'status' => 'required|numeric|in:0,1',
                // 'is_user' => 'required|numeric|in:0,1'
            ]);
            if ($validator->fails()) {
                $request->session()->flash('danger', $validator->messages());
                return redirect(route($this->path . '.edit', ['id' => $encrypted_id]))->withInput();
            } else {
                $update = $role->updateRole($info, $name, $status);
                if ($update) {
                    Cache::forget('spatie.permission.cache');
                    $request->session()->flash('success', self::UPDATE_SUCCESS);
                    return redirect(route($this->path . '.view'));
                } else {
                    $request->session()->flash('danger', self::EXECUTION_ERROR);
                    return redirect(route($this->path . '.edit', ['id' => $encrypted_id]))->withInput();
                }
            }
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route($this->path . '.view'));
        }
    }

    //////////////////////////////////////////////
    public function getPermissions(Request $request, $id)
    {
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }

        $roles = new Absent_Teacher();
        $info = $roles->getRole($id);
        if ($info) {
            parent::$data['btn_primary'] = 'btn-success';
            $permission_group = new PermissionsGroup();
            parent::$data['permission_group'] = $permission_group->getAllPermissionGroup();
            $role_has_permissions = new RoleHasPermissions();
            parent::$data['role_permissions'] = $role_has_permissions->getRoleHasPermissionsByRoleId($id);
            parent::$data['info'] = $info;
            return view('admin.' . $this->path . '.permissions', parent::$data);
        } else {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route($this->path . '.view'));
        }
    }

    //////////////////////////////////////////////
    public function postPermissions(Request $request, $id)
    {
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }

        $permissions = $request->get('permissions');

        if (sizeof($permissions) > 0) {
            $role_has_permissions = new RoleHasPermissions();
            $role_has_permissions->deleteRoleHasPermissionsByRoleId($id);

            foreach ($permissions as $permission_id) {
                $role_has_permissions = new RoleHasPermissions();
                $add = $role_has_permissions->addRoleHasPermissions($permission_id, $id);
            }
            Cache::forget('spatie.permission.cache');

            $request->session()->flash('success', self::UPDATE_SUCCESS);
            return redirect(route($this->path . '.permissions', ['id' => Crypt::encrypt($id)]));
        } else {
            $role_has_permissions = new RoleHasPermissions();
            $role_has_permissions->deleteRoleHasPermissionsByRoleId($id);
            $request->session()->flash('success', self::UPDATE_SUCCESS);
            return redirect(route($this->path . '.permissions', ['id' => Crypt::encrypt($id)]));
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



        $roles = new Absent_Teacher();
        $info = $roles->getRole($id);
        if ($info) {
            $status = $info->status;
            if ($status == 0) {
                $delete = $roles->updateStatus($id, 1);
                if ($delete) {
                    Cache::forget('spatie.permission.cache');
                    return response()->json(['status' => 'success', 'message' => self::ACTIVATION_SUCCESS, 'type' => 'yes']);
                } else {
                    return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
                }
            } else {
                $delete = $roles->updateStatus($id, 0);
                if ($delete) {
                    return response()->json(['status' => 'success', 'message' => self::DISABLE_SUCCESS, 'type' => 'no']);
                } else {
                    return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
                }
            }
        } else {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }
    }

    //////////////////////////////////////////////
    public function postDelete(Request $request)
    {
        $id = $request->get('id');
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }

        if ($id == 1) {
            $request->session()->flash('danger', self::NOT_FOUND);
            return response()->json(['status' => 'error', 'message' => 'Error, Data not found']);
        }
        $roles = new Absent_Teacher();
        $info = $roles->getRole($id);
        if ($info) {
            $delete = $roles->deleteRole($info);
            if ($delete) {
                Cache::forget('spatie.permission.cache');
                return response()->json(['status' => 'success', 'message' => self::DELETE_SUCCESS]);
            } else {
                return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }
    }
}
