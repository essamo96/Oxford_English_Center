<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use DB;

class Fees extends Authenticatable {

    use Notifiable,
        SoftDeletes,
        HasRoles;

    protected $table = 'group_students_fees';
    
    public function student() {
        return $this->belongsTo('App\Models\Students', 'student_id');
    }
    public function group() {
        return $this->belongsTo('App\Models\Groups', 'group_id');
    }

//////////////////////////////////////////////
    function addGroupStudentFees($student_id, $student_fee_paid, $student_paid_type, $group_id) {
        $this->student_id = $student_id;
        $this->student_fee_paid = $student_fee_paid;
        $this->student_paid_type = $student_paid_type;
        $this->group_id = $group_id;
        $this->save();
        return $this;
    }

//////////////////////////////////////////////
    function updateStudentFees($obj, $student_fee_paid, $student_paid_type) {
        $obj->student_fee_paid = $student_fee_paid;
        $obj->student_paid_type = $student_paid_type;

        $obj->save();
        return $obj;
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
    function getFees($id) {
        return $this->find($id);
    }

//////////////////////////////////////////////
    function deleteStudentFees($obj) {
        return $obj->delete();
    }

//////////////////////////////////////////////
    function getStudentFees($id) {
        return $this->find($id);
    }

    function getAllStudentsFees() {
        return $this->where('status', '=', 1)->get();
    }

//////////////////////////////////////////////
    function getSearchStudentsFees($title) {
        return $this->where(function($query) use ($title) {
                            if ($title != "") {
                                $query->where('name', 'LIKE', '%' . $title . '%');
                            }
                        })
                        ->orderBy('id', 'desc')
                        ->get();
    }

    function getSearchStudentsAjax($title) {
        $data = $this->where(function($query) use ($title) {
                    if ($title != "") {
                        $query->where('name', 'LIKE', '%' . $title . '%');
                    }
                })
                ->orderBy('id', 'desc')
                ->get();
        foreach ($data as $it) {
            $sd[] = array('value' => $it->id, 'label' => $it->name);
        }
        return $sd;
    }

    function getSearchFees($title) {
        return $this->select('group_students_fees.*', 'students.name as student_name', 'groups.name as group_name')
                        ->join('students', function($join)use ($title) {
                            $join->on('students.id', '=', 'student_id');
                            if ($title != "") {
                                $join->where('name', 'LIKE', '%' . $title . '%');
                            }
                        })
                        ->join('groups', function($join) {
                            $join->on('groups.id', '=', 'group_students_fees.group_id');
                        })
                        ->get();
    }

    function getMyGroupFees($user_id, $group_id) {
        return $this
                        ->where('student_id', '=', $user_id)
                        ->where('group_id', '=', $group_id)
                        ->get();
    }

}
