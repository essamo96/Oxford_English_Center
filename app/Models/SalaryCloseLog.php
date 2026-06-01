<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryCloseLog extends Model
{
    protected $table = 'salary_close_logs';

    protected $fillable = [
        'year', 'month', 'teachers_count', 'total_lectures', 'total_amount', 'closed_by', 'notes',
    ];

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }
}
