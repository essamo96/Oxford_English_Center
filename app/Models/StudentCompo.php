<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentCompo extends Model
{
    protected $fillable = [
        'full_name_ar', 'full_name_en', 'phone', 'email', 'dob', 'gender',
        'address', 'branch', 'major_profession', 'health_issues', 'health_issues_details',
        'placement_test', 'placement_test_date', 'program_id', 'program_type', 'is_invoiced', 'is_read', 'is_contacted'
    ];

    public function parents()
    {
        return $this->hasMany(ParentsCompoStudent::class, 'student_compo_id');
    }

    public function program()
    {
        return $this->belongsTo(Programs::class, 'program_id');
    }

    public function payments()
    {
        return $this->hasMany(StudentCompoPayment::class, 'student_compo_id');
    }
}
