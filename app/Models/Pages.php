<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pages extends Model {

    use SoftDeletes;

    protected $table = 'pages';
    protected $fillable = [
        'title', 'details', 'image', 'tags', 'status',
    ];

    //////////////////////////////////
    function updatePage($obj, $title, $details, $image, $banner, $tags, $url, $status, $age, $level, $weeks, $hours, $mock, $duration, $class_size, $fees, $price, $start, $days, $time) {
        $obj->title = $title;
        $obj->details = $details;
        $obj->image = $image;
        $obj->banner = $banner;
        $obj->tags = $tags;
        $obj->url = $url;
        $obj->status = $status;
        $obj->age = $age;
        $obj->level = $level;
        $obj->weeks = $weeks;
        $obj->hours = $hours;
        $obj->mock = $mock;
        $obj->duration = $duration;
        $obj->class_size = $class_size;
        $obj->fees = $fees;
        $obj->price = $price;
        $obj->start = $start;
        $obj->days = $days;
        $obj->time = $time;
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

    //////////////////////////////////
    function getPage($id) {
        return $this->find($id);
    }

    //////////////////////////////////
    function getAllPages() {
        return $this->get();
    }

    //////////////////////////////////
    function getPageByName($name) {
        return $this->where('name', '=', $name)->first();
    }

    function getPageBySlug($slug) {
        return $this->where('slug', '=', $slug)->first();
    }

    //////////////////////////////////////////////
    function getPages($page = null) {
        return $this->where(function($query) use ($page) {
                    if ($page != "") {
                        $query->where('title', 'LIKE', '%' . $page . '%');
                    }
                })->get();
    }

}
