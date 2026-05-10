<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoursesStudents extends Model
{
    use SoftDeletes;

    protected $table = 'courses_students';

    public function student()
    {
        return $this->belongsTo('App\Models\Students', 'student_id');
    }

    public function course()
    {
        return $this->belongsTo('App\Models\Courses', 'course_id');
    }
}
