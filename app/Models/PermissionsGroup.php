<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionsGroup extends Model
{

    protected $table = 'permissions_group';
    protected $fillable = [
        'name', 'name_ar', 'name_en', 'icon', 'color', 'sort', 'status', 'parent_id',
    ];
    protected $hidden = [
        '',
    ];

    //////////////////////////////////////////////
    public function mychild()
    {
        return $this->hasMany('App\Models\PermissionsGroup', 'parent_id')->orderBy('sort', 'asc');
    }

    //////////////////////////////////////////////
    public function mychild2()
    {
        return $this->hasMany('App\Models\PermissionsGroup', 'parent_id', '');
    }

    //////////////////////////////////////////////
    public function permissions()
    {
        return $this->hasMany('App\Models\Permissions', 'group_id', 'id');
    }

    //////////////////////////////////////////////
    function addPermissionsGroup($name, $name_ar, $name_en, $icon, $sort, $status, $parent_id)
    {
        $this->name = $name;
        $this->name_ar = $name_ar;
        $this->name_en = $name_en;
        $this->icon = $icon;
        $this->sort = $sort;
        $this->status = $status;
        $this->parent_id = $parent_id;

        $this->save();
        return $this;
    }

    //////////////////////////////////////////////
    function updatePermissionsGroup($obj, $name, $name_ar, $name_en, $icon, $sort, $status, $parent_id)
    {
        $obj->name = $name;
        $obj->name_ar = $name_ar;
        $obj->name_en = $name_en;
        $obj->icon = $icon;
        $obj->sort = $sort;
        $obj->status = $status;
        $obj->parent_id = $parent_id;
        $obj->save();
        return $obj;
    }

    //////////////////////////////////////////////
    function deletePermissionsGroup($obj)
    {
        return $obj->delete();
    }

    //////////////////////////////////////////////
    function getPermissionsGroup($id)
    {
        return $this->find($id);
    }

    //////////////////////////////////////////////
    function getAllPermissionGroup()
    {
        return $this->where('status', 1)->orderBy('sort', 'asc')->get();
    }

    ////////////////////////////////////////
    function getAllParentPermissionGroup()
    {
        return $this->where('parent_id', 0)
            ->where('status', 1)
            ->where('name', '!=', 'dashboard')
            ->orderByRaw('COALESCE(sort, 9999) ASC')
            ->with(['mychild' => fn($q) => $q->where('status', 1)->orderBy('sort')])
            ->get();
    }

    //////////////////////////////////////////////
    function getAllPermissionGroupSearch($name = null)
    {
        return $this->where(function ($query) use ($name) {
            if ($name != "") {
                $query->where('name', 'LIKE', '%' . $name . '%');
            }
        })->where('parent_id', 0)->get();
    }

    function getPermissionGroupSearch($name = null)
    {
        return $this->where(function ($query) use ($name) {
            if ($name != "") {
                $query->where('name', 'LIKE', '%' . $name . '%');
            }
        })->orderBy('sort')->get();
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
}
