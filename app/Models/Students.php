<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Traits\HasRoles;
use Carbon\Carbon;

class Students extends Authenticatable
{

    use Notifiable,
        SoftDeletes,
        HasRoles;

    protected $table = 'students';
    protected $fillable = [
        'name',
        'name_en',
        'username',
        'password',
        'mobile',
        'dob',
        'job',
        'major',
        'current_level',
        'program_type',
        'requested_program_type',
        'enrollment_type',
        'parent_id',
        'email',
        'join_date',
        'exam_date',
        'exam_degree',
        'status',
        'delaying',
        'gender',
        'title',
        'img',
        'url',
        'user_id',
        'ask_update',
        'is_verified',
        'address',
        'health_conditions',
        'delay_cusess',
        'note'
    ];
    protected $hidden = [
        'password'
    ];
    public function messages()
    {
        return $this->hasMany('App\Models\Students_Admin_Messages', 'student_id', 'id');
    }
    public function gropes()
    {
        return $this->hasMany('App\Models\GroupStudents', 'student_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(Parents::class, 'parent_id');
    }

    public function placementTests()
    {
        return $this->hasMany(PlacementTests::class, 'student_id');
    }

    //////////////////////////////////////////////
    function addStudent($name, $name_en, $username, $password, $mobile, $dob, $job, $major, $current_level, $program_type, $parent_id, $email, $join_date, $exam_date, $exam_degree, $status, $delaying = 0, $gender)
    {
        try {
            $this->name = $name;
            $this->name_en = $name_en;
            $this->username = $username;
            $this->password = $password;
            $this->mobile = $mobile;
            $this->dob = $dob;
            $this->job = $job;
            $this->major = $major;
            $this->current_level = $current_level;
            $this->program_type = $program_type;
            $this->parent_id = $parent_id;
            $this->delaying = $delaying;
            $this->email = $email;
            $this->join_date = $join_date;
            $this->exam_date = $exam_date;
            $this->exam_degree = $exam_degree;
            $this->status = $status;
            $this->gender = $gender;
            return $this->save();
        } catch (\Exception $e) {
            Log::error('Error adding student: ' . $e->getMessage());
            return false;
        }
    }
    //////////////////////////////////////////////
    function addnotes($id, $note)
    {
        try {
            return DB::table('students')
                ->where('id', $id)
                ->update(['note' => $note]);
        } catch (\Exception $e) {
            Log::error('Error adding notes: ' . $e->getMessage());
            return false;
        }
    }
    //////////////////////////////////////////////
    function AddDelayCusess($id, $note)
    {
        try {
            return DB::table('students')
                ->where('id', $id)
                ->update(['delay_cusess' => $note]);
        } catch (\Exception $e) {
            Log::error('Error adding delay cause: ' . $e->getMessage());
            return false;
        }
    }

    //////////////////////////////////////////////
    function updateStudent($obj, $name, $username, $password, $mobile, $dob, $job, $email, $join_date, $exam_date, $exam_degree, $status)
    {
        try {
            $obj->name = $name;
            $obj->username = $username;
            $obj->password = $password;
            $obj->mobile = $mobile;
            $obj->dob = $dob;
            $obj->job = $job;
            $obj->email = $email;
            $obj->join_date = $join_date;
            $obj->exam_date = $exam_date;
            $obj->exam_degree = $exam_degree;
            $obj->status = $status;
            return $obj->save();
        } catch (\Exception $e) {
            Log::error('Error updating student: ' . $e->getMessage());
            return false;
        }
    }
    //////////////////////////////////////////////
    function getAllnewStudentsCount()
    {
        return $this->where('status', 0)->where('seen', 0)->whereNull('deleted_at')->count();
    }
    //////////////////////////////////////////////
    function updateStudentFrontEnd($obj, $dob, $job, $email, $name, $mobile)
    {
        try {
            $obj->dob = $dob;
            $obj->job = $job;
            $obj->email = $email;
            $obj->name = $name;
            $obj->mobile = $mobile;
            return $obj->save();
        } catch (\Exception $e) {
            Log::error('Error updating student from frontend: ' . $e->getMessage());
            return false;
        }
    }
    //////////////////////////////////////////////
    function updatePassword($id, $password)
    {
        try {
            return $this->where('id', '=', $id)
                ->update(['password' => $password]);
        } catch (\Exception $e) {
            Log::error('Error updating password: ' . $e->getMessage());
            return false;
        }
    }

    //////////////////////////////////////////////
    function updateImage($id, $image)
    {
        try {
            return $this->where('id', '=', $id)
                ->update(['image' => $image]);
        } catch (\Exception $e) {
            Log::error('Error updating image: ' . $e->getMessage());
            return false;
        }
    }

    //////////////////////////////////////////////
    function updateStatus($id, $status)
    {
        try {
            return $this->where('id', '=', $id)
                ->update(['status' => $status]);
        } catch (\Exception $e) {
            Log::error('Error updating status: ' . $e->getMessage());
            return false;
        }
    }

    //////////////////////////////////////////////
    function updateDailling($id, $delaying)
    {
        try {
            return $this->where('id', '=', $id)
                ->update(['delaying' => $delaying]);
        } catch (\Exception $e) {
            Log::error('Error updating delaying: ' . $e->getMessage());
            return false;
        }
    }

    //////////////////////////////////////////////
    function deleteStudent($obj)
    {
        try {
            return $obj->delete();
        } catch (\Exception $e) {
            Log::error('Error deleting student: ' . $e->getMessage());
            return false;
        }
    }

    //////////////////////////////////////////////
    function getStudent($id)
    {
        try {
            return $this->find($id);
        } catch (\Exception $e) {
            Log::error('Error getting student: ' . $e->getMessage());
            return null;
        }
    }

    //////////////////////////////////////////////
    function getAllStudents()
    {
        try {
            return $this->where('status', '=', 1)->get();
        } catch (\Exception $e) {
            Log::error('Error getting all students: ' . $e->getMessage());
            return collect();
        }
    }

    //////////////////////////////////////////////
    function getSearchStudents($title, $activeS, $delaying, $gender)
    {
        try {
            return $this
                ->where(function ($query) use ($title) {
                    if ($title != "") {
                        $query->where('name', 'LIKE', '%' . $title . '%');
                        $query->orWhere('mobile', 'LIKE', '%' . $title . '%');
                    }
                })
                ->where(function ($query) use ($activeS) {
                    if ($activeS != "") {
                        $query->where('status', $activeS);
                    }
                })
                ->where(function ($query) use ($delaying) {
                    if ($delaying != "") {
                        $query->where('delaying', $delaying);
                    }
                })
                ->orderBy('id', 'desc')->whereNull('deleted_at')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error searching students: ' . $e->getMessage());
            return collect();
        }
    }
    //////////////////////////////////////////////
    function getSearchDelayStudents($title = null, $activeS)
    {
        try {
            return $this->with('gropes')
                ->where(function ($query) use ($title) {
                    if ($title != "") {
                        $query->where('name', 'LIKE', '%' . $title . '%');
                        $query->orWhere('mobile', 'LIKE', '%' . $title . '%');
                    }
                })
                ->where(function ($query) use ($activeS) {
                    if ($activeS != "") {
                        $query->where('status', $activeS);
                    }
                })
                ->where('delaying', 1)
                ->orderBy('id', 'desc')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error searching delay students: ' . $e->getMessage());
            return collect();
        }
    }
    //////////////////////////////////////////////
    function getAllStudentsHaveBirthdays($title = null, $activeS = null, $delaying = null, $group_id = null)
    {
        try {
            $query = $this->select('students.*', \DB::raw('GROUP_CONCAT(groups.name SEPARATOR ", ") as group_names'))
                ->leftJoin('group_students', 'students.id', '=', 'group_students.student_id')
                ->leftJoin('groups', 'group_students.group_id', '=', 'groups.id')
                ->where(function ($query) use ($title) {
                    if ($title != "") {
                        $query->where('students.name', 'LIKE', '%' . $title . '%')
                              ->orWhere('students.mobile', 'LIKE', '%' . $title . '%')
                              ->orWhere('students.email', 'LIKE', '%' . $title . '%');
                    }
                })
                ->where(function ($query) use ($activeS) {
                    if ($activeS !== null && $activeS !== "") {
                        $query->where('students.status', $activeS);
                    }
                })
                ->where(function ($query) use ($delaying) {
                    if ($delaying !== null && $delaying !== "") {
                        $query->where('students.delaying', $delaying);
                    }
                })
                ->where(function ($query) use ($group_id) {
                    if ($group_id !== null && $group_id !== "") {
                        $query->where('group_students.group_id', $group_id);
                    }
                })
                ->whereMonth('students.dob', date('m'))
                ->whereDay('students.dob', date('d'))
                ->whereNull('students.deleted_at')
                ->groupBy('students.id')
                ->orderBy('students.id', 'desc');

            return $query->get();
        } catch (\Exception $e) {
            Log::error('Error getting birthdays: ' . $e->getMessage());
            return collect();
        }
    }

    //////////////////////////////////////////////
    function getAllStudentsHaveBirthdaysCount()
    {
        try {
            return $this
                ->whereMonth('dob', date('m'))
                ->whereDay('dob', date('d'))
                ->whereNull('deleted_at')
                ->count();
        } catch (\Exception $e) {
            Log::error('Error counting birthdays: ' . $e->getMessage());
            return 0;
        }
    }

    //////////////////////////////////////////////
    // function getSearchStudentsAskJoin($title = null) {
    //     try {
    //         return $this->where(function($query) use ($title) {
    //             if ($title != "") {
    //                 $query->where('name', 'LIKE', '%' . $title . '%');
    //                 $query->orWhere('mobile', 'LIKE', '%' . $title . '%');
    //             }
    //         })
    //         ->where('status', 0)
    //         ->orderBy('id', 'desc')
    //         ->get();
    //     } catch (\Exception $e) {
    //         Log::error('Error searching ask join students: ' . $e->getMessage());
    //         return collect();
    //     }
    // }
    public static function askJoinQuery(array $filters = [])
    {
        try {

            $search   = trim($filters['search'] ?? '');
            $dateFrom = trim($filters['date_from'] ?? '');
            $dateTo   = trim($filters['date_to'] ?? '');
            $gender   = trim($filters['gender'] ?? '');

            $query = self::query()
                ->where('status', 0)
                ->whereNull('deleted_at')
                ->select(
                    'id',
                    'name',
                    'name_en',
                    'mobile',
                    'dob',
                    'job',
                    'email',
                    'status',
                    'gender',
                    'program_type',
                    'requested_program_type',
                    'enrollment_type',
                    'join_date',
                    'parent_id',
                    'image',
                    'created_at'
                )
                ->orderByDesc('id');

            /*
        |--------------------------------------------------
        | Search
        |--------------------------------------------------
        */
            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhere('mobile', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%");
                });
            }

            /*
        |--------------------------------------------------
        | Gender
        |--------------------------------------------------
        */
            if ($gender !== '') {
                if ($gender == 'male') {
                    $query->whereIn('gender', ['male', '1']);
                } elseif ($gender == 'female') {
                    $query->whereIn('gender', ['female', '0', '2']);
                } else {
                    $query->where('gender', $gender);
                }
            }

            /*
        |--------------------------------------------------
        | Date Filters & Today Logic
        |--------------------------------------------------
        */
            $isToday = $filters['is_today'] ?? '';

            if ($isToday == '1') {
                $query->whereDate('created_at', Carbon::today());
            } else {
                if ($dateFrom) {
                    $query->whereDate('created_at', '>=', $dateFrom);
                }
                if ($dateTo) {
                    $query->whereDate('created_at', '<=', $dateTo);
                }
            }


            return $query;
        } catch (\Exception $e) {

            Log::error('Ask Join Query Error: ' . $e->getMessage());

            return self::query()->whereRaw('1=0');
        }
    }

    //////////////////////////////////////////////
    function getSearchStudentsAsk_update($title = null)
    {
        try {
            return $this->where(function ($query) use ($title) {
                if ($title != "") {
                    $query->where('name', 'LIKE', '%' . $title . '%');
                    $query->orWhere('mobile', 'LIKE', '%' . $title . '%');
                }
            })
                ->where('ask_update', 1)
                ->orderBy('id', 'desc')
                ->get();
        } catch (\Exception $e) {
            \Log::error('Error searching ask update students: ' . $e->getMessage());
            return collect();
        }
    }

    //////////////////////////////////////////////
    function getSearchStudentsAjax($title)
    {
        try {
            $data = $this->where(function ($query) use ($title) {
                if ($title != "") {
                    $query->where('name', 'LIKE', '%' . $title . '%');
                }
            })
                ->orderBy('id', 'desc')
                ->get();

            $sd = array();
            foreach ($data as $it) {
                $sd[] = array('value' => $it->id, 'label' => $it->name);
            }
            return $sd;
        } catch (\Exception $e) {
            \Log::error('Error searching students ajax: ' . $e->getMessage());
            return array();
        }
    }

    public static function searchReportQuery(array $filters = [])
    {
        $query = self::query()->whereNull('deleted_at');

        // Search text (Name, Mobile, Email)
        if (!empty($filters['title'])) {
            $search = $filters['title'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('mobile', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        // Basic Selection Filters
        if (isset($filters['activeS']) && $filters['activeS'] !== '') {
            $query->where('status', $filters['activeS']);
        }
        if (isset($filters['delaying']) && $filters['delaying'] !== '') {
            $query->where('delaying', $filters['delaying']);
        }
        if (isset($filters['gender']) && $filters['gender'] !== '') {
            $query->where('gender', $filters['gender']);
        }

        // Date Range Filters
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Quick Filters
        if (!empty($filters['is_today'])) {
            $query->whereDate('created_at', Carbon::today());
        }
        if (!empty($filters['is_activated_today'])) {
            $query->where('status', 1)->whereDate('updated_at', Carbon::today());
        }

        // Join-based Filters (Group and Teacher)
        if (!empty($filters['group_id']) || !empty($filters['teacher_id'])) {
            $query->whereHas('gropes', function($q) use ($filters) {
                if (!empty($filters['group_id'])) {
                    $q->where('group_id', $filters['group_id']);
                }
                if (!empty($filters['teacher_id'])) {
                    $q->whereHas('group', function($g) use ($filters) {
                        $g->where('teacher_id', $filters['teacher_id']);
                    });
                }
            });
        }

        return $query->orderByDesc('id');
    }
}
