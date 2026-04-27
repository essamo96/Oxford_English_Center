<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Groups extends Model {

    use SoftDeletes;

    protected $table = 'groups';
    protected $fillable = [
        'title', 'descs', 'image', 'status', 'user_id', 'start_date', 'end_date', 'zoom', 'image', 'drive', 'progress_at'
    ];
    protected $hidden = [
        '',
    ];

    //////////////////////////////////////////////
    public function teacher() {
        return $this->belongsTo('App\Models\Teachers', 'teacher_id');
    }

    public function program() {
        return $this->belongsTo('App\Models\Programs', 'program_id');
    }
    // public function program() {
    //     return $this->hasOne('App\Models\Programs', 'program_id');
    // }
    public function ctime() {
        return $this->belongsTo('App\Models\Times', 'date_id');
    }
    public function exam_dates()
    {
        return $this->hasOne('App\Models\GroupExamDates', 'id', 'group_id');
    }
    //////////////////////////////////////////////
    function addGroup($name, $program_id, $teacher_id, $date_id, $start_date, $end_date, $subjects, $status,$zoom, $image, $drive) {
        $this->name = $name;
        $this->program_id = $program_id;
        $this->teacher_id = $teacher_id;
        $this->date_id = $date_id;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->subjects = $subjects;
        $this->status = $status;
        $this->zoom = $zoom;
        $this->image = $image;
        $this->drive = $drive;
        $this->save();
        return $this;
    }

    //////////////////////////////////////////////
    function updateGroup($obj, $name, $program_id, $teacher_id, $date_id, $start_date, $end_date, $subjects, $status,$zoom, $image, $drive) {
        $obj->name = $name;
        $obj->program_id = $program_id;
        $obj->teacher_id = $teacher_id;
        $obj->date_id = $date_id;
        $obj->start_date = $start_date;
        $obj->end_date = $end_date;
        $obj->subjects = $subjects;
        $obj->status = $status;
        $obj->zoom = $zoom;
        $obj->image = $image;
        $obj->drive = $drive;
        $obj->save();
        return $obj;
    }
    //////////////////////////////////////////////
    function updateGroupTeacherLib($obj,$teacher_lib) {
        $obj->teacher_lib = $teacher_lib;
        $obj->save();
        return $obj;
    }
    //////////////////////////////////////
    function getGroupteacher($teacher_id)
    {
        return $this->where('teacher_id', $teacher_id)
            ->orderBy('id', 'desc')
            ->whereNull('deleted_at')->pluck('id')->all();}
    //////////////////////////////////////////////
    function updateStatus($id, $status) {
        return $this
                        ->where('id', '=', $id)
                        ->update([
                            'status' => $status
        ]);
    }
    //////////////////////////////////////////////
    function updateCode($id,$code,$code_scope) {
        return $this
                        ->where('id', '=', $id)
                        ->update([
                            'code' => $code,
                            'code_scope' => $code_scope,
        ]);
    }

    //////////////////////////////////////////////
    function deleteGroup($obj) {
        return $obj->delete();
    }

    //////////////////////////////////////////////
    function getGroup($id) {
        return $this->find($id);
    }
    

    //////////////////////////////////
    function getAllGroups() {
        return $this->get();
    }
    //////////////////////////////////
    function getAllActiveGroups() {
        return $this->where('status',1)->whereNull('deleted_at')
        ->get();
    }
    //////////////////////////////////
    function getAllActiveGroupsExcept($id) {
        return $this
        ->whereIn('id','!=', $id)
        ->where('status',1)
        ->whereNull('deleted_at')->get();
    }

//////////////////////////////////////////////
    function getSearchGroups($title, $program_id, $activeG, $teacher_id = null, $student_name = null, $is_today = null, $date_from = null, $date_to = null, $date_id = null) {
        $query = $this->with(['ctime', 'teacher', 'program']);

        if ($title) {
            $query->where('name', 'LIKE', '%' . $title . '%');
        }

        if ($program_id) {
            $query->where('program_id', $program_id);
        }

        if ($activeG !== null && $activeG !== "") {
            $query->where('status', $activeG);
        }

        if ($teacher_id) {
            $query->where('teacher_id', $teacher_id);
        }

        if ($date_id) {
            $query->where('date_id', $date_id);
        }

        if ($student_name) {
            $query->whereHas('students', function($q) use ($student_name) {
                $q->where(function($qq) use ($student_name) {
                    $qq->where('students.name', 'LIKE', '%' . $student_name . '%')
                       ->orWhere('students.mobile', 'LIKE', '%' . $student_name . '%');
                });
            });
        }


        if ($is_today) {
            $dayNameMap = [
                'Sunday' => 'الأحد', 'Monday' => 'الاثنين', 'Tuesday' => 'الثلاثاء',
                'Wednesday' => 'الأربعاء', 'Thursday' => 'الخميس', 'Friday' => 'الجمعة', 'Saturday' => 'السبت'
            ];
            $todayAr = $dayNameMap[date('l')];
            $query->whereHas('ctime', function($q) use ($todayAr) {
                $q->where('days', 'LIKE', '%' . $todayAr . '%');
            });
        }

        if ($date_from) {
            $query->where('start_date', '>=', $date_from);
        }

        if ($date_to) {
            $query->where('end_date', '<=', $date_to);
        }

        return $query->whereNull('deleted_at')->orderBy('id', 'desc')->get();
    }

    public function students() {
        return $this->belongsToMany('App\Models\Students', 'group_students', 'group_id', 'student_id');
    }

//////////////////////////////////////////////
    function Progress_list($teacher_name,$program_id ,$group_name) {
        return $this
                        ->with('ctime')
                        ->with('teacher')
                        ->with('program')
            ->whereHas('teacher', function ($query) use ($teacher_name) {
                if ($teacher_name != "") {
                    $query->where('name', 'LIKE', '%' . $teacher_name . '%');
                }
            })
                        ->where(function($query) use ($program_id) {
                            if ($program_id != "") {
                                $query->where('program_id',$program_id);
                            }
                        })
                        ->where(function($query) use ($group_name ) {
                            if ($group_name != "") {
                                $query->where('name', $group_name );
                            }
                        })
                        // ->where('status',1)
                        ->whereNull('deleted_at')->get();
    }
//////////////////////////////////////////////
    function getGropeByIdSearch($title,$program_id,$activeG) {
        return $this
                        ->with('ctime')
                        ->with('teacher')
                        ->with('program')
                        ->where(function($query) use ($title) {
                            if ($title != "") {
                                $query->where('name', 'LIKE', '%' . $title . '%');
                            }
                        })
                        ->where(function($query) use ($activeG ) {
                            if ($activeG  !== "" && $activeG !== 'all' && $activeG !== null) {
                                $query->where('status',$activeG);
                            }
                        })
                        ->where('program_id',$program_id)->whereNull('deleted_at')->get();
    }
//////////////////////////////////////////////
    function getSearchProgramGroups($title,$program_id) {
        return $this
                        ->with('ctime')
                        ->with('teacher')
                        ->with('program')
                        ->where(function($query) use ($title) {
                            if ($title != "") {
                                $query->where('name', 'LIKE', '%' . $title . '%');
                            }
                        })->where('status',1)->where('program_id' ,$program_id)->whereNull('deleted_at')->get();
    }
//////////////////////////////////////////////
    function getSearchEndGroups($title) {
        return $this
                        ->with('ctime')
                        ->with('teacher')
                        ->with('program')
                        ->where(function($query) use ($title) {
                            if ($title != "") {
                                $query->where('name', 'LIKE', '%' . $title . '%');
                            }
                        })->where('status',0)->
                        whereNull('deleted_at')->get();
    }
    function getGroupByTeacher($teacher_id) {
        return $this->where('teacher_id', $teacher_id)->where('status',1)
        ->whereNull('deleted_at')->get();
    }
    function countGroup($id) {
        return $this->where('program_id', $id)
                        ->count('id');
    }

}
