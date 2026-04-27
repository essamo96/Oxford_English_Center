<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Questions extends Model {

    use SoftDeletes;

    protected $table = 'questions';
    protected $fillable = [
        'name', 'status'
    ];
    protected $hidden = [
        '',
    ];

    //////////////////////////////////////////////
    // public function teacher() {
    //     return $this->belongsTo('App\Models\Teachers', 'teacher_id');
    // }

    //////////////////////////////////////////////
    function addQuestions($name, $status) {
        $this->name = $name;
        $this->status = $status;
        $this->save();
        return $this;
    }

    //////////////////////////////////////////////
    function updateQuestions($obj,$name) {
        $obj->name = $name;
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
    function deleteQuestions($obj) {
        return $obj->delete();
    }

    //////////////////////////////////////////////
    function getQuestions($id) {
        return $this->find($id);
    }
    

    //////////////////////////////////
    function getAllQuestions() {
        return $this->get();
    }
    //////////////////////////////////
    function getAllActiveQuestions() {
        return $this->where('status',1)->whereNull('deleted_at')
        ->get();
    }

//////////////////////////////////////////////
    function getSearchQuestions($title = null) {
        return $this
                        // ->with('ctime')
                        ->where(function($query) use ($title) {
                            if ($title != "") {
                                $query->where('name', 'LIKE', '%' . $title . '%');
                            }
                        })
                        ->whereNull('deleted_at')->get();
    }

    function getQuestionsByStatus($status) {
        return $this->where('status', $status)
                    ->get();
    }
    function countQuestions($status) {
        return $this->where('status', $status)
                        ->count();
    }

}
