<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamAttempt extends Model
{
    use SoftDeletes;

    protected $table = 'exam_attempts';

    protected $fillable = [
        'exam_id', 'student_id', 'attempt_number', 'status', 'started_at', 'submitted_at', 'expires_at',
        'total_marks', 'auto_score', 'manual_score', 'final_score', 'percentage', 'recommended_level',
        'violations_count', 'is_auto_submitted', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_auto_submitted' => 'boolean',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function student()
    {
        return $this->belongsTo(Students::class, 'student_id');
    }

    public function answers()
    {
        return $this->hasMany(ExamAttemptAnswer::class, 'attempt_id');
    }

    public function violations()
    {
        return $this->hasMany(ExamViolation::class, 'attempt_id');
    }

    public function reviewRequests()
    {
        return $this->hasMany(ExamReviewRequest::class, 'attempt_id');
    }
}
