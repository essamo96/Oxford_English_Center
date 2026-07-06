<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentCompoPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_compo_id',
        'admin_id',
        'payer_name',
        'amount',
        'currency',
        'payment_method',
        'receipt_path',
    ];

    public function studentCompo()
    {
        return $this->belongsTo(StudentCompo::class, 'student_compo_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id'); // Using User model for admins table as fallback, assuming admin uses same model or we just rely on ID
    }
}
