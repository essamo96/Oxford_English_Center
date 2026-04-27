<?php

namespace App\Models;

use DB;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Pending_Data extends Authenticatable {

    use Notifiable,
        SoftDeletes,
        HasRoles;

    protected $table = 'pending_data';
    protected $guarded = ['id'];
    protected $hidden = [
        '',
    ];

    //////////////////////////////////////////////
    public function students() {
        return $this->belongsTo('App\Models\Students', 'student_id');
    }

//    public function messages()
//    {
//        return $this->hasMany('App\Models\Students_Admin_Messages', 'student_id', 'id');
//    }
//    public function gropes()
//    {
//        return $this->hasMany('App\Models\GroupStudents', 'student_id', 'id');
//    }
//////////////////////////////////////////////
    function updateStudent($obj, $name, $username, $password, $mobile, $dob, $job, $email, $join_date, $exam_date, $exam_degree, $status) {
        $obj->name = $name;
        $this->username = $username;
        $this->password = $password;
        $obj->mobile = $mobile;
        $obj->dob = $dob;
        $obj->job = $job;
        $obj->email = $email;
        $obj->join_date = $join_date;
        $obj->exam_date = $exam_date;
        $obj->exam_degree = $exam_degree;
        $obj->status = $status;

        $obj->save();
        return $obj;
    }

    ////////////////////////////////////////////// 
    function getAllnewStudentsCount() {
        return $this->where('status', 0)->whereNull('deleted_at')->count();
    }

    ////////////////////////////////////////////// 
    function updateStudentFrontEnd($obj, $dob, $job, $email, $name, $mobile) {
        $obj->dob = $dob;
        $obj->job = $job;
        $obj->email = $email;
        $obj->name = $name;
        $obj->mobile = $mobile;
        $obj->save();
        return $obj;
    }

    //////////////////////////////////////////////
    function updatePassword($id, $password) {
        return $this
                        ->where('id', '=', $id)
                        ->update([
                            'password' => $password
        ]);
    }

    //////////////////////////////////////////////
    function updateImage($id, $image) {
        return $this
                        ->where('id', '=', $id)
                        ->update([
                            'image' => $image
        ]);
    }

//////////////////////////////////////////////
    function updateStatus($id, $status) {
        return $this
                        ->where('id', '=', $id)
                        ->update([
                            'status' => $status
        ]);
    }

//////////////////////////////////////////////
    function updateDailling($id, $delaying) {
        return $this
                        ->where('id', '=', $id)
                        ->update([
                            'delaying' => $delaying
        ]);
    }

//////////////////////////////////////////////
    function deleteStudent($obj) {
        return $obj->delete();
    }

//////////////////////////////////////////////
    function getStudent($id) {
        return $this->find($id);
    }
//////////////////////////////////////////////
    function getStudentPending_Data($id) {
        return $this->where('student_id',$id)->where('ask_update',0)->first();
    }

    function getAllStudents() {
        return $this->where('status', '=', 1)->get();
    }

//////////////////////////////////////////////
    function getSearchStudents($title, $activeS, $delaying, $gender) {
        return $this
                        ->where(function ($query) use ($title) {
                            if ($title != "") {
                                $query->where('name', 'LIKE', '%' . $title . '%');
                                $query->orWhere('mobile', 'LIKE', '%' . $title . '%');
                            }
                        })
                        ->where(function ($query) use ($activeS) {
                            if ($activeS != "") {
                                $query->where('status', $activeS);
                            }
                        })
                        ->where(function ($query) use ($delaying) {
                            if ($delaying != "") {
                                $query->where('delaying', $delaying);
                            }
                        })
                        // ->where(function($query) use ($gender) {
                        //                     if ($gender != "") {
                        //                         $query->where('gender', $gender);
                        //                     }
                        //                 })
                        ->orderBy('id', 'desc')->whereNull('deleted_at')
                        ->get();
    }

//////////////////////////////////////////////
    function getSearchDelayStudents($title = null, $activeS) {
        return $this->with('gropes')
                        ->where(function ($query) use ($title) {
                            if ($title != "") {
                                $query->where('name', 'LIKE', '%' . $title . '%');
                                $query->orWhere('mobile', 'LIKE', '%' . $title . '%');
                            }
                        })
                        ->where(function ($query) use ($activeS) {
                            if ($activeS != "") {
                                $query->where('status', $activeS);
                            }
                        })
                        ->where('delaying', 1)
                        ->orderBy('id', 'desc')
                        ->get();
    }

//////////////////////////////////////////////
    function getAllStudentsHaveBirthdays($title = null) {
        return $this
                        ->where(function ($query) use ($title) {
                            if ($title != "") {
                                $query->where('name', 'LIKE', '%' . $title . '%');
                                $query->orWhere('mobile', 'LIKE', '%' . $title . '%');
                            }
                        })->whereMonth('dob', date('m'))
                        ->whereDay('dob', date('d'))
                        ->whereNull('deleted_at')
                        ->orderBy('id', 'desc')
                        ->get();
    }

//////////////////////////////////////////////
    function getAllStudentsHaveBirthdaysCount() {
        return $this
                        ->whereMonth('dob', date('m'))
                        ->whereDay('dob', date('d'))
                        ->whereNull('deleted_at')
                        ->orderBy('id', 'desc')
                        ->count();
    }

//////////////////////////////////////////////
    function getSearchStudentsAskJoin($title = null) {
        return $this->where(function ($query) use ($title) {
                            if ($title != "") {
                                $query->where('name', 'LIKE', '%' . $title . '%');
                                $query->orWhere('mobile', 'LIKE', '%' . $title . '%');
                            }
                        })->where('status', 0)
                        ->orderBy('id', 'desc')
                        ->get();
    }

//////////////////////////////////////////////
    function getSearchStudentsAsk_update($title = null) {
        return $this->where(function ($query) use ($title) {
                            if ($title != "") {
                                $query->where('name', 'LIKE', '%' . $title . '%');
                                $query->orWhere('mobile', 'LIKE', '%' . $title . '%');
                            }
                        })->where('ask_update', 0)
                        ->orderBy('id', 'desc')
                        ->get();
    }

    function getSearchStudentsAjax($title) {
        $data = $this->where(function ($query) use ($title) {
                    if ($title != "") {
                        $query->where('name', 'LIKE', '%' . $title . '%');
                    }
                })
                ->orderBy('id', 'desc')
                ->get();
        $sd = array();
        foreach ($data as $it) {
            $sd[] = array('value' => $it->id, 'label' => $it->name);
        }
        return $sd;
    }
}
