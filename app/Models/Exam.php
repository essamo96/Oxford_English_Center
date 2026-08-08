<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Scopes\BranchScope;

class Exam extends Model
{
    use SoftDeletes;

    protected $table = 'exams';

    protected $fillable = [
        'branch_id', 'category', 'title', 'description', 'program_id', 'group_id', 'teacher_id',
        'duration_minutes', 'max_attempts', 'passing_score', 'start_date', 'end_date', 'status',
        'shuffle_questions', 'shuffle_answers', 'generation_rules', 'review_available', 'result_visibility',
        'anti_cheat_enabled', 'anti_cheat_violation_limit', 'anti_cheat_action',
        'created_by_type', 'created_by_id',
    ];

    protected $casts = [
        'generation_rules' => 'array',
        'shuffle_questions' => 'boolean',
        'shuffle_answers' => 'boolean',
        'review_available' => 'boolean',
        'anti_cheat_enabled' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new BranchScope());
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function program()
    {
        return $this->belongsTo(Programs::class, 'program_id');
    }

    public function group()
    {
        return $this->belongsTo(Groups::class, 'group_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teachers::class, 'teacher_id');
    }

    public function questions()
    {
        return $this->belongsToMany(ExamQuestion::class, 'exam_exam_question', 'exam_id', 'question_id')
            ->withPivot(['sort_order', 'marks_override'])
            ->orderBy('exam_exam_question.sort_order');
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class, 'exam_id');
    }

    public function scopeForTeacher($query, $teacherId)
    {
        return $query->where('category', 'group')->where('teacher_id', $teacherId);
    }

    public function scopePlacement($query)
    {
        return $query->where('category', 'placement');
    }

    // Flips due Group Exams from 'scheduled' to 'published' and fires the student
    // notification for each. Shared by the `exams:publish-scheduled` cron command AND a
    // lazy fallback called from student/teacher/admin exam listings, so a scheduled exam
    // still goes live (and notifies) even on a server where the cron job isn't configured yet.
    public static function publishDueScheduled()
    {
        $exams = static::where('category', 'group')
            ->where('status', 'scheduled')
            ->whereNotNull('start_date')
            ->where('start_date', '<=', now())
            ->get();

        foreach ($exams as $exam) {
            $exam->update(['status' => 'published']);
            \App\Http\Controllers\ExamNotifier::notifyGroupExamPublished($exam);
        }

        return $exams;
    }
}
