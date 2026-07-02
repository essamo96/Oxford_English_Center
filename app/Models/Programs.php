<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Programs extends Model {

    use SoftDeletes;

    protected $table = 'programs';
    protected $fillable = [
        'title', 'short', 'exam', 'status', 'image',
        'min_payment_percent', 'min_payment_fixed',
        'registration_start', 'registration_end',
        'program_type', 'is_placement_test',
    ];

    /**
     * Compute the minimum acceptable first payment for this program against a given total.
     * Uses the higher of (total * percent / 100) and the fixed amount; capped at total.
     */
    public function computeMinimumDue(float $totalDue): float
    {
        $pct   = $this->min_payment_percent !== null ? (float) $this->min_payment_percent : 0.0;
        $fixed = $this->min_payment_fixed   !== null ? (float) $this->min_payment_fixed   : 0.0;
        $byPct = $pct > 0 ? ($totalDue * $pct / 100.0) : 0.0;
        $min   = max($byPct, $fixed);
        return min($min, $totalDue);
    }
    protected $hidden = [
        '',
    ];
    public function grope() {
        return $this->hasMany('App\Models\Groups', 'program_id');
    }

    public function groupStudents() {
        return $this->hasManyThrough('App\Models\GroupStudents', 'App\Models\Groups', 'program_id', 'group_id');
    }

     //////////////////////////////////////////////
    public function file() {
        return $this->hasOne('App\Models\Files', 'program_id');
    }
    //////////////////////////////////////////////
    function addProgram($title, $short, $exam, $status, $image) {
        $this->title = $title;
        $this->short = $short;
        $this->exam = $exam;
        $this->status = $status;
        $this->image = $image;
        $this->save();
        return $this;
    }

    //////////////////////////////////////////////
    function updateProgram($obj, $title, $short, $exam, $status, $image) {
        $obj->title = $title;
        $obj->short = $short;
        $obj->exam = $exam;
        $obj->status = $status;
        $obj->image = $image;
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
    function deleteProgram($obj) {
        return $obj->delete();
    }

    //////////////////////////////////////////////
    function getProgram($id) {
        return $this->find($id);
    }

    //////////////////////////////////
    function getAllPrograms() {
        return $this->where('status', '=', 1)->get();
    }

//////////////////////////////////////////////
    function getSearchPrograms($title, $status = null, $group_name = null) {
        return $this->withCount(['groupStudents as students_count' => function($query) {
                            $query->whereHas('student', function($q) {
                                $q->where('status', 1)->where('delaying', 0);
                            });
                        }])
                        ->where(function($query) use ($title, $status, $group_name) {
                            if ($title != "") {
                                $query->where('title', 'LIKE', '%' . $title . '%');
                            }
                            if ($status !== null && $status !== '' && $status !== 'all') {
                                $query->where('status', $status);
                            }
                            if ($group_name != "") {
                                $query->whereHas('grope', function($q) use ($group_name) {
                                    $q->where('name', 'LIKE', '%' . $group_name . '%');
                                });
                            }
                        })
                        ->orderBy('id', 'desc')
                        ->get();
    }

}
