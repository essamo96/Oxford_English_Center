<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Tenders extends Model {

    use SoftDeletes;

    protected $table = 'tenders';
    protected $fillable = [
        'title', 'img', 'url', 'status', 'user_id'
    ];
    protected $hidden = [
        '',
    ];

    //////////////////////////////////////////////
    function addTender($name, $mobile, $comapny, $email, $notes) {
        $this->name = $name;
        $this->mobile = $mobile;
        $this->comapny = $comapny;
        $this->email = $email;
        $this->notes = $notes;

        $this->save();
        return $this;
    }

    //////////////////////////////////////////////
    function updateTender($obj, $title, $descs, $image, $url, $status) {
        $obj->title = $title;
        $obj->descs = $descs;
        $obj->url = $url;
        $obj->image = $image;
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
    function deleteTender($obj) {
        return $obj->delete();
    }

    //////////////////////////////////////////////
    function getTender($id) {
        return $this->find($id);
    }

    function getTenders($start, $limit) {
        return $this
                        ->where('status', '=', 1)
                        ->orderBy('id', 'desc')
                        ->skip($start)
                        ->take($limit)
                        ->paginate($limit);
    }

    function getLastTenders($start, $limit) {
        return $this
                        ->where('status', '=', 1)
                        ->orderBy('id', 'desc')
                        ->skip($start)
                        ->take($limit)
                        ->get();
    }

    //////////////////////////////////////////////
    function getLastTender() {
        return $this
                        ->where('status', '=', 1)
                        ->orderBy('id', 'desc')
                        ->first();
    }

    //////////////////////////////////
    function getAllTenders() {
        return $this->where('status', '=', 1)->get();
    }

//////////////////////////////////////////////
    function getSearchTenders($title) {
        return $this->where(function($query) use ($title) {
                            if ($title != "") {
                                $query->where('name', 'LIKE', '%' . $title . '%');
                            }
                        })
                        ->orderBy('id', 'desc')
                        ->get();
    }

}
