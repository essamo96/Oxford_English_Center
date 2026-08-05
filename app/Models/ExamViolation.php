<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamViolation extends Model
{
    protected $table = 'exam_violations';

    protected $fillable = ['attempt_id', 'type', 'occurred_at', 'meta'];

    protected $casts = [
        'occurred_at' => 'datetime',
        'meta' => 'array',
    ];

    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }
}
