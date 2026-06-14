<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentPaymentSubmission extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'student_id', 'group_id', 'program_id',
        'amount_paid', 'receipt_file', 'payment_method_id', 'fee_id', 'notes',
        'status', 'admin_notes', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'amount_paid' => 'decimal:2',
    ];

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

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethods::class, 'payment_method_id');
    }

    public function fee()
    {
        return $this->belongsTo(GroupStudentsFees::class, 'fee_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForBranch($query, ?int $branchId)
    {
        if ($branchId) {
            $query->whereHas('student', fn($q) => $q->where('branch_id', $branchId));
        }
        return $query;
    }

    public function getReceiptUrlAttribute(): ?string
    {
        return $this->receipt_file ? asset('uploads/' . $this->receipt_file) : null;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'بانتظار المراجعة',
            'approved' => 'مقبول',
            'rejected' => 'مرفوض',
            default    => $this->status,
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            default    => 'secondary',
        };
    }
}
