<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class GroupExamDates extends Authenticatable {

    use Notifiable,
        SoftDeletes,
        HasRoles;

    protected $table = 'group_exam_dates';
    protected $fillable = ['progress_test1', 'progress_test2', 'progress_test3', 'final_exam', 'group_id'];
    public function group()
    {
        return $this->belongsTo('App\Models\Groups', 'group_id');
    }
    function getGroupExamDates($group_id) {
        return $this->where('group_id', '=', $group_id)
                        ->orderBy('id', 'desc')
                        ->first();
    }

}
