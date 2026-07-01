<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentsCompoStudent extends Model
{
    protected $fillable = [
        'student_compo_id', 'parent_name', 'parent_phone', 'parent_email'
    ];

    public function student()
    {
        return $this->belongsTo(StudentCompo::class, 'student_compo_id');
    }
}
