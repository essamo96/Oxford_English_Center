<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class GroupStudents extends Authenticatable {

    use Notifiable,
        SoftDeletes,
        HasRoles;

    protected $table = 'group_students';
    protected $guarded = ['created_at', 'deleted_at','updated_at'];
    //////////////////////////////////////////////
    public function fees() {
        return $this->hasMany('App\Models\Fees', 'student_id', 'student_id');
    }

    public function group() {
        return $this->hasOne('App\Models\Groups', 'id', 'group_id');
    }

    // public function student() {
    //     return $this->hasMany('App\Models\Students', 'student_id', 'id');
    // }
    public function student() {
        return $this->belongsTo('App\Models\Students', 'student_id', 'id');
    }

    // public function teacher() {
    //     return $this->hasOne('App\Models\Groups', 'id', 'teacher_id');
    // }

//////////////////////////////////////////////
    function addGroupStudent($student_id, $student_fee_total=0, $student_book_total=0, $group_id) {
        $this->student_id = $student_id;
        $this->student_fee_total = $student_fee_total;
        $this->student_book_total = $student_book_total;
        $this->group_id = $group_id;
        $this->save();
        return $this;
    }
    function split_myString($str) {
        $myString = substr($str,0,5);
        return  $myString;
    }

    //////////////////////////////////////////////
    function updateGroupStudent($id, $exam, $degree) {
        return $this
                        ->where('id', '=', $id)
                        ->update([
                            $exam => $degree
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
    function getIdTodelete($student_id,$group_id) {
        return $this->where('student_id',$student_id)->where('group_id',$group_id)->get();
    }

//////////////////////////////////////////////
    function getSearchStudents($title, $group_id) {
        return $this->select('*', 'group_students.id as gid')->whereHas('fees', function ($query) use ($group_id) {
                            $query->where('group_id', '=', $group_id);
                        })
                        ->where('group_id', $group_id)
                        ->join('students', function($join)use ($title) {
                            $join->on('students.id', '=', 'student_id');
                            if ($title != "") {
                                $join->where('name', 'LIKE', '%' . $title . '%');
                            }
                        })
                        ->orderBy('group_students.id', 'desc')
                        ->get();
    }

    function getStudentGroup($student_id) {
        return $this->with('group')
                        ->where('student_id', $student_id)
                        ->orderBy('group_students.id', 'desc')
                        ->get();
    }
    function searchCertificates($mobile) {
                        return $this
            // ->with(['group' => function ($query) use ($teacher_ids) {
            //     $query->whereIn('teacher_id', $teacher_ids); 
            // }])
            ->whereHas('student', function ($query) use ($mobile) {
            if ($mobile != "") {
                $query->where('mobile', $mobile);
            }
            })
            ->where('total_degree', '>', 75)
            ->where('cer_code','!=', null)
            ->whereNull('deleted_at')
            ->get();
    }
    function getStudentGroupName($user_id) {
        return $this->where('student_id', $user_id)->latest('id')->first();

    }

    function getSearchFees($title, $group_id) {
        return $this->whereHas('fees', function ($query) use ($group_id) {
                            $query->where('group_id', '=', 'group_students.group_id');
                        })
                        ->select('students.name as student_name', 'groups.name as group_name')
                        ->where(function($query) use ($group_id) {
                            if ($group_id != "") {
                                $query->where('group_id', '=', $group_id);
                            }
                        })
                        ->join('students', function($join)use ($title) {
                            $join->on('students.id', '=', 'student_id');
                            if ($title != "") {
                                $join->where('name', 'LIKE', '%' . $title . '%');
                            }
                        })
                        ->join('groups', function($join) {
                            $join->on('groups.id', '=', 'group_students.group_id');
                        })
                        ->orderBy('group_students.id', 'desc')
                        ->get();
    }

    function countStudentGroup($group_id) {
        return $this->where('group_id', $group_id)
            ->pluck('student_id')->all();
    }
    function StudentGroups($student_id) {
        return $this->where('student_id', $student_id)->whereNull('deleted_at')
            ->pluck('group_id')->all();
    }


    function getGroupStudentsForNotification($group_id) {
        return $this->where('group_id', '=', $group_id)
                        ->orderBy('id', 'desc')
                        ->whereNull('deleted_at')->pluck('student_id');
    }
    function getGroupStudents($group_id) {
        return $this->where('group_id', '=', $group_id)
                        ->orderBy('id', 'desc')
                        ->whereNull('deleted_at')->pluck('student_id')->all();
    }
    function getGroupStudentsDegrees($group_id) {
        return $this->with(['student' => function ($query) {$query->where('status',1)->whereNull('deleted_at');}])->where('group_id', '=', $group_id)
                        ->orderBy('id', 'desc')
                        ->whereNull('deleted_at')->get();
    }

    function getAllGroupStudents($group_id) {
        return $this->where('group_id', '=', $group_id)
                        ->orderBy('id', 'desc')
                        ->get();
    }

    function checkStudentGroupExist($student_id, $group_id) {
        return $this
                        ->where('group_id', '=', $group_id)
                        ->where('student_id', '=', $student_id)
                        ->first();
    }
    function checkFirstStudentGroup($student_id) {
        return $this
                        ->where('student_id', '=', $student_id)
                        ->first();
    }
    function getallStudentrow($student_id) {
        return $this
                        ->where('student_id', $student_id)
                        ->whereNull('deleted_at')->pluck('group_id')->all();
                       
    }

    function changeGropeStudent($id ,$group_id) {
        return $this
                        ->where('id', $id)
                        ->update([
                            'group_id' => $group_id
        ]);
    }
      // الدالة المستخدمة لاسترجاع مجموعات الطالب الذي يتم الوصول له من ادارة الاكادمية الطلاب
    // الدالة تستثني المجموعات المحذوفة
    function getAdminMyGroup($student_id, $title)
    {

        return $this
            ->select('group_students.id AS row_id', 'groups.id', 'programs.id AS programID', 'groups.name', 'programs.title AS programName','groups.status AS statusOfGroup', 'teachers.name AS teacherName','group_students.student_id','times.days','times.times')
            ->where('student_id','=', $student_id)
            ->where(
                function ($query) use ($title) {
                    if ($title != "") {
                        $query->where('groups.name', 'LIKE', '%' . $title . '%');
                    }
                }
            )
            ->whereNull('group_students.deleted_at')
            ->join('groups', 'groups.id', '=', 'group_students.group_id')
            ->join('times', 'times.id', '=', 'groups.date_id')
            ->join('programs', 'programs.id', '=', 'groups.program_id')
            ->join('teachers', 'teachers.id', '=', 'groups.teacher_id')
            ->with('student')
            ->get();
    }
    //////////////////////////////////////////////
    function getAllStudentGroupMark($id = null)
    {
        return $this->where('student_id', $id)
       ->get();
    }
    // //////////////////////////////////////////////
    // function getAllStudentGroupMark($mobile = null, $name = null)
    // {
    //     return $this->where(function ($q) use ($mobile) {
    //         if ($mobile) {
    //             $q->where('mobile', 'LIKE', '%' . $mobile . '%');
    //         }
    //     })
    // ->where(function ($q) use ($name) {

    //         if ($name) {
    //             $q->where('name', 'LIKE', '%' . $name . '%');
    //         }
    //     })
    // ->get();
    // }


}
