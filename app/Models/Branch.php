<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use SoftDeletes;

    protected $table = 'branches';

    protected $fillable = [
        'name_ar', 'name_en', 'status',
    ];

    public function students()
    {
        return $this->hasMany(Students::class, 'branch_id');
    }

    public function getAll()
    {
        return $this->where('status', 1)->orderBy('name_ar')->get();
    }

    public function getSearch($name, $status = null)
    {
        $q = $this->withCount('students');
        if ($name) {
            $q->where(function ($query) use ($name) {
                $query->where('name_ar', 'LIKE', '%' . $name . '%')
                      ->orWhere('name_en', 'LIKE', '%' . $name . '%');
            });
        }
        if ($status !== null && $status !== '' && $status !== 'all') {
            $q->where('status', $status);
        }
        return $q->orderBy('id', 'desc')->get();
    }

    public function getBranch($id)
    {
        return $this->find($id);
    }

    public function addBranch($name_ar, $name_en, $status)
    {
        $this->name_ar = $name_ar;
        $this->name_en = $name_en;
        $this->status  = $status;
        $this->save();
        return $this;
    }

    public function updateBranch($obj, $name_ar, $name_en, $status)
    {
        $obj->name_ar = $name_ar;
        $obj->name_en = $name_en;
        $obj->status  = $status;
        $obj->save();
        return $obj;
    }

    public function updateStatus($id, $status)
    {
        return $this->where('id', $id)->update(['status' => $status]);
    }

    public function deleteBranch($obj)
    {
        return $obj->delete();
    }
}
