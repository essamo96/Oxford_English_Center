<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes, HasRoles;
    //////////////////////////////////////////////
    protected $table = 'users';
    protected $fillable = [
        'username', 'name', 'email', 'role', 'created_by', 'password', 'status', 'image', 'last_login_at'
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $guard_name = 'admin';

    //////////////////////////////////////////////
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'created_by');
    }

    //////////////////////////////////////////////
    function addUser($username, $name, $email, $role, $created_by, $password, $status, $image = null)
    {
        $this->username = $username;
        $this->name = $name;
        $this->email = $email;
        $this->role = $role;
        $this->created_by = $created_by;
        $this->password = $password;
        $this->status = $status;
        $this->image = $image;

        $this->save();
        return $this;
    }

    //////////////////////////////////////////////
    function updateUser($obj, $username, $name, $email, $role, $status, $image = null)
    {
        $obj->username = $username;
        $obj->name = $name;
        $obj->email = $email;
        $obj->role = $role;
        $obj->status = $status;
        if($image) $obj->image = $image;

        $obj->save();
        return $obj;
    }

    //////////////////////////////////////////////
    function updatePassword($id, $password)
    {
        return $this
            ->where('id', '=', $id)
            ->update([
                'password' => $password
            ]);
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
    function deleteUser($obj)
    {
        return $obj->delete();
    }

    //////////////////////////////////////////////
    function getUser($id)
    {
        return $this->find($id);
    }

    //////////////////////////////////////////////
    function getUsers($username = null, $name = null, $email = null, $status = null)
    {
        return $this->where(function ($query) use ($username, $name, $email, $status) {
            if ($username != "") {
                $query->where('username', 'LIKE', '%' . $username . '%');
            }
            if ($name != "") {
                $query->where('name', 'LIKE', '%' . $name . '%');
            }
            if ($email != "") {
                $query->where('email', 'LIKE', '%' . $email . '%');
            }
            if ($status !== null && $status !== "") {
                $query->where('status', (int)$status);
            }
        })->orderBy('id', 'desc')->get();
    }

}