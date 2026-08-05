<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamQuestionOption extends Model
{
    protected $table = 'exam_question_options';

    protected $fillable = ['question_id', 'option_text', 'is_correct', 'sort_order'];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function question()
    {
        return $this->belongsTo(ExamQuestion::class, 'question_id');
    }
}
