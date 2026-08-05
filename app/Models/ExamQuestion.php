<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamQuestion extends Model
{
    use SoftDeletes;

    protected $table = 'exam_questions';

    protected $fillable = [
        'branch_id', 'program_id', 'skill_id', 'type', 'difficulty',
        'question_text', 'image_path', 'audio_path', 'explanation',
        'estimated_time_seconds', 'marks', 'tags', 'status',
        'created_by_type', 'created_by_id',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function program()
    {
        return $this->belongsTo(Programs::class, 'program_id');
    }

    public function skill()
    {
        return $this->belongsTo(ExamSkill::class, 'skill_id');
    }

    public function options()
    {
        return $this->hasMany(ExamQuestionOption::class, 'question_id');
    }

    // The owning teacher when created_by_type = 'teacher'
    public function teacher()
    {
        return $this->belongsTo(Teachers::class, 'created_by_id');
    }

    public function scopeVisibleToTeacher($query, $teacherId)
    {
        return $query->where(function ($q) use ($teacherId) {
            $q->where('created_by_type', 'admin')
              ->orWhere(function ($qq) use ($teacherId) {
                  $qq->where('created_by_type', 'teacher')->where('created_by_id', $teacherId);
              });
        });
    }
}
