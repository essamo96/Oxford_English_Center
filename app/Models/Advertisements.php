<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Advertisements extends Model
{
    use SoftDeletes;
    //////////////////////////////////////////////
    protected $table = 'advertisements';
    protected $fillable = [
        'name', 'image', 'type','expiry_date', 'url', 'status',
    ];
    protected $hidden = [];
    //////////////////////////////////////////////
    function addAdvertisement($name, $image, $type, $expiry_date, $url, $status)
    {
        $this->name = $name;
        $this->image = $image;
        $this->type = $type;
        $this->expiry_date = $expiry_date;
        $this->url = $url;
        $this->status = $status;

        $this->save();
        return $this;
    }
    //////////////////////////////////////////////
    function updateAdvertisement($obj, $name, $image, $type, $expiry_date, $url, $status)
    {
        $obj->name = $name;
        $obj->image = $image;
        $obj->type = $type;
        $obj->expiry_date = $expiry_date;
        $obj->url = $url;
        $obj->status = $status;

        $obj->save();
        return $obj;
    }
    //////////////////////////////////////////////
    function updateStatus($id, $status)
    {
        return $this
            ->where('id', '=', $id)
            ->update([
                'status' => $status
            ]);
    }
    //////////////////////////////////////////////
    function deleteAdvertisement($obj)
    {
        return $obj->delete();
    }
    //////////////////////////////////////////////
    function getAdvertisement($id)
    {
        return $this->find($id);
    }
    //////////////////////////////////////////////
    function getAllActiveAdvertisements()
    {
        return $this->where('status','=',1)->where('expiry_date','>=',date("Y-m-d"))->get();
    }
    //////////////////////////////////////////////
    function getActiveAdvertisementsByType($type)
    {
        return $this->where('status','=',1)->where('type','=',$type)->where('expiry_date','>=',date("Y-m-d"))->first();
    }
    //////////////////////////////////////////////
    function getActiveAdvertisement($advertisement_id)
    {
        return $this->where('status','=',1)->where('id','=',$advertisement_id)->first();
    }
    //////////////////////////////////////////////
    function getSearchAdvertisements($name = null)
    {
        return $this->where(function($query) use ($name) {
            if($name != "")
            {
                $query->where('name','LIKE','%'.$name.'%');
            }
        })->get();
    }
}