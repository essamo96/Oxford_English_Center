<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Evaluate_Items extends Model {

    use SoftDeletes;

    protected $table = 'evaluate_items';
    protected $fillable = [
        'name_en', 'status'
    ];
    protected $hidden = [
        '',
    ];

    //////////////////////////////////////////////
    // public function teacher() {
    //     return $this->belongsTo('App\Models\Teachers', 'teacher_id');
    // }

    // public function program() {
    //     return $this->belongsTo('App\Models\Programs', 'program_id');
    // }
    // public function program() {
    //     return $this->hasOne('App\Models\Programs', 'program_id');
    // }
    // public function ctime() {
    //     return $this->belongsTo('App\Models\Times', 'date_id');
    // }

    //////////////////////////////////////////////
    function addEvaluate_Items($name_en, $status) {
        $this->name_en = $name_en;
        $this->status = $status;
        $this->save();
        return $this;
    }

    //////////////////////////////////////////////
    function updateEvaluate_Items($obj,$name_en) {
        $obj->name_en = $name_en;
        // $obj->status = $status;
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
    function deleteEvaluate_Items($obj) {
        return $obj->delete();
    }

    //////////////////////////////////////////////
    function getEvaluate_Items($id) {
        return $this->find($id);
    }
    

    //////////////////////////////////
    function getAllEvaluate_Items() {
        return $this->get();
    }
    //////////////////////////////////
    function getAllActiveEvaluate_Items() {
        return $this->where('status',1)->whereNull('deleted_at')
        ->get();
    }

//////////////////////////////////////////////
    function getSearchEvaluate_Items($title = null) {
        return $this
                        // ->with('ctime')
                        ->where(function($query) use ($title) {
                            if ($title != "") {
                                $query->where('name_en', 'LIKE', '%' . $title . '%');
                            }
                        })
                        ->whereNull('deleted_at')->get();
    }

    function getEvaluate_ItemsByStatus($status) {
        return $this->where('status', $status)
                    ->get();
    }
    function countEvaluate_Items($status) {
        return $this->where('status', $status)
                        ->count();
    }

}
