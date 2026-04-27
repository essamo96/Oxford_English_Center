<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Socials extends Model
{
    use SoftDeletes;
    //////////////////////////////////////////////
    protected $table = 'socials';
    protected $fillable = [
        'name', 'link', 'icon', 'status',
    ];
    protected $hidden = [
        '',
    ];
    //////////////////////////////////
    function addSocial($name, $link, $icon, $status)
    {
        $this->name = $name;
        $this->link = $link;
        $this->icon = $icon;
        $this->status = $status;

        $this->save();
        return $this;
    }
    //////////////////////////////////
    function updateSocial($obj, $link, $icon, $status)
    {
        $obj->link = $link;
        $obj->icon = $icon;
        $obj->status = $status;

        return $obj->save();
    }
    //////////////////////////////////
    function updateStatus($id, $status)
    {
        return $this
            ->where('id', '=', $id)
            ->update([
                'status' => $status
            ]);
    }
    //////////////////////////////////
    function deleteSocial($obj)
    {
        return $obj->delete();
    }
    //////////////////////////////////
    function getSocial($id)
    {
        return $this->find($id);
    }
    //////////////////////////////////
    function getAllSocial()
    {
        return $this::all();
    }
    //////////////////////////////////
    function getAllSocialActive()
    {
        return $this->where('status', '=', 1)->get();
    }
    //////////////////////////////////
    function countSocial()
    {
        return $this->count();
    }
}
