<?php

namespace App\Models;

use DB;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Students_Admin_Messages extends Authenticatable
{

    use Notifiable,
        SoftDeletes,
        HasRoles;

    protected $table = 'students_admin_messages';
    protected $fillable = [
        'title', 'content', 'student_id'
    ];
    protected $hidden = [
        '',
    ];
    //////////////////////////////////////////////
    public function student()
    {
        return $this->belongsTo('App\Models\Students', 'student_id');
    }

    //////////////////////////////////////////////
    function SaveMessages($title,  $content , $student_id)
    {
        $this->title = $title;
        $this->content = $content;
        $this->student_id = $student_id;
        $this->save();
        return $this;
    }
    //////////////////////////////////////////////
    function addnotes($id, $note)
    {
        DB::table('students_admin_messages')
            ->where('id', $id)
            ->update(['note' => $note]);
    }

    //////////////////////////////////////////////
    function updateStudent($obj, $name, $username, $password, $mobile, $dob, $job, $email, $join_date, $exam_date, $exam_degree, $status)
    {
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
    function getAllnewStudentsCount()
    {
        return $this->where('status', 0)->whereNull('deleted_at')->count();
    }
    ////////////////////////////////////////////// 
    function updateStudentFrontEnd($obj,  $dob, $job, $email)
    {
        $obj->dob = $dob;
        $obj->job = $job;
        $obj->email = $email;

        $obj->save();
        return $obj;
    }
    //////////////////////////////////////////////
    function updatePassword($id, $password)
    {
        return $this
            ->where('id', '=', $id)
            ->update([
                'password' => $password
            ]);
    }

    //////////////////////////////////////////////
    function updateImage($id, $image)
    {
        return $this
            ->where('id', '=', $id)
            ->update([
                'image' => $image
            ]);
    }
    //////////////////////////////////////////////
    function updateStatus($id, $status)
    {
        return $this
            ->where('id', '=', $id)
            ->update([
                'status' => $status
            ]);
    }
    //////////////////////////////////////////////
    function updateDailling($id, $delaying)
    {
        return $this
            ->where('id', '=', $id)
            ->update([
                'delaying' => $delaying
            ]);
    }
    //////////////////////////////////////////////
    function updateSeen($id)
    {
        return $this
            ->where('id', '=', $id)
            ->update([
                'seen' => 1
            ]);
    }

    //////////////////////////////////////////////
    function deleteStudent($obj)
    {
        return $obj->delete();
    }

    //////////////////////////////////////////////
    function getStudent($id)
    {
        return $this->find($id);
    }

    function getAllStudentsMessages()
    {
        return $this->with('student')->whereNull('deleted_at')->get();
    }
    function getAllUnreadStudentsMessages()
    {
        return $this->where('seen',0)->whereNull('deleted_at')->count();
    }

    //////////////////////////////////////////////
    function getSearchStudents($title = null, $activeS, $delaying)
    {
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
            ->orderBy('id', 'desc')
            ->get();
    }
    //////////////////////////////////////////////
    function getAllStudentsHaveBirthdays($title = null)
    {
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
    function getSearchStudentsAskJoin($title = null)
    {
        return $this->where(function ($query) use ($title) {
            if ($title != "") {
                $query->where('name', 'LIKE', '%' . $title . '%');
                $query->orWhere('mobile', 'LIKE', '%' . $title . '%');
            }
        })->where('status', 0)
            ->orderBy('id', 'desc')
            ->get();
    }

    function getSearchStudentsAjax($title)
    {
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
