<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comments extends Model
{
    use SoftDeletes;
    //////////////////////////////////////////////
    protected $table = 'comments';
    protected $fillable = [
        'name', 'email', 'details','parent_id', 'news_id', 'status'
    ];
    protected $hidden = [];
    //////////////////////////////////////////////
    public function children()
    {
        return $this->hasMany('App\Models\Comments','parent_id','id');
    }
    //////////////////////////////////////////////
    public function parent()
    {
        return $this->belongsTo('App\Models\Comments','parent_id');
    }
    //////////////////////////////////////////////
    public function news()
    {
        return $this->belongsTo('App\Models\News','news_id');
    }
    //////////////////////////////////////////////
    function addComment($name, $email, $details, $parent_id, $news_id, $status)
    {
        $this->name = $name;
        $this->email = $email;
        $this->details = $details;
        $this->parent_id = $parent_id;
        $this->news_id = $news_id;
        $this->status = $status;

        $this->save();
        return $this;
    }
    //////////////////////////////////////////////
    function updateComment($obj, $name, $email, $details, $parent_id, $news_id, $status)
    {
        $obj->name = $name;
        $obj->email = $email;
        $obj->details = $details;
        $obj->parent_id = $parent_id;
        $obj->news_id = $news_id;
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
    function deleteComment($obj)
    {
        return $obj->delete();
    }
    //////////////////////////////////////////////
    function getComment($id)
    {
        return $this->find($id);
    }
    //////////////////////////////////////////////
    function getAllActiveComments()
    {
        return $this->where('status','=',1)->orderBy('id','desc')->get();
    }
    //////////////////////////////////////////////
    function getAllActiveCommentsByNews($news_id)
    {
        return $this->where('status','=',1)->where('news_id','=',$news_id)->orderBy('id','desc')->get();
    }
    //////////////////////////////////////////////
    function getActiveComment($id)
    {
        return $this->where('status','=',1)->where('id','=',$id)->first();
    }
    //////////////////////////////////////////////
    function getSearchComments($status = null,$start,$length)
    {
        return $this->where(function($query) use ($status) {
            if($status != "")
            {
                $query->where('status', '=', $status);
            }
        })->skip($start)->take($length)->get();
    }
}