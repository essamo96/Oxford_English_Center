<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Settings extends Model {

    use SoftDeletes;

    protected $table = 'settings';
    protected $fillable = [
        'title', 'description', 'logo', 'tags', 'email', 'email_password', 'smtp_host', 'smtp_port', 'smtp_timeout', 'smtp_crypto', 'contact_email',
    ];

    //////////////////////////////////
    function updateSettings($obj, $title, $description, $more_desc, $tags, $mobile, $address, $donars, $clients, $happy, $tickects, $contact_email) {
        $obj->title = $title;
        $obj->description = $description;
        $obj->more_desc = $more_desc;
        $obj->tags = $tags;
        $obj->mobile = $mobile;
        $obj->address = $address;
        $obj->donars = $donars;
        $obj->clients = $clients;
        $obj->happy = $happy;
        $obj->tickects = $tickects;
        $obj->contact_email = $contact_email;
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
    function getSetting($id) {
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

    //////////////////////////////////////////////
    function getPages($page = null) {
        return $this->where(function($query) use ($page) {
                    if ($page != "") {
                        $query->where('title', 'LIKE', '%' . $page . '%');
                    }
                })->get();
    }

}
