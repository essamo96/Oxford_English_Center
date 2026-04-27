<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Times extends Model {

    use SoftDeletes;

    protected $table = 'times';
    protected $fillable = [
        'title', 'img', 'url', 'status', 'user_id'
    ];
    protected $hidden = [
        '',
    ];

    //////////////////////////////////////////////
    function addTime($days, $times, $status) {
        $this->days = $days;
        $this->times = $times;
        $this->status = $status;

        $this->save();
        return $this;
    }

    //////////////////////////////////////////////
    function updateTime($obj, $days, $times, $status) {
        $obj->days = $days;
        $obj->times = $times;
        $obj->status = $status;

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
    function getSearchTimes($title) {
        return $this->where(function($query) use ($title) {
                            if ($title != "") {
                                $query->where('days', 'LIKE', '%' . $title . '%');
                            }
                        })
                        ->orderBy('id', 'desc')
                        ->get();
    }

}
