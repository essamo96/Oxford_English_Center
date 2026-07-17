<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class ProfileController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        parent::$data['active_menu'] = 'profile';
    }

    public function index()
    {
        $user = Auth::user();
        parent::$data['user'] = $user;
        return view('admin.profile.index', parent::$data);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => 'required|string|max:191',
            'username' => ['required', 'string', 'max:191', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:191', Rule::unique('users')->ignore($user->id)],
            'mobile' => ['nullable', 'string', 'max:191', Rule::unique('users')->ignore($user->id)],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ];

        // Only validate password if the user intends to change it
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $messages = [
            'name.required' => 'حقل الاسم مطلوب.',
            'username.required' => 'حقل اسم المستخدم مطلوب.',
            'username.unique' => 'اسم المستخدم هذا مسجل ومستخدم من قبل.',
            'email.required' => 'حقل البريد الإلكتروني مطلوب.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'email.unique' => 'البريد الإلكتروني هذا مسجل ومستخدم من قبل.',
            'mobile.unique' => 'رقم الجوال هذا مسجل ومستخدم من قبل.',
            'password.required' => 'حقل كلمة المرور مطلوب.',
            'password.min' => 'يجب أن لا تقل كلمة المرور عن 8 أحرف.',
            'password.confirmed' => 'كلمات المرور غير متطابقة.',
            'image.image' => 'يجب أن يكون الملف المرفق صورة.',
            'image.mimes' => 'يجب أن تكون الصورة بإحدى الصيغ التالية: jpeg, png, jpg, gif, svg, webp.',
            'image.max' => 'حجم الصورة يجب أن لا يتجاوز 2 ميجابايت.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'يرجى التأكد من البيانات المدخلة.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user->name = $request->input('name');
            $user->username = $request->input('username');
            $user->email = $request->input('email');
            $user->mobile = $request->input('mobile');

            if ($request->filled('password')) {
                $user->password = Hash::make($request->input('password'));
            }

            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($user->image && File::exists(public_path('assets/media/avatars/' . $user->image))) {
                    File::delete(public_path('assets/media/avatars/' . $user->image));
                }

                $image = $request->file('image');
                $imageName = time() . '_' . uniqid() . '.' . $image->extension();
                $image->move(public_path('assets/media/avatars'), $imageName);
                $user->image = $imageName;
            }

            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'تم تحديث الملف الشخصي بنجاح.',
                'image_url' => $user->image ? asset('assets/media/avatars/' . $user->image) : asset('assets/media/avatars/blank.png')
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ غير متوقع أثناء الحفظ: ' . $e->getMessage()
            ], 500);
        }
    }

    // AJAX checks
    public function checkUnique(Request $request)
    {
        $user = Auth::user();
        $field = $request->input('field'); // email, username, or mobile
        $value = $request->input('value');

        if (!in_array($field, ['email', 'username', 'mobile'])) {
            return response()->json(['valid' => false, 'message' => 'حقل غير صالح.']);
        }

        if (empty($value)) {
            return response()->json(['valid' => true]);
        }

        $exists = User::where($field, $value)->where('id', '!=', $user->id)->exists();

        if ($exists) {
            $msg = 'هذه القيمة مستخدمة بالفعل.';
            if ($field == 'email') $msg = 'البريد الإلكتروني مسجل مسبقاً.';
            if ($field == 'username') $msg = 'اسم المستخدم غير متاح.';
            if ($field == 'mobile') $msg = 'رقم الجوال مسجل مسبقاً.';
            return response()->json(['valid' => false, 'message' => $msg]);
        }

        return response()->json(['valid' => true]);
    }
}
