<?php

namespace App\Http\Controllers;

use Hash;
use Illuminate\Support\Facades\Auth;
use Session;
use Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller {

    use AuthenticatesUsers;

    public function __construct() {
        parent::__construct();
    }

    ///////////////////////////////
    public function getIndex() {
        if (Auth::guard('students')->check()) {
            return redirect('/student');
        } elseif (Auth::guard('teachers')->check()) {
            return redirect('/teacher');
        }
        
        parent::$data['login_type']= 0;
        return view('frontend.login.view', parent::$data);
    }
    ///////////////////////////////
    public function getTeachrIndex() {
        if (Auth::guard('teachers')->check()) {
            return redirect('/teacher');
        } elseif (Auth::guard('students')->check()) {
            return redirect('/student');
        }

        parent::$data['login_type']= 1;
        return view('frontend.login.view', parent::$data);
    }

    ///////////////////////////////////////////
    public function postIndex(Request $request) {
        $username = $request->get('username');
        $password = $request->get('password');
        $login_type = (int)$request->get('login_type');
        $admin['username'] = $username;
        $admin['password'] = $password;
        $admin['status'] = 1;

        if ($login_type == 0) {
            if (Auth::guard('students')->attempt($admin)) {
                return redirect()->intended('/student');
            } else {
                return redirect('/login')->with(['danger' => 'Error username or password']);
            }
        } else {
             if (Auth::guard('teachers')->attempt($admin)) { 
                return redirect()->intended('/teacher');
            } else {
                return redirect('/login/teacher')->with(['danger' => 'Error username or password']);
            }
        }
       
    }
    
 //////////////////////////////////////////
    public function getLogout()
    {
        Auth::guard('students')->logout();
        Auth::guard('teachers')->logout();
        return redirect('/');   
    }
    //////////////////////////////////////////
}
