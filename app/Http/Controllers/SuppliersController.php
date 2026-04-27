<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use App\Models\Suppliers;
use App\Models\Tenders;
use App\Models\User;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class SuppliersController extends Controller {

    const EXECUTION_ERROR = "عذراً، حدث خطأ أثناء تنفيذ العملية";

    public function __construct() {
        parent::__construct();
    }

    public function getList() {
        $tenders = new Tenders();
        parent::$data['tenders'] = $tenders->getAllTenders();
        return view('frontend.suppliers.list', parent::$data);
    }

    public function getAddtTender() {
        return view('frontend.suppliers.add', parent::$data);
    }

    public function getViewTender() {
        return view('frontend.suppliers.view', parent::$data);
    }

    public function getRegister() {
        return view('frontend.suppliers.register', parent::$data);
    }

    public function postRegister(Request $request) {
        ////////////////////////////////////////////
        $company_name = $request->get('company_name');
        $company_reg_no = $request->get('company_reg_no');
        $company_tax_no = $request->get('company_tax_no');
        $company_person = $request->get('company_person');
        $emp_postion = $request->get('emp_postion');
        $emp_tel = $request->get('emp_tel');
        $emp_mobile = $request->get('emp_mobile');
        $emp_ext = $request->get('emp_ext');
        $email = $request->get('email');
        $password = $request->get('password');
        $password_confirmation = $request->get('password_confirmation');

        $validator = Validator::make([
                    'company_name' => $company_name,
                    'email' => $email,
                    'company_reg_no' => $company_reg_no,
                    'company_tax_no' => $company_tax_no,
                    'company_person' => $company_person,
                    'emp_postion' => $emp_postion,
                    'emp_tel' => $emp_tel,
                    'emp_mobile' => $emp_mobile,
                    'password' => $password,
                    'password_confirmation' => $password_confirmation
                        ], [
                    'company_name' => 'required',
                    'email' => 'required|email|unique:suppliers,emp_email',
                    'company_reg_no' => 'required',
                    'company_tax_no' => 'required',
                    'company_person' => 'required',
                    'emp_postion' => 'required',
                    'emp_tel' => 'required|digits:10|numeric',
                    'emp_mobile' => 'required|digits:10|numeric',
                    'password' => 'required|between:6,16|confirmed',
                    'password_confirmation' => 'required|between:6,16'
        ]);
        //////////////////////////////////////////////////////////
        if ($validator->fails()) {
            Session::flash('danger', $validator->messages());
            return redirect('suppliers/register')->withInput();
        } else {
            $contact = new Suppliers();
            $token = Str::random(40);
            $add = $contact->addSupplier($company_name, $company_reg_no, $company_tax_no, $company_person, $emp_postion, $emp_tel, $emp_mobile, $emp_ext, $email, Hash::make($password), $token);
            $this->getmail($company_person, $email, $token);
            if ($add) {
                Session::flash('success', 'تم الحفظ');
                return redirect('suppliers/register')->withInput();
            } else {
                Session::flash('danger', 'حدثت مشكلة');
                return redirect('suppliers/register')->withInput();
            }
        }
    }

    public function getLogin() {
        return view('frontend.suppliers.login', parent::$data);
    }

    public function doLogin(Request $request) {
        //   $field = filter_var($request->get('username'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $email = $request->get('email');
        $password = $request->get('password');

        $user['emp_email'] = $email;
        $user['password'] = $password;
        $user['status'] = 1;

        $remember = 1; // $request->get('remember');
        if (Auth::guard('web')->attempt($user, $remember)) {
            return redirect()->intended('/');
        } else {
            Session::flash('danger', 'اسم المستخدم او كلمة المرور غير صحيحة او ان الحساب غير مفعل');
            return redirect('login')->withInput();
        }
    }

    public function doLogout() {
        Auth::logout();
        return redirect('/');
    }

    //////////////////////////////////////////////
    public function getForgotPassword() {
        return view('frontend.login.forgot_password', parent::$data);
    }

    public function showProfile() {
        $user = new Suppliers();
        parent::$data['user'] = $user->getSupplier(Auth::guard('web')->user()->id);
        return view('frontend.suppliers.profile', parent::$data);
    }

    public function postProfile(Request $request) {
        $company_name = $request->get('company_name');
        $company_reg_no = $request->get('company_reg_no');
        $company_tax_no = $request->get('company_tax_no');
        $company_person = $request->get('company_person');
        $emp_postion = $request->get('emp_postion');
        $emp_tel = $request->get('emp_tel');
        $emp_mobile = $request->get('emp_mobile');
        $emp_ext = $request->get('emp_ext');
        $password = $request->get('password');

        $validator = Validator::make([
                    'company_name' => $company_name,
                    'company_reg_no' => $company_reg_no,
                    'company_tax_no' => $company_tax_no,
                    'company_person' => $company_person,
                    'emp_postion' => $emp_postion,
                    'emp_tel' => $emp_tel,
                    'emp_mobile' => $emp_mobile,
                    'password' => $password,
                        ], [
                    'company_name' => 'required',
                    'company_reg_no' => 'required',
                    'company_tax_no' => 'required',
                    'company_person' => 'required',
                    'emp_postion' => 'required',
                    'emp_tel' => 'required|digits:10|numeric',
                    'emp_mobile' => 'required|digits:10|numeric',
                    'password' => 'nullable|between:6,16',
        ]);
        //////////////////////////////////////////////////////////
        if ($validator->fails()) {
            Session::flash('danger', $validator->messages());
            return redirect('profile')->withInput();
        } else {
            $user = new Suppliers();
            $info = $user->getSupplier(Auth::guard('web')->user()->id);
            $password = $password != '' ? Hash::make($password) : $password;
            $add = $user->updateSupplierFrontend($info, $company_name, $company_reg_no, $company_tax_no, $company_person, $emp_postion, $emp_tel, $emp_mobile, $emp_ext, $password);
            if ($add) {
                Session::flash('success', 'تم الحفظ');
                return redirect(route('profile'));
            } else {
                Session::flash('danger', self::EXECUTION_ERROR);
                return redirect('profile')->withInput();
            }
        }
    }

    //////////////////////////////////////////////
    public function postForgotPassword(Request $request) {
        $email = $request->get('email');
        //////////////////////////////////////
        $customers = new User();
        $info = $customers->getUserByEmail($email);
        if ($info) {
            $token = Str::random(40);
            $customers->updateToken($info->id, $token);
            $this->send_mail('reset', $info->id);
            Session::flash('success', 'تم ارسال بريد الكتروني لاستعادة كلمة المرور');
            return redirect('login');
        } else {
            Session::flash('success', 'هذا البريد غير موجود');
            return redirect('login/forgotPassword');
        }
    }

    //////////////////////////////////////////////
    public function getResetPassword(Request $request, $email, $token) {

        /////////////////////////////
        $customers = new User();
        $info = $customers->getUserByToken($token);
        if ($info) {
            $data_token = $info->token;
            if ($data_token == $token) {
                return view('frontend.login.reset_password', parent::$data);
            } else {
                Session::flash('success', 'هذا البريد غير موجود');
                return redirect('login/forgotPassword');
            }
        } else {
            Session::flash('success', 'هذا البريد غير موجود');
            return redirect('login/forgotPassword');
        }
    }

    //////////////////////////////////////////////
    public function postResetPassword(Request $request, $email, $token) {
        $password = $request->get('password');
        $password_confirmation = $request->get('password_confirmation');

        $customers = new User();
        $info = $customers->getUserByEmail($email);

        if ($info) {
            $customer_id = $info->id;
            $data_token = $info->token;
            if ($data_token == $token) {
                $validator = Validator::make([
                            'password' => $password,
                            'password_confirmation' => $password_confirmation
                                ], [
                            'password' => 'required|between:6,16|confirmed',
                            'password_confirmation' => 'required|between:6,16'
                ]);
                //////////////////////////////////////////////////////////
                if ($validator->fails()) {
                    Session::flash('success', 'كلمة المرور غير سليمة حاول مرة اخري..');
                    return redirect('login/resetPassword/' . $email . '/' . $token);
                } else {
                    $change = $customers->updatePassword($customer_id, Hash::make($password));
                    if ($change) {
                        $customers->updateToken($customer_id, null);
                        Session::flash('success', 'تم تغير كلمة المرور بنجاح..');
                        return redirect('login');
                    } else {
                        Session::flash('success', 'كلمة المرور غير سليمة حاول مرة اخري..');
                        return redirect('login/resetPassword/' . $email . '/' . $token);
                    }
                }
            } else {
                Session::flash('success', 'لم يتم العثور علي البيانات المطلوبة');
                return redirect('login');
            }
        } else {
            Session::flash('success', 'لم يتم العثور علي البيانات المطلوبة');
            return redirect('login');
        }
    }

    public function verifyUser(Request $request, $token) {
        $verifyUser = new Suppliers();
        $user = $verifyUser->getUserByToken($token);
        if ($user) {
            if (!$user->status) {
                $verifyUser->updateStatus($user->id, 1);
                $verifyUser->updateToken($user->id, null);
                Session::flash('success', 'تم تفعيل البريد الالكتروني يمكنك تسجيل الدخول الان');
            } else {
                Session::flash('success', 'الحساب الخاص بك مفعل من قبل');
            }
        } else {
            Session::flash('success', 'للاسف لم يمكن التعرف علي الايميل الخاص بك');
        }

        return redirect(url('login'));
    }

    public function getmail($name, $email, $token) {
        $mail = new PHPMailer(true); // create a new object
        $mail->IsSMTP(); // enable SMTP
        $mail->CharSet = 'UTF-8';
        $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
        $mail->SMTPAuth = true; // authentication enabled
        $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 465; // or 587
        $mail->IsHTML(true);
        $mail->Username = "info@hayatdowntown.com";
        $mail->Password = "Hayat123downtown#";
        $mail->SetFrom("info@hayatdowntown.com");
        $mail->Subject = "Hayatdowntown Supplier Section";
        $mail->AddAddress($email, $name);
        $data = array(
            'email' => $email,
            'name' => $name,
            'token' => $token,
        );
        $html = view('emails.verify', $data);
        $mail->Body = $html;
        if (!$mail->Send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
            exit;
        } else {
            return 'true';
        }
    }

}
