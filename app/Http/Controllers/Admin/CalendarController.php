<?php

namespace App\Http\Controllers\Admin;

use App\Models\Groups;
use App\Models\Teachers;
use App\Models\Times;
use App\Models\GroupStudents;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        parent::$data['active_menu'] = 'calendar';
    }

    public function index()
    {
        parent::$data['teachers'] = Teachers::whereNull('deleted_at')->get();
        return view('admin.calendar.view', parent::$data);
    }

    public function getEvents(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');
        $teacherId = $request->input('teacher_id');
        $studentSearch = $request->input('student_search');

        $groupIds = null;

        if (!empty($studentSearch)) {
            $student = \App\Models\Students::where('name', 'like', "%{$studentSearch}%")
                ->orWhere('mobile', 'like', "%{$studentSearch}%")
                ->orWhere('email', 'like', "%{$studentSearch}%")
                ->orWhere('username', 'like', "%{$studentSearch}%")
                ->first();

            if (!$student) {
                return response()->json([
                    'error' => true,
                    'type' => 'not_found',
                    'message' => 'الطالب غير موجود في النظام.'
                ]);
            }

            $currentGroupIds = \App\Models\GroupStudents::where('student_id', $student->id)
                ->whereNull('deleted_at')
                ->pluck('group_id')
                ->toArray();

            if (empty($currentGroupIds)) {
                return response()->json([
                    'error' => true,
                    'type' => 'not_enrolled',
                    'message' => 'الطالب "' . $student->name . '" غير منتسب لأي مجموعة حالياً.',
                    'student_id' => $student->id,
                    'student_encrypted_id' => encrypt($student->id)
                ]);
            }

            $groupIds = $currentGroupIds;
        }

        $groupsQuery = Groups::with(['teacher', 'ctime', 'program'])
            ->whereNull('deleted_at')
            ->where('status', 1)
            ->whereNotNull('teacher_id')
            ->when($teacherId, function ($query) use ($teacherId) {
                return $query->where('teacher_id', $teacherId);
            });

        if ($groupIds !== null) {
            $groupsQuery->whereIn('id', $groupIds);
        }

        $groups = $groupsQuery->get();

        $events = [];
        $conflicts = [];

        // Pre-calculate student counts for performance
        $studentCounts = GroupStudents::whereNull('deleted_at')
            ->selectRaw('group_id, count(*) as count')
            ->groupBy('group_id')
            ->pluck('count', 'group_id');

        foreach ($groups as $group) {
            if (!$group->ctime || !$group->ctime->days || !$group->ctime->times) continue;

            // Simple parsing of "10:00 - 12:00"
            $timeParts = explode('-', $group->ctime->times);
            if (count($timeParts) < 2) continue;
            
            $startTimeStr = trim($timeParts[0]);
            $endTimeStr = trim($timeParts[1]);

            // Simple parsing of "Saturday - Monday - Wednesday"
            $days = explode('-', $group->ctime->days);
            $dayList = array_map(function($d) {
                return strtolower(trim($d));
            }, $days);

            // Mapping Arabic/English day names to Carbon day constants if needed
            $dayMapping = [
                'saturday' => Carbon::SATURDAY,
                'sunday' => Carbon::SUNDAY,
                'monday' => Carbon::MONDAY,
                'tuesday' => Carbon::TUESDAY,
                'wednesday' => Carbon::WEDNESDAY,
                'thursday' => Carbon::THURSDAY,
                'friday' => Carbon::FRIDAY,
                'السبت' => Carbon::SATURDAY,
                'الأحد' => Carbon::SUNDAY,
                'الاثنين' => Carbon::MONDAY,
                'الثلاثاء' => Carbon::TUESDAY,
                'الأربعاء' => Carbon::WEDNESDAY,
                'الخميس' => Carbon::THURSDAY,
                'الجمعة' => Carbon::FRIDAY,
            ];

            // Generate event dates between start and end of calendar view
            $currentDate = Carbon::parse($start);
            $endDate = Carbon::parse($end);

            while ($currentDate <= $endDate) {
                $dayName = strtolower($currentDate->format('l'));
                
                // Check if current date is within the group's lifetime
                $groupStart = Carbon::parse($group->start_date);
                $groupEnd = Carbon::parse($group->end_date);

                if (($currentDate >= $groupStart->startOfDay() && $currentDate <= $groupEnd->endOfDay()) || (!$group->start_date)) {
                    
                    if (in_array($dayName, $dayList)) {
                        $eventStart = $currentDate->copy()->setTimeFromTimeString($startTimeStr);
                        $eventEnd = $currentDate->copy()->setTimeFromTimeString($endTimeStr);

                        $events[] = [
                            'id' => $group->id . '_' . $currentDate->toDateString(),
                            'groupId' => $group->id,
                            'title' => $group->name ?? 'مجموعة بدون اسم',
                            'start' => $eventStart->toIso8601String(),
                            'end' => $eventEnd->toIso8601String(),
                            'color' => '#3699FF', // Default Blue
                            // FullCalendar standard way: custom props at top level
                            'teacher' => ($group->teacher && $group->teacher->name) ? $group->teacher->name : 'غير محدد',
                            'students' => $studentCounts[$group->id] ?? 0,
                            'program' => ($group->program && $group->program->title) ? $group->program->title : 'بدون برنامج',
                            'original_id' => $group->id,
                            'conflict' => false // Will be updated below
                        ];
                    }
                }
                $currentDate->addDay();
            }
        }

        // Conflict Detection Logic
        $eventsCount = count($events);
        for ($i = 0; $i < $eventsCount; $i++) {
            for ($j = $i + 1; $j < $eventsCount; $j++) {
                $e1 = $events[$i];
                $e2 = $events[$j];

                // If same teacher and overlapping time on same day
                if ($e1['teacher'] === $e2['teacher'] && $e1['teacher'] !== 'غير محدد') {
                    $start1 = Carbon::parse($e1['start']);
                    $end1 = Carbon::parse($e1['end']);
                    $start2 = Carbon::parse($e2['start']);
                    $end2 = Carbon::parse($e2['end']);

                    if ($start1->toDateString() === $start2->toDateString()) {
                        if ($start1 < $end2 && $end1 > $start2) {
                            $events[$i]['color'] = '#F64E60'; // Red
                            $events[$j]['color'] = '#F64E60'; // Red
                            $events[$i]['conflict'] = true;
                            $events[$j]['conflict'] = true;
                        }
                    }
                }
            }
        }

        return response()->json($events);
    }
}
