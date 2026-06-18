<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsArchive extends Model
{
    protected $table = 'sms_archives';
    
    protected $fillable = [
        'student_id',
        'receiver_name',
        'mobile',
        'message',
        'status',
        'error_message',
        'sender_id',
        'group_id',
        'program_id'
    ];

    public function student()
    {
        return $this->belongsTo(Students::class, 'student_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function group()
    {
        return $this->belongsTo(Groups::class, 'group_id');
    }

    public function program()
    {
        return $this->belongsTo(Programs::class, 'program_id');
    }
}
