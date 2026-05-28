<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Times extends Model {

    use SoftDeletes;

    protected $table = 'times';
    protected $fillable = [
        'days', 'times', 'status', 'is_placement_test'
    ];

    protected $casts = [
        'is_placement_test' => 'boolean',
    ];
    protected $hidden = [
        '',
    ];

    //////////////////////////////////////////////
    function addTime($days, $times, $status, $is_placement_test = false) {
        $this->days = $days;
        $this->times = $times;
        $this->status = $status;
        $this->is_placement_test = (bool) $is_placement_test;

        $this->save();
        return $this;
    }

    //////////////////////////////////////////////
    function updateTime($obj, $days, $times, $status, $is_placement_test = false) {
        $obj->days = $days;
        $obj->times = $times;
        $obj->status = $status;
        $obj->is_placement_test = (bool) $is_placement_test;

        $obj->save();
        return $obj;
    }

    //////////////////////////////////////////////
    function updateStatus($id, $status) {
        return $this
                        ->where('id', '=', $id)
                        ->update([
                            'status' => $status
        ]);
    }

    //////////////////////////////////////////////
    function deleteTime($obj) {
        return $obj->delete();
    }

    //////////////////////////////////////////////
    function getTime($id) {
        return $this->find($id);
    }

    //////////////////////////////////
    function getAllTimes() {
        return $this->where('status', '=', 1)->get();
    }

//////////////////////////////////////////////
    function getSearchTimes($title, $is_placement_test = null) {
        return $this->where(function($query) use ($title) {
                            if ($title != "") {
                                $query->where('days', 'LIKE', '%' . $title . '%')
                                      ->orWhere('times', 'LIKE', '%' . $title . '%');
                            }
                        })
                        ->where(function($query) use ($is_placement_test) {
                            if ($is_placement_test !== null && $is_placement_test !== '') {
                                $query->where('is_placement_test', (int) $is_placement_test);
                            }
                        })
                        ->orderBy('id', 'desc')
                        ->get();
    }

}
