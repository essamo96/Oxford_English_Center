<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupStudentsFees extends Model
{
    use SoftDeletes;

    protected $table = 'group_students_fees';

    protected $fillable = [
        'student_id',
        'student_fee_paid',
        'student_paid_type',
        'group_id',
        'program_id',
        'payment_receipt',
        'admin_verified_amount',
        'total_due_amount',
        'remaining_amount',
        'status',
        'audit_status',
        'verified_by',
        'notes',
        'payment_method_id',
        'transaction_type',
        'transaction_amount'
    ];

    public function scopeConfirmed($query)
    {
        return $query->where('audit_status', 'verified');
    }

    public function student()
    {
        return $this->belongsTo(Students::class, 'student_id');
    }

    public function group()
    {
        return $this->belongsTo(Groups::class, 'group_id');
    }

    public function program()
    {
        return $this->belongsTo(Programs::class, 'program_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethods::class, 'payment_method_id');
    }
}
