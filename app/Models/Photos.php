<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Photos extends Model {

    use SoftDeletes;

    protected $table = 'photo';
    protected $fillable = [
        'title', 'descs', 'image', 'status', 'user_id'
    ];
    protected $hidden = [
        '',
    ];

    public function images() {
        return $this->hasMany('App\Models\Images', 'album_id', 'id')->orderBy('feature', 'desc');
    }

    //////////////////////////////////////////////
    function addPhoto($title, $descs, $tags, $status, $user_id) {
        $this->title = $title;
        $this->descs = $descs;
        $this->tags = $tags;
        $this->status = $status;
        $this->user_id = $user_id;
        $this->save();
        return $this;
    }

    //////////////////////////////////////////////
    function updatePhoto($obj, $title, $descs, $tags, $status) {
        $obj->title = $title;
        $obj->descs = $descs;
        $obj->tags = $tags;
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

    function updateFeature($id, $status) {
        return $this
                        ->where('id', '=', $id)
                        ->update([
                            'feature' => $status
        ]);
    }

    //////////////////////////////////////////////
    function deletePhoto($obj) {
        return $obj->delete();
    }

    //////////////////////////////////////////////
    function getPhoto($id) {
        return $this->find($id);
    }

    //////////////////////////////////////////////
    function getLastPhoto() {
        return $this
                        ->where('status', '=', 1)
                        ->orderBy('id', 'desc')
                        ->first();
    }

    function getPhotos($start, $limit) {
        return $this
                        ->where('status', '=', 1)
                        ->orderBy('id', 'desc')
                        ->skip($start)
                        ->take($limit)
                        ->paginate($limit);
    }

    function getClients() {
        return $this->with('images')
                        ->where('status', '=', 1)
                        ->orderBy('id', 'desc')
                        ->get();
    }

    function getClient($slug) {
        return $this->with('images')
                        ->where('status', '=', 1)
                        ->where('slug', '=', $slug)
                        ->first();
    }

    //////////////////////////////////
    function getAllPhotos() {
        return $this->get();
    }

//////////////////////////////////////////////
    function getSearchPhotos($title) {
        return $this->with('images')->where(function($query) use ($title) {
                            if ($title != "") {
                                $query->where('title', 'LIKE', '%' . $title . '%');
                            }
                        })
                        ->get();
    }

}
