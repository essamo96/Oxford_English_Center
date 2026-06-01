<?php

namespace App\Models;

use DB;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Absent_Student extends Model
{
    use Notifiable, SoftDeletes, HasRoles;
    //////////////////////////////////////////////
    protected $table = 'absent_student';
    protected $fillable = [
        'teacher_id', 'student_id', 'group_id', 'days', 'status', 'round', 'is_late', 'recorded_at'
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $guard_name = 'admin';
    //////////////////////////////////////////////
//    public function roles()
//    {
//        return $this->belongsTo('App\Models\Roles', 'role');
//    }
    //////////////////////////////////////////////
    public function absent_student()
    {
        return $this->belongsTo('App\Models\Absent_Student', 'created_by');
    }
    //////////////////////////////////////////////
    function addAbsent_Student($teacher_id, $student_id, $group_id, $days, $status)
    {
        $this->teacher_id = $teacher_id;
        $this->student_id = $student_id;
        $this->group_id = $group_id;
        $this->days = $days;
        $this->status = $status;

        $this->save();
        return $this;
    }
    //////////////////////////////////////////////
    function updateAbsent_Student($obj,$teacher_id, $student_id, $group_id, $days, $status)
    {
        $obj->teacher_id = $teacher_id;
        $obj->student_id = $student_id;
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