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

    // withTrashed(): deleting an Exam only soft-deletes it (SoftDeletes on the Exam model), but
    // an attempt must always be able to resolve its exam — otherwise an in-progress attempt for
    // a since-deleted exam hard-crashes the student's take/submit/result screens, and every
    // historical grading/report screen (admin, teacher) breaks for any attempt tied to a deleted
    // exam. Deleting an exam already blocks NEW attempts (Exam::find() in start()/getIndex()
    // correctly excludes trashed rows) — this only keeps EXISTING attempts functional.
    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id')->withTrashed();
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
