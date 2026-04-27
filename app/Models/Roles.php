<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Roles extends Model
{
    use SoftDeletes;
    //////////////////////////////////////////////
    protected $table = 'roles';
    protected $fillable = ['name', 'status'];
    // protected $hidden = [
    //     '',
    // ];
    //////////////////////////////////////////////
    public function admin()
    {
        return $this->hasMany('App\Models\Admin');
    }

    //////////////////////////////////////////////
    public function permissions()
    {
        return $this->belongsToMany('App\Models\Permissions', 'role_has_permissions', 'role_id', 'permission_id');
    }
    //////////////////////////////////////////////
    function addRole($name, $status)
    {
        $this->name = $name;
        $this->status = $status;
        // $this->is_user = $is_user;

        $this->save();
        return $this;
    }
    //////////////////////////////////////////////
    function updateRole($obj, $name, $status)
    {
        $obj->name = $name;
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
    function deleteRole($obj)
    {
        return $obj->delete();
    }
    //////////////////////////////////////////////
    function getRole($id)
    {
        return $this->find($id);
    }
    //////////////////////////////////////////////
    function getAllRolesActive()
    {
        return $this->where('status', '=', 1)->whereNull('deleted_at')->get();
    }
    //////////////////////////////////////////////
    function getRoles($name = null)
    {
        return $this->with('permissions')->where(function ($query) use ($name) {
            if ($name != "") {
                $query->where('name', 'LIKE', '%' . $name . '%');
            }
        })->get();
    }
}
