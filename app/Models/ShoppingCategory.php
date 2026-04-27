<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class ShoppingCategory extends Model {

    use SoftDeletes;

    protected $table = 'shopping_categories';
    protected $fillable = [
        'title', 'img', 'url', 'status', 'user_id'
    ];
    protected $hidden = [
        '',
    ];

    //////////////////////////////////////////////
    function addCategories($name, $sort, $tags, $descs, $status, $in_menu) {
        $this->name = $name;
        $this->sort = $sort;
        $this->tags = $tags;
        $this->descs = $descs;
        $this->status = $status;
        $this->in_menu = $in_menu;


        $this->save();
        return $this;
    }

    //////////////////////////////////////////////
    function updateCategories($obj, $name, $sort, $tags, $descs, $status, $in_menu) {
        $obj->name = $name;
        $obj->sort = $sort;
        $obj->tags = $tags;
        $obj->descs = $descs;
        $obj->status = $status;
        $obj->in_menu = $in_menu;

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
    function deleteCategories($obj) {
        return $obj->delete();
    }

    //////////////////////////////////////////////
    function getCategories($id) {
        return $this->find($id);
    }

    function getCategoriesBySlug($slug) {
        return $this
                        ->where('status', '=', 1)
                        ->where('name', '=', $slug)
                        ->first();
    }

    function getActiveCategories() {
        return $this->where('status', '=', 1)->get();
    }

//////////////////////////////////////////////
    function getSearchCategory($title) {
        return $this->where(function($query) use ($title) {
                            if ($title != "") {
                                $query->where('name', 'LIKE', '%' . $title . '%');
                            }
                        })
                        ->orderBy('id', 'desc')
                        ->get();
    }

}
