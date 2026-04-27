<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Encryption\DecryptException;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Cache;

use App\Models\Roles;
use App\Models\User;

class UsersController extends AdminController
{
    const INSERT_SUCCESS_MESSAGE = "نجاح، تم الإضافة بتجاح";
    const UPDATE_SUCCESS = "نجاح، تم التعديل بنجاح";
    const DELETE_SUCCESS = "نجاح، تم الحذف بنجاح";
    const PASSWORD_SUCCESS = "نجاح، تم تغيير كلمة المرور بنجاح";
    const EXECUTION_ERROR = "عذراً، حدث خطأ أثناء تنفيذ العملية";
    const NOT_FOUND = "عذراً،لا يمكن العثور على البيانات";
    const ACTIVATION_SUCCESS = "نجاح، تم التفعيل بنجاح";
    const DISABLE_SUCCESS = "نجاح، تم التعطيل بنجاح";
    protected $path;
    public function __construct()
    {
        parent::__construct();
        parent::$data['active_menu'] = 'users';
        parent::__construct();
        $this->path = 'users';
    }
    //////////////////////////////////////////////
    public function getIndex()
    {
        return view('admin.' . $this->path . '.view', parent::$data);
    }
    //////////////////////////////////////////////
    public function getList(Request $request)
    {
        $user = new User();
        $username = $request->get('username');
        $name = $request->get('name');
        $email = $request->get('email');
        $status = $request->get('status');

        $info = $user->getUsers($username, $name, $email, $status);

        $datatable = Datatables::of($info)->setTotalRecords(sizeof($info));

        $datatable->editColumn('username', function ($row) {
            $imagePath = $row->image ? url($row->image) : asset('assets/media/avatars/blank.png');
            return '
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                        <a href="#">
                            <div class="symbol-label">
                                <img src="' . $imagePath . '" alt="' . $row->username . '" class="w-100" />
                            </div>
                        </a>
                    </div>
                    <div class="d-flex flex-column text-start">
                        <a href="#" class="text-gray-800 text-hover-primary mb-1 fw-bold">' . $row->username . '</a>
                        <span class="fs-7 text-muted">' . $row->email . '</span>
                    </div>
                </div>';
        });

        $datatable->editColumn('name', function ($row) {
            return '<div class="text-gray-800 fw-bold">' . $row->name . '</div>';
        });

        $datatable->editColumn('email', function ($row) {
            return '<a href="mailto:' . $row->email . '" class="text-hover-primary text-gray-600">' . (!empty($row->email) ? $row->email : 'N/A') . '</a>';
        });

        $datatable->addColumn('last_appearance', function ($row) {
            $time = $row->last_login_at ?: $row->created_at;
            return '
                <div class="badge badge-light-info fw-bold">' . ($time ? $time->diffForHumans() : 'لم يظهر بعد') . '</div>
                <div class="text-muted fs-7">' . ($time ? $time->format('Y-m-d H:i') : '') . '</div>';
        });

        $datatable->editColumn('created_by', function ($row) {
            $x = $row->user ? $row->user->username : '--';
            return '<div class="badge badge-light-warning fw-bold">' . $x . '</div>';
        });

        $path = $this->path;
        $datatable->editColumn('status', function ($row) {
            $data['id'] = $row->id;
            $data['status'] = $row->status;
            return view('admin.' . $this->path . '.parts.status', $data)->render();
        });

        $datatable->addColumn('actions', function ($row) use ($path) {
            $data['active_menu'] = $path;
            $data['id'] = $row->id;
            return view('admin.' . $this->path . '.parts.actions', $data)->render();
        });

        $datatable->escapeColumns(['*']);
        return $datatable->make(true);
    }

