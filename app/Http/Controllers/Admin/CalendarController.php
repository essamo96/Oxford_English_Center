<?php

namespace App\Http\Controllers\Admin;

use App\Models\Groups;
use App\Models\Teachers;
use App\Models\GroupStudents;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends AdminController
{
    /**
     * Map of recognizable day labels (Arabic + English variants) to a
     * canonical lowercase English weekday name used by Carbon.
     */
    private $dayMap = [
        'السبت'     => 'saturday',
        'الأحد'     => 'sunday',
        'الاحد'     => 'sunday',
        'الاثنين'   => 'monday',
        'الإثنين'   => 'monday',
        'الثلاثاء'  => 'tuesday',
        'الأربعاء'  => 'wednesday',
        'الاربعاء'  => 'wednesday',
        'الخميس'    => 'thursday',
        'الجمعة'    => 'friday',
        'saturday'  => 'saturday',
        'sunday'    => 'sunday',
        'monday'    => 'monday',
        'tuesday'   => 'tuesday',
        'wednesday' => 'wednesday',
        'thursday'  => 'thursday',
        'friday'    => 'friday',
    ];

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
        $studentSearch = trim((string) $request->input('student_search'));

        // Guard: a valid date window is required.
        if (empty($start) || empty($end)) {
            return response()->json([]);
        }

        $groupIds = null;

        // --- Optional filter by student ---------------------------------
        if ($studentSearch !== '') {
            $students = \App\Models\Students::where(function ($q) use ($studentSearch) {
                $q->where('name', 'like', "%{$studentSearch}%")
                    ->orWhere('mobile', 'like', "%{$studentSearch}%")
                    ->orWhere('email', 'like', "%{$studentSearch}%")
                    ->orWhere('username', 'like', "%{$studentSearch}%");
            })->whereNull('deleted_at')->get();

            if ($students->isEmpty()) {
                return response()->json([
                    'error'   => true,
                    'type'    => 'not_found',
                    'message' => 'الطالب غير موجود في النظام.',
                ]);
            }

            $studentIds = $students->pluck('id')->all();

            $currentGroupIds = GroupStudents::whereIn('student_id', $studentIds)
                ->whereNull('deleted_at')
                ->pluck('group_id')
                ->unique()
                ->values()
                ->all();

            if (empty($currentGroupIds)) {
                $first = $students->first();
                return response()->json([
                    'error'                 => true,
                    'type'                  => 'not_enrolled',
                    'message'               => 'الطالب "' . $first->name . '" غير منتسب لأي مجموعة حالياً.',
                    'student_id'            => $first->id,
                    'student_encrypted_id'  => encrypt($first->id),
                ]);
            }

            $groupIds = $currentGroupIds;
        }

        // --- Load the relevant active groups ----------------------------
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

        // Pre-calculate student counts in one query (avoids N+1).
        $studentCounts = GroupStudents::whereNull('deleted_at')
            ->selectRaw('group_id, count(*) as count')
            ->groupBy('group_id')
            ->pluck('count', 'group_id');

        $windowStart = Carbon::parse($start)->startOfDay();
        $windowEnd   = Carbon::parse($end)->endOfDay();

        $events = [];

        foreach ($groups as $group) {
            try {
                if (!$group->ctime || !$group->ctime->days || !$group->ctime->times) {
                    continue;
                }

                $time = $this->parseTimeRange($group->ctime->times);
                if (!$time) {
                    continue; // unparsable time string – skip rather than break the calendar
                }

                $dayList = $this->parseDays($group->ctime->days);
                if (empty($dayList)) {
                    continue;
                }

                // Clamp the iteration window to the group's own lifetime.
                $groupStart = $group->start_date ? Carbon::parse($group->start_date)->startOfDay() : $windowStart;
                $groupEnd   = $group->end_date ? Carbon::parse($group->end_date)->endOfDay() : $windowEnd;

                $cursor   = $windowStart->greaterThan($groupStart) ? $windowStart->copy() : $groupStart->copy();
                $stopDate = $windowEnd->lessThan($groupEnd) ? $windowEnd->copy() : $groupEnd->copy();

                $teacherName = ($group->teacher && $group->teacher->name) ? $group->teacher->name : 'غير محدد';
                $program     = ($group->program && $group->program->title) ? $group->program->title : 'بدون برنامج';
                $studentsNo  = $studentCounts[$group->id] ?? 0;
                $link        = $group->zoom ?: $group->drive;

                while ($cursor <= $stopDate) {
                    if (in_array(strtolower($cursor->format('l')), $dayList, true)) {
                        $eventStart = $cursor->copy()->setTimeFromTimeString($time['start']);
                        $eventEnd   = $cursor->copy()->setTimeFromTimeString($time['end']);

                        // Handle ranges that cross midnight defensively.
                        if ($eventEnd->lessThanOrEqualTo($eventStart)) {
                            $eventEnd->addDay();
                        }

                        $events[] = [
                            'id'    => $group->id . '_' . $cursor->toDateString(),
                            'title' => $group->name ?: 'مجموعة بدون اسم',
                            'start' => $eventStart->toIso8601String(),
                            'end'   => $eventEnd->toIso8601String(),
                            'color' => '#3699FF',
                            'extendedProps' => [
                                'groupId'   => $group->id,
                                'teacher'   => $teacherName,
                                'teacherId' => $group->teacher_id,
                                'students'  => $studentsNo,
                                'program'   => $program,
                                'link'      => $link,
                                'conflict'  => false,
                            ],
                        ];
                    }
                    $cursor->addDay();
                }
            } catch (\Throwable $e) {
                // A single malformed group must never blank out the whole calendar.
                \Log::warning('Calendar: skipped group #' . $group->id . ' – ' . $e->getMessage());
                continue;
            }
        }

        $this->markTeacherConflicts($events);

        return response()->json(array_values($events));
    }

    /**
     * Flag overlapping sessions taught by the same teacher on the same day
     * (a teacher cannot be in two places at once).
     */
    private function markTeacherConflicts(array &$events)
    {
        // Bucket events by teacher + calendar day, then compare only within
        // each bucket – far cheaper than an all-pairs O(n^2) scan.
        $buckets = [];
        foreach ($events as $idx => $event) {
            $teacherId = $event['extendedProps']['teacherId'] ?? null;
            if (!$teacherId) {
                continue;
            }
            $day = substr($event['start'], 0, 10);
            $buckets[$teacherId . '|' . $day][] = $idx;
        }

        foreach ($buckets as $indexes) {
            $count = count($indexes);
            for ($i = 0; $i < $count; $i++) {
                for ($j = $i + 1; $j < $count; $j++) {
                    $a = $events[$indexes[$i]];
                    $b = $events[$indexes[$j]];

                    if ($a['start'] < $b['end'] && $a['end'] > $b['start']) {
                        foreach ([$indexes[$i], $indexes[$j]] as $k) {
                            $events[$k]['color'] = '#F64E60';
                            $events[$k]['extendedProps']['conflict'] = true;
                        }
                    }
                }
            }
        }
    }

    /**
     * Resolve a free-text day string (Arabic or English, any separator) to a
     * list of canonical lowercase English weekday names.
     */
    private function parseDays($daysStr)
    {
        $daysStr = trim($daysStr);

        // "يومياً" / "daily" => every day of the week.
        if (mb_strpos($daysStr, 'يومي') !== false || stripos($daysStr, 'daily') !== false) {
            return ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        }

        $result = [];
        foreach ($this->dayMap as $label => $canonical) {
            if (in_array($canonical, $result, true)) {
                continue;
            }
            // Arabic labels: substring match (days are joined with "و").
            // English labels: word match, case-insensitive.
            if (preg_match('/[ء-ي]/u', $label)) {
                $found = mb_strpos($daysStr, $label) !== false;
            } else {
                $found = stripos($daysStr, $label) !== false;
            }
            if ($found) {
                $result[] = $canonical;
            }
        }

        return array_values(array_unique($result));
    }

    /**
     * Parse a time range like "10:00 صباحاً - 12:00 مساءً", "08:00 AM - 10:00 AM"
     * or "10:00 - 12:00" into 24h ['start' => 'HH:MM', 'end' => 'HH:MM'].
     */
    private function parseTimeRange($raw)
    {
        $parts = preg_split('/\s*-\s*/u', trim($raw));
        if (count($parts) < 2) {
            return null;
        }

        $start = $this->parseSingleTime($parts[0]);
        $end   = $this->parseSingleTime($parts[1]);

        if ($start === null || $end === null) {
            return null;
        }

        return ['start' => $start, 'end' => $end];
    }

    private function parseSingleTime($value)
    {
        $value = trim($value);
        if (!preg_match('/(\d{1,2}):(\d{2})/u', $value, $m)) {
            return null;
        }

        $hour   = (int) $m[1];
        $minute = (int) $m[2];

        $lower = mb_strtolower($value);
        $isPM = (mb_strpos($lower, 'مساء') !== false) || strpos($lower, 'pm') !== false;
        $isAM = (mb_strpos($lower, 'صباح') !== false) || strpos($lower, 'am') !== false;

        if ($isPM && $hour < 12) {
            $hour += 12;
        }
        if ($isAM && $hour === 12) {
            $hour = 0;
        }

        if ($hour < 0 || $hour > 23 || $minute < 0 || $minute > 59) {
            return null;
        }

        return sprintf('%02d:%02d', $hour, $minute);
    }
}
