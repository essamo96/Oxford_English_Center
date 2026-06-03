<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeacherSalaryForm extends Model
{
    use SoftDeletes;

    protected $table = 'teacher_salary_forms';

    protected $fillable = [
        'teacher_id', 'year', 'month', 'lectures_count', 'lecture_rate',
        'gross_amount', 'bonus', 'deduction', 'net_amount', 'status',
        'notes', 'closed_by', 'closed_at',
        'is_received', 'received_at', 'received_by',
    ];

    protected $casts = [
        'closed_at'   => 'datetime',
        'received_at' => 'datetime',
        'is_received' => 'boolean',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teachers::class, 'teacher_id');
    }

    public function details()
    {
        return $this->hasMany(TeacherSalaryDetail::class, 'salary_form_id');
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    /** Recompute net from gross + bonus - deduction. */
    public function recalcNet(): void
    {
        $this->net_amount = round((float) $this->gross_amount + (float) $this->bonus - (float) $this->deduction, 2);
    }
}
