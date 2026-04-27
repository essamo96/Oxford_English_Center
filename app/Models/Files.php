<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Files extends Model {

    use SoftDeletes;

    protected $table = 'files';
    protected $fillable = [
        'title', 'descs', 'image', 'program_id', 'status'
    ];
    protected $hidden = [
        '',
    ];

    //////////////////////////////////////////////
    function addFile($title, $descs, $image, $program_id, $status) {
        $this->title = $title;
        $this->descs = $descs;
        $this->image = $image;
        $this->program_id = $program_id;
        $this->status = $status;
        $this->save();
        return $this;
    }

    //////////////////////////////////////////////
    function updateFile($obj, $title, $descs, $image, $program_id, $status) {
        $obj->title = $title;
        $obj->descs = $descs;
        $obj->image = $image;
        $obj->program_id = $program_id;
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
    function deleteFile($obj) {
        return $obj->delete();
    }

    //////////////////////////////////////////////
    function getFile($id) {
        return $this->find($id);
    }

    //////////////////////////////////////////////
    function getLastFile() {
        return $this
                        ->where('status', '=', 1)
                        ->orderBy('id', 'desc')
                        ->first();
    }

    function getFiles($start = 0, $limit = 25) {
        return $this
                        ->where('status', '=', 1)
                        ->orderBy('id', 'desc')
                        ->skip($start)
                        ->take($limit)
                        ->paginate($limit);
    }

    //////////////////////////////////
    function getAllFiles() {
        return $this->get();
    }

//////////////////////////////////////////////
    function getSearchFiles($title) {
        return $this->where(function($query) use ($title) {
                            if ($title != "") {
                                $query->where('title', 'LIKE', '%' . $title . '%');
                            }
                        })
                        ->get();
    }

}
