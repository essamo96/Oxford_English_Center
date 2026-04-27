<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Closed_Classes extends Model
{
    use SoftDeletes;
    //////////////////////////////////////////////
    protected $table = 'closed_classes';
    protected $fillable = [
        'teacher_id', 'group_id', 'closed_date','seen'
    ];
    protected $hidden = [
        '',
    ];
    public function Teacher()
    {
        return $this->belongsTo('App\Models\Teachers', 'teacher_id')->withTrashed();
    }
    public function Groups()
    {
        return $this->belongsTo('App\Models\Groups', 'group_id')->withTrashed();
    }
    function deleteClosed_Classes($obj)
    {
        return $obj->delete();
    }
    //////////////////////////////////
    function getClosed_Classes($id)
    {
        return $this->find($id);
    }
    //////////////////////////////////
    function getCurrentClosed_Classes()
    {
        return $this->get();
    }
    //////////////////////////////////
    function countClosed_Classes()
    {
        return $this->count('id');
    }
    //////////////////////////////////
    function getAllClosed_Classes($name = null, $teacher_id = null, $closed_date = null)
    {
        return $this->with(['Teacher', 'Groups'])
            ->where(function ($query) use ($name) {
                if ($name != "") {
                    $query->whereHas('Teacher', function ($q) use ($name) {
                        $q->where('name', 'LIKE', '%' . $name . '%');
                    })->orWhereHas('Groups', function ($q) use ($name) {
                        $q->where('name', 'LIKE', '%' . $name . '%');
                    });
                }
            })
            ->where(function ($query) use ($teacher_id) {
                if ($teacher_id != "") {
                    $query->where('teacher_id', $teacher_id);
                }
            })
            ->where(function ($query) use ($closed_date) {
                if ($closed_date != "") {
                    $query->whereDate('closed_date', $closed_date);
                }
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
