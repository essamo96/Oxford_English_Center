<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeacherLibrary extends Model {

    use SoftDeletes;

    protected $table = 'teacher_library';
    protected $fillable = [
        'title', 'group_id', 'teacher_id', 'url'
    ];
    protected $hidden = [
        '',
    ];
    
    public function teacher() {
        return $this->belongsTo('App\Models\Teachers', 'teacher_id');
    }
    public function group() {
        return $this->belongsTo('App\Models\GroupStudents', 'group_id');
    }
    
    //////////////////////////////////////////////
    function deleteTeacherLibrary($obj) {
        return $obj->delete();
    }

    //////////////////////////////////////////////
    function getTeacherLibrary($id) {
        return $this->find($id);
    }
    //////////////////////////////////////////////
    function getAllTeacherLibraries() {
        return $this->get();
    }
    //////////////////////////////////////////////
    function getTeacherLibrariesByGroup($group_id, $limit) {
        return $this->where('group_id', '=', $group_id)
                    ->orderBy('id', 'desc')
                    ->paginate($limit);
    }
    //////////////////////////////////////////////
}
