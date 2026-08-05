<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAttemptAnswer extends Model
{
    protected $table = 'exam_attempt_answers';

    protected $fillable = [
        'attempt_id', 'question_id', 'selected_option_id', 'answer_text', 'answer_audio_path',
        'is_correct', 'marks_awarded', 'teacher_comment', 'graded_by_id', 'graded_at',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'graded_at' => 'datetime',
    ];

    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(ExamQuestion::class, 'question_id');
    }

    public function selectedOption()
    {
        return $this->belongsTo(ExamQuestionOption::class, 'selected_option_id');
    }
}
