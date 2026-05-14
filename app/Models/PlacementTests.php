<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlacementTests extends Model
{
    use SoftDeletes;

    protected $table = 'placement_tests';

    protected $fillable = [
        'student_id',
        'test_date',
        'test_time',
        'preferred_days',
        'preferred_time',
        'status',
        'score',
        'assigned_level',
        'payment_receipt',
        'paid_amount',
        'total_amount',
        'remaining_amount',
        'payment_status',
        'payment_method_id',
    ];

    public function student()
    {
        return $this->belongsTo(Students::class, 'student_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethods::class, 'payment_method_id');
    }
}
