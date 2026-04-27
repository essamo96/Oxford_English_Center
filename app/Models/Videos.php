<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Videos extends Model {

    use SoftDeletes;

    protected $table = 'vedio';
    protected $fillable = [
        'title', 'img', 'url', 'status', 'user_id'
    ];
    protected $hidden = [
        '',
    ];

    //////////////////////////////////////////////
    function addVideo($title, $url, $status, $user_id) {
        $this->title = $title;
        $this->url = $url;
        $this->status = $status;
        $this->user_id = $user_id;

        $this->save();
        return $this;
    }

    //////////////////////////////////////////////
    function updateVideo($obj, $title, $url, $status) {
        $obj->title = $title;
        $obj->url = $url;
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
    function deleteVideo($obj) {
        return $obj->delete();
    }

    //////////////////////////////////////////////
    function getVideo($id) {
        return $this->find($id);
    }

    function getVideos($start, $limit) {
        return $this
                        ->where('status', '=', 1)
                        ->orderBy('id', 'desc')
                        ->skip($start)
                        ->take($limit)
                        ->paginate($limit);
    }

    function getLastVideos($start, $limit) {
        return $this
                        ->where('status', '=', 1)
                        ->orderBy('id', 'desc')
                        ->skip($start)
                        ->take($limit)
                        ->get();
    }

    //////////////////////////////////////////////
    function getLastVideo() {
        return $this
                        ->where('status', '=', 1)
                        ->orderBy('id', 'desc')
                        ->first();
    }

    //////////////////////////////////
    function getAllVideos() {
        return $this->get();
    }

//////////////////////////////////////////////
    function getSearchVideos($title) {
        return $this->where(function($query) use ($title) {
                            if ($title != "") {
                                $query->where('title', 'LIKE', '%' . $title . '%');
                            }
                        })
                        ->orderBy('id', 'desc')
                        ->get();
    }

}
