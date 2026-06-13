<?php

namespace App\Models;

use DB;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Absent_Teacher extends Model
{
    use Notifiable, SoftDeletes, HasRoles;
    //////////////////////////////////////////////
    protected $table = 'absent_teacher';
    protected $fillable = [
        'teacher_id','group_id', 'days', 'status', 'lecture_no', 'recorded_at', 'ip_address'
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $guard_name = 'admin';
    //////////////////////////////////////////////
    public function teacher()
    {
        return $this->belongsTo('App\Models\Teachers', 'teacher_id');
    }
    public function group()
    {
        return $this->belongsTo('App\Models\Groups', 'group_id');
    }

    //////////////////////////////////////////////
    function addAbsent_Teacher($teacher_id, $group_id, $days, $status)
    {
        $this->teacher_id = $teacher_id;
        $this->group_id = $group_id;
        $this->days = $days;
        $this->status = $status;

        $this->save();
        return $this;
    }
    //////////////////////////////////////////////
    function updateAbsent_Student($obj,$teacher_id, $group_id, $days, $status)
    {
        $obj->teacher_id = $teacher_id;
        $obj->group_id = $group_id;
        $obj->days = $days;
        $obj->status = $status;
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

//function getAbsent_Teacher($teacher_id, $grope)
//{
//  
//    return $this
//    ->select('teacher_id')
//    ->selectRaw('COUNT(DISTINCT days) as days_count')
//    ->selectRaw('COUNT(DISTINCT CONCAT(group_id, days)) as groups_count')
//    ->groupBy('teacher_id')
//    ->get();
//}
    function getAbsent_Teacher($name = null)
    {
        return $this
            ->select('teacher_id')
            ->selectRaw('COUNT(days) as days_count')
            ->selectRaw('COUNT(DISTINCT group_id) as groups_count')
            ->whereHas('teacher', function ($q) use ($name) {
                if ($name != "") {
                    $q->where('name', 'LIKE', '%' . $name . '%');
                }
                // BranchScope on Teachers model auto-filters by branch here
            })
            ->groupBy('teacher_id');
    }

    //////////////////////////////////////////////
    function getAttendanceWithCount($teacher_id,$group_id)
    {
        return
        $this
            ->join('students', 'students.id', '=', 'absent_student.student_id')
            ->select('students.id', 'students.name', DB::raw('COUNT(*) as attendance_count'))
            ->where('teacher_id', $teacher_id)->where('group_id', $group_id)
            ->groupBy('students.id', 'students.name')
            ->get();
    }
// return $this->join('students', 'students.id', '=', 'absent_student.student_id')
//     ->select('students.id', 'students.name', DB::raw('COUNT(*) as attendance_count'))
//     ->where('teacher_id', $teacher_id)
//     ->where('group_id', $group_id)
//     ->groupBy('students.id', 'students.name')
//     ->get();
}