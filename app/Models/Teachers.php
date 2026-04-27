<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Teachers extends Authenticatable {

    use Notifiable,
        SoftDeletes,
        HasRoles;

    protected $table = 'teachers';
    protected $fillable = [
        'title', 'img', 'url', 'status', 'user_id', 'image', 'evaluations'
    ];
    protected $hidden = [
        '',
    ];
    // public function groups()
    // {
    //     return $this->belongsTo('App\Models\Groups', 'id', 'teacher_id');
    // }
    //////////////////////////////////////////////
    function addTeacher($name, $username, $password, $mobile, $dob, $email, $join_date, $cv, $status, $image) {
        $this->name = $name;
        $this->username = $username;
        $this->password = $password;
        $this->mobile = $mobile;
        $this->dob = $dob;
        $this->email = $email;
        $this->join_date = $join_date;
        $this->cv = $cv;
        $this->image = $image;
        $this->status = $status;
        $this->save();
        return $this;
    }

    //////////////////////////////////////////////
    function updateTeacher($obj, $name, $username ,$password, $mobile, $dob, $email, $join_date, $cv, $status, $image) {
        $obj->name = $name;
        $obj->username = $username;
        $obj->password = $password;
        $obj->mobile = $mobile;
        $obj->dob = $dob;
        $obj->email = $email;
        $obj->join_date = $join_date;
        $obj->cv = $cv;
        $obj->image = $image;
        $obj->status = $status;

        $obj->save();
        return $obj;
    }
    
    ////////////////////////////////////////////// 
    function updateTeacherFrontEnd($obj, $dob, $email) {
        $obj->dob = $dob;
        $obj->email = $email;

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
    function updateEvaluations($id, $evaluations) {
        return $this
                        ->where('id', '=', $id)
                        ->update([
                         'evaluations' => $evaluations
        ]);
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
    function updateImage($id, $image)
    {
        return $this
            ->where('id', '=', $id)
            ->update([
                'image' => $image
            ]);
    }
    //////////////////////////////////////////////
    function deleteTeacher($obj) {
        return $obj->delete();
    }

    //////////////////////////////////////////////
    function getTeacher($id) {
        return $this->find($id);
    }

    function getAllTeachers() {
        return $this->where('status', '=', 1)->get();
    }

//////////////////////////////////////////////
    //////////////////////////////////////////////
    function getSearchTeachers($title,$activeT) {
        return $this
        ->where(function($query) use ($title) {
                            if ($title != "") {
                                $query->where('name', 'LIKE', '%' . $title . '%');
                            }
                        })
        ->where(function($query) use ($activeT) {
                            if ($activeT != "") {
                                $query->where('status', $activeT);
                            }
                        })
                        ->orderBy('id', 'desc')
                        ->get();
    }

    public function messages()
    {
        return $this->hasMany('App\Models\Teachers_Admin_Messages', 'teacher_id', 'id');
    }
}
