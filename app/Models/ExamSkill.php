<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamSkill extends Model
{
    use SoftDeletes;

    protected $table = 'exam_skills';

    protected $fillable = ['name_en', 'name_ar', 'slug', 'status'];

    public function questions()
    {
        return $this->hasMany(ExamQuestion::class, 'skill_id');
    }
}
