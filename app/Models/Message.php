<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Message extends Model {

    protected $table = "messages";

//    public function fromUser() {
//        return $this->belongsTo('App\Models\Students', 'from_user');
//    }
//
//    public function toUser() {
//        return $this->belongsTo('App\Models\Students', 'to_user');
//    }

    public function chatGroup() {
        return $this->belongsTo('App\Models\Groups', 'group_id');
    }

    public function getLastMessages($group_id, $type) {
        return $this->selectRaw("*,(
                        CASE
                            WHEN user_type =0 THEN (select name FROM students where id=messages.from_user)
                            ELSE  (select name FROM teachers where id=messages.from_user)
                        END
                    ) as name,(
                        CASE
                            WHEN user_type =0 THEN (select image FROM students where id=messages.from_user)
                            ELSE  (select image FROM teachers where id=messages.from_user)
                        END
                    ) as image,(
                        CASE
                            WHEN user_type =0 THEN (select gender FROM students where id=messages.from_user)
                            ELSE NULL
                        END
                    ) as gender ")->where('group_id', $group_id)->orderBy('messages.created_at', 'desc')->limit(10)->get();
    }

}
