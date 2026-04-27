<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Images extends Model {

    use SoftDeletes;

    protected $table = 'photos_images';
    protected $fillable = [
        'title', 'descs', 'image', 'status', 'user_id'
    ];
    protected $hidden = [
        '',
    ];

    //////////////////////////////////////////////
    function addPhoto($filename, $id) {
        $this->image = $filename;
        $this->album_id = $id;
        $this->save();
        return $this;
    }

    //////////////////////////////////////////////
    function updatePhoto($obj, $title, $title_en, $category_id, $descs, $descs_en, $tags, $status) {
        $obj->title = $title;
        $obj->title_en = $title_en;
        $obj->category_id = $category_id;
        $obj->descs = $descs;
        $obj->descs_en = $descs_en;
        $obj->tags = $tags;
        $obj->status = $status;
        $obj->save();
        return $obj;
    }

    //////////////////////////////////////////////
    function deletePhoto($obj) {
        return $obj->delete();
    }

    //////////////////////////////////
    function getAllImages($album_id) {
        return $this->where('album_id', '=', $album_id)->get();
    }

    function getCoverImage($album_id) {
        return $this
                        ->where('album_id', '=', $album_id)
                        ->where('feature', '=', 1)
                        ->first();
    }

    function getImageByName($name) {
        return $this->where('image', '=', $name)->first();
    }

    function featureImage($id, $album_id) {
        $this
                ->where('album_id', '=', $album_id)
                ->update([
                    'feature' => 0
        ]);
        return $this
                        ->where('id', '=', $id)
                        ->update([
                            'feature' => 1
        ]);
    }

    function deleteImage($obj) {
        return $obj->delete();
    }

}
