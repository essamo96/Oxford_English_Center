<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contacts extends Model
{
    use SoftDeletes;

    protected $table = 'contacts';

    protected $fillable = [
        'name', 'email', 'mobile', 'subject', 'message', 'status',
    ];
    ////////////////////////////////////
    function addContactUs($name, $email, $mobile, $subject, $message, $status)
    {
        $this->name = $name;
        $this->email = $email;
        $this->mobile = $mobile;
        $this->subject = $subject;
        $this->message = $message;
        $this->status = $status;

        $this->save();
        return $this;
    }
    //////////////////////////////////
    function updateContactUs($obj, $name, $email, $mobile, $subject, $message, $status)
    {
        $obj->name = $name;
        $obj->email = $email;
        $obj->mobile = $mobile;
        $obj->subject = $subject;
        $obj->message = $message;
        $obj->status = $status;

        $obj->save();
        return $obj;
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
    function deleteContactUs($obj)
    {
        return $obj->delete();
    }
    //////////////////////////////////
    function getContacts($id)
    {
        return $this->find($id);
    }
    //////////////////////////////////
    function getAllContactUs()
    {
        return $this->get();
    }
    //////////////////////////////////
    function countContactUs()
    {
        return $this->count('id');
    }
    //////////////////////////////////
    function getAllContactUsForAdmin($name,$email,$mobile)
    {
        return $this->where(function($query) use ($name,$email,$mobile)
        {
            if($name != "")
            {
                $query->where('name','LIKE', '%'.$name.'%');
            }
            //////////////////////////////
            if($email != "")
            {
                $query->where('email','LIKE', '%'.$email.'%');
            }
            //////////////////////////////
            if($mobile != "")
            {
                $query->where('mobile','LIKE', '%'.$mobile.'%');
            }
        })->get();
    }
}
