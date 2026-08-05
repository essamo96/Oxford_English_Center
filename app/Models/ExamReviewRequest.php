<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamReviewRequest extends Model
{
    protected $table = 'exam_review_requests';

    protected $fillable = [
        'attempt_id', 'student_id', 'message', 'status', 'teacher_comment', 'reviewed_by_id', 'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }

    public function student()
    {
        return $this->belongsTo(Students::class, 'student_id');
    }
}
