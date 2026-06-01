<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherSalaryDetail extends Model
{
    protected $table = 'teacher_salary_details';

    protected $fillable = [
        'salary_form_id', 'group_id', 'lecture_date', 'lectures', 'rate', 'amount',
    ];

    public function form()
    {
        return $this->belongsTo(TeacherSalaryForm::class, 'salary_form_id');
    }

    public function group()
    {
        return $this->belongsTo(Groups::class, 'group_id');
    }
}
