<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Partners extends Model {

    use SoftDeletes;

    protected $table = 'partner';
    protected $fillable = [
        'title', 'img', 'url', 'status', 'user_id'
    ];
    protected $hidden = [
        '',
    ];

    //////////////////////////////////////////////
    function addPartner($title, $descs, $image, $url, $status, $user_id) {
        $this->title = $title;
        $this->descs = $descs;
        $this->url = $url;
        $this->image = $image;
        $this->status = $status;
        $this->user_id = $user_id;

        $this->save();
        return $this;
    }

    //////////////////////////////////////////////
    function updatePartner($obj, $title, $descs, $image, $url, $status, $user_id) {
        $obj->title = $title;
        $obj->descs = $descs;
        $obj->url = $url;
        $obj->image = $image;
        $obj->status = $status;
        $obj->user_id = $user_id;

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
    function deletePartner($obj) {
        return $obj->delete();
    }

    //////////////////////////////////////////////
    function getPartner($id) {
        return $this->find($id);
    }

    function getPartners($start, $limit) {
        return $this
                        ->where('status', '=', 1)
                        ->orderBy('id', 'desc')
                        ->skip($start)
                        ->take($limit)
                        ->paginate($limit);
    }

    function getLastPartners($start, $limit, $type) {
        return $this
                        ->where('status', '=', 1)
                        ->where('user_id', '=', $type)
                        ->orderBy('id', 'desc')
                        ->skip($start)
                        ->take($limit)
                        ->get();
    }

    //////////////////////////////////////////////
    function getLastPartner() {
        return $this
                        ->where('status', '=', 1)
                        ->orderBy('id', 'desc')
                        ->first();
    }

    //////////////////////////////////
    function getAllPartners($type) {
        return $this
                        ->where('status', '=', 1)
                        ->where('user_id', '=', $type)
                        ->get();
    }

//////////////////////////////////////////////
    function getSearchPartners($title) {
        return $this->where(function($query) use ($title) {
                            if ($title != "") {
                                $query->where('title', 'LIKE', '%' . $title . '%');
                            }
                        })
                        ->orderBy('id', 'desc')
                        ->get();
    }

}