    //////////////////////////////////////////////
    public function getAdd()
    {
        $roles = new Roles();
        parent::$data['roles'] = $roles->getAllRolesActive();
        parent::$data['info'] = NULL;
        return view('admin.' . $this->path . '.add', parent::$data);
    }
    //////////////////////////////////////////////
    public function postAdd(Request $request)
    {
        $username = $request->get('username');
        $name = $request->get('name');
        $email = $request->get('email');
        $role = $request->get('role');
        $password = $request->get('password');
        $password_confirmation = $request->get('password_confirmation');
        $status = (int)$request->get('status');

        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users,username',
            'name' => 'required',
            'email' => 'required|email',
            'role' => 'required|numeric',
            'password' => 'required|between:6,16|confirmed',
            'password_confirmation' => 'required|between:6,16',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($validator->fails()) {
            $request->session()->flash('danger', $validator->messages());
            return redirect(route('users.add'))->withInput();
        } else {
            $imagePath = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/users'), $filename);
                $imagePath = 'uploads/users/' . $filename;
            }

            $user = new User();
            $add = $user->addUser($username, $name, $email, $role, Auth::guard('admin')->user()->id, Hash::make($password), $status, $imagePath);
            if ($add) {
                $roles = new Roles();
                $new_info = $roles->getRole($role);
                if ($new_info) {
                    $new_role_name = $new_info->name;
                    $add->syncRoles([$new_role_name]);
                }
                Cache::forget('spatie.permission.cache');
                $request->session()->flash('success', self::INSERT_SUCCESS_MESSAGE);
                return redirect(route('users.view'));
            } else {
                $request->session()->flash('danger', self::EXECUTION_ERROR);
                return redirect(route('users.add'))->withInput();
            }
        }
    }

    //////////////////////////////////////////////
    public function getEdit(Request $request, $id)
    {
        try
        {
            $id = Crypt::decrypt($id);
        }
        catch (DecryptException $e)
        {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('users.view'));
        }
        if ($id == 1)
        {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('users.view'));
        }

        $user = new User();
        $roles = new Roles();

        $info = $user->getUser($id);
        if ($info)
        {
            parent::$data['roles'] = $roles->getAllRolesActive();
            parent::$data['info'] = $info;
            return view('admin.' . $this->path . '.add', parent::$data);
        }
        else
        {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route($this->path . '.view'));
        }
    }
    //////////////////////////////////////////////
    public function postEdit(Request $request, $id)
    {
        try
        {
            $encrypted_id = $id;
            $id = Crypt::decrypt($id);
        }
        catch (DecryptException $e)
        {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('users.view'));
        }
        if ($id == 1)
        {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route('users.view'));
        }

        $user = new User();
        $info = $user->getUser($id);
        if ($info)
        {
            $db_role = $info->role;
            $username = $request->get('username');
            $name = $request->get('name');
            $email = $request->get('email');
            $role = $request->get('role');
            $status = (int)$request->get('status');

            $validator = Validator::make($request->all(), [
                'username' => 'required|unique:users,username,' . $id,
                'name' => 'required',
                'email' => 'required|email',
                'role' => 'required',
                'image' => 'nullable|image|max:2048'
            ]);

            if ($validator->fails()) {
                $request->session()->flash('danger', $validator->messages());
                return redirect(route('users.edit', ['id' => $encrypted_id]))->withInput();
            } else {
                $imagePath = $info->image;
                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads/users'), $filename);
                    $imagePath = 'uploads/users/' . $filename;
                }

                $update = $user->updateUser($info, $username, $name, $email, $role, $status, $imagePath);
                if ($update) {
                    $roles = new Roles();
                    $new_info = $roles->getRole($role);
                    if ($new_info) {
                        $new_role_name = $new_info->name;
                        $info->syncRoles([$new_role_name]);
                    }
                    Cache::forget('spatie.permission.cache');
                    $request->session()->flash('success', self::UPDATE_SUCCESS);
                    return redirect(route($this->path . '.view'));
                } else {
                    $request->session()->flash('danger', self::EXECUTION_ERROR);
                    return redirect(route($this->path . '.edit', ['id' => $encrypted_id]))->withInput();
                }
            }
        }

        else
        {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route($this->path . '.view'));
        }
    }
    //////////////////////////////////////////////
    public function postStatus(Request $request)
    {
        $id = $request->get('id');
        try
        {
            $id = Crypt::decrypt($id);
        }
        catch (DecryptException $e)
        {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }
        if ($id == 1)
        {
            $request->session()->flash('danger', self::NOT_FOUND);
            return response()->json(['status' => 'error', 'message' => 'Error, Data not found']);
        }

        $users = new User();
        $info = $users->getUser($id);
        if ($info)
        {
            $status = $info->status;
            if($status == 0)
            {
                $delete = $users->updateStatus($id,1);
                if($delete)
                {
                    return response()->json(['status' => 'success', 'message' => self::ACTIVATION_SUCCESS, 'type' => 'yes']);
                }
                else
                {
                    return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
                }
            }
            else
            {
                $delete = $users->updateStatus($id,0);
                if($delete)
                {
                    return response()->json(['status' => 'success', 'message' => self::DISABLE_SUCCESS, 'type' => 'no']);
                }
                else
                {
                    return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
                }
            }
        }
        else
        {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }
    }
    //////////////////////////////////////////////
    public function getPassword(Request $request, $id)
    {
        try
        {
            $id = Crypt::decrypt($id);
        }
        catch (DecryptException $e)
        {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route($this->path . '.view'));
        }
        if ($id == 1)
        {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route($this->path . '.view'));
        }
        /////////////////////////////
        $user = new User();
        $info = $user->getUser($id);
        if ($info)
        {
            parent::$data['info'] = $info;
            return view('admin.' . $this->path . '.password', parent::$data);
        }
        else
        {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route($this->path . '.view'));
        }
    }
    //////////////////////////////////////////////
    public function postPassword(Request $request, $id)
    {
        try
        {
            $encrypted_id = $id;
            $id = Crypt::decrypt($id);
        }
        catch (DecryptException $e)
        {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route($this->path . '.view'));
        }
        if ($id == 1)
        {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route($this->path . '.view'));
        }

        $user = new User();
        $info = $user->getUser($id);
        if ($info)
        {
            $password = $request->get('password');
            $password_confirmation = $request->get('password_confirmation');

            $validator = Validator::make([
                'password' => $password,
                'password_confirmation' => $password_confirmation
            ], [
                'password' => 'required|between:6,16|confirmed',
                'password_confirmation' => 'required|between:6,16'
            ]);

            if ($validator->fails())
            {
                $request->session()->flash('danger', $validator->messages());
                return redirect(route($this->path . '.password', ['id' => $encrypted_id]))->withInput();
            }
            else
            {
                $update = $user->updatePassword($id, Hash::make($password));
                if ($update)
                {
                    $request->session()->flash('success', self::PASSWORD_SUCCESS);
                    return redirect(route($this->path . '.view'));
                }
                else
                {
                    $request->session()->flash('danger', self::EXECUTION_ERROR);
                    return redirect(route($this->path . '.password', ['id' => $encrypted_id]))->withInput();
                }
            }
        }
        else
        {
            $request->session()->flash('danger', self::NOT_FOUND);
            return redirect(route($this->path . '.view'));
        }
    }
    //////////////////////////////////////////////
    public function postDelete(Request $request)
    {
        $id = $request->get('id');
        try
        {
            $id = Crypt::decrypt($id);
        }
        catch (DecryptException $e)
        {
            return response()->json(['status' => 'error', 'message' => 'Error Decode']);
        }
        if ($id == 1)
        {
            $request->session()->flash('danger', self::NOT_FOUND);
            return response()->json(['status' => 'error', 'message' => 'Error, Data not found']);
        }

        $users = new User();
        $info = $users->getUser($id);
        if ($info)
        {
            $delete = $users->deleteUser($info);
            if ($delete)
            {
                return response()->json(['status' => 'success', 'message' => self::DELETE_SUCCESS]);
            }
            else
            {
                return response()->json(['status' => 'error', 'message' => self::EXECUTION_ERROR]);
            }
        }
        else
        {
            return response()->json(['status' => 'error', 'message' => self::NOT_FOUND]);
        }
    }
}
