<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher_Evaluate_Student extends Model
{
    use SoftDeletes;
    //////////////////////////////////////////////
    protected $table = 'teacher_evaluate_student';
    protected $fillable = ['teacher_id', 'student_id', 'group_id', 'total', 'notes', 'evaluation_sort'];
    protected $hidden = [
        '',
    ];
    //////////////////////////////////////////////
    public function questions()
    {
        return $this->hasOne('App\Models\Evaluate_Items', 'id','question_id');
    }
    //////////////////////////////////////////////
    public function teachers()
    {
        return $this->hasOne('App\Models\Teachers', 'id','teacher_id');
    }
    //////////////////////////////////////////////
    //////////////////////////////////////////////
    public function students()
    {
        return $this->hasOne('App\Models\Students', 'id','student_id');
    }
    //////////////////////////////////////////////
    public function groups()
    {
        return $this->hasOne('App\Models\Groups', 'id','group_id');
    }
    function addRole($teacher_id, $student_id, $group_id, $question_id, $answer, $notes, $evaluation_sort)
    {
        $this->teacher_id = $teacher_id;
        $this->student_id = $student_id;
        $this->group_id = $group_id;
        $this->question_id = $question_id;
        $this->answer = $answer;
        $this->notes = $notes;
        $this->evaluation_sort = $evaluation_sort;
        $this->save();
        return $this;
    }
    //////////////////////////////////////////////
    function updateRole($obj, $teacher_id, $student_id, $group_id, $question_id, $answer, $notes, $evaluation_sort)
    {
        $obj->teacher_id = $teacher_id;
        $obj->student_id = $student_id;
        $obj->group_id = $group_id;
        $obj->question_id = $question_id;
        $obj->answer = $answer;
        $obj->notes = $notes;
        $obj->evaluation_sort = $evaluation_sort;

        $obj->save();
        return $obj;
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
    function deleteRole($obj)
    {
        return $obj->delete();
    }
    //////////////////////////////////////////////
    function getRole($id)
    {
        return $this->find($id);
    }
    //////////////////////////////////////////////
    function getAllTeacher_Evaluate_Student()
    {
        return $this->all();
    }
    //////////////////////////////////////////////
    function getTeacher_Evaluate_Student($name = null)
    {
        return $this->where(function ($query) use ($name) {
            if ($name != "") {
                $query->where('name', 'LIKE', '%' . $name . '%');
            }
        })->get();
    }
}
