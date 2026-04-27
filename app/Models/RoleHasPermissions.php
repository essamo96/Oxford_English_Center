<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleHasPermissions extends Model
{
    protected $table = 'role_has_permissions';
    protected $fillable = [
        'permission_id', 'role_id',
    ];
    protected $hidden = [
        '',
    ];
    public $timestamps = false;
    //////////////////////////////////////////////
    function addRoleHasPermissions($permission_id, $role_id)
    {
        $this->permission_id = $permission_id;
        $this->role_id = $role_id;

        $this->save();
        return $this;
    }
    //////////////////////////////////////////////
    function updateRoleHasPermissions($obj, $permission_id, $role_id)
    {
        $obj->permission_id = $permission_id;
        $obj->role_id = $role_id;

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
    function deleteRoleHasPermissions($obj)
    {
        return $obj->delete();
    }
    //////////////////////////////////////////////
    function deleteRoleHasPermissionsByRoleId($role_id)
    {
        return $this->where('role_id',$role_id)->delete();
    }
    //////////////////////////////////////////////
    function getRoleHasPermissions($id)
    {
        return $this->find($id);
    }
    //////////////////////////////////////////////
    function getRoleHasPermissionsByRoleId($id)
    {
        return $this->where('role_id', $id)->get()->toArray();
    }
    //////////////////////////////////////////////
    function getRolesHasPermissions($name = null)
    {
        return $this->where(function($query) use ($name) {
            if($name != "")
            {
                $query->where('name','LIKE','%'.$name.'%');
            }
        })->get();
    }
}
