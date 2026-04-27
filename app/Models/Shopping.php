<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Shopping extends Model {

    use SoftDeletes;

    protected $table = 'shopping_products';
    protected $fillable = [
        'title', 'img', 'url', 'status', 'user_id'
    ];
    protected $hidden = [
        '',
    ];

    //////////////////////////////////////////////
    function addShopping($title, $descs, $image, $category_id, $tags, $publish, $user_id) {

        $this->title = $title;
        $this->descs = $descs;
        $this->image = $image;
        $this->category_id = $category_id;
        $this->tags = $tags;
        $this->status = $publish;
        $this->user_id = $user_id;

        $this->save();
        return $this;
    }

    function updateShopping($obj, $title, $descs, $image, $category_id, $tags, $publish) {
        $obj->title = $title;
        $obj->descs = $descs;
        $obj->image = $image;
        $obj->category_id = $category_id;
        $obj->tags = $tags;
        $obj->status = $publish;


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
    function deleteShopping($obj) {
        return $obj->delete();
    }

    //////////////////////////////////////////////
    function getShopping($id) {
        return $this->find($id);
    }

    function getShoppings($start, $limit) {
        return $this
                        ->where('status', '=', 1)
                        ->orderBy('id', 'desc')
                        ->skip($start)
                        ->take($limit)
                        ->paginate($limit);
    }

    function getLastShoppings($start, $limit) {
        return $this
                        ->where('status', '=', 1)
                        ->orderBy('id', 'desc')
                        ->skip($start)
                        ->take($limit)
                        ->get();
    }

    function getCategoryShoppings($id) {
        return $this
                        ->where('status', '=', 1)
                        ->where('category_id', '=', $id)
                        ->orderBy('id', 'desc')
                        ->get();
    }

    //////////////////////////////////////////////
    function getLastShopping() {
        return $this
                        ->where('status', '=', 1)
                        ->orderBy('id', 'desc')
                        ->first();
    }

    //////////////////////////////////
    function getAllShoppings() {
        return $this->where('status', '=', 1)->get();
    }

//////////////////////////////////////////////
    function getSearchShopping($title) {
        return $this->where(function($query) use ($title) {
                            if ($title != "") {
                                $query->where('title', 'LIKE', '%' . $title . '%');
                            }
                        })
                        ->orderBy('id', 'desc')
                        ->get();
    }

}
