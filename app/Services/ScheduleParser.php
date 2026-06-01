<?php

namespace App\Services;

use Carbon\Carbon;

/**
 * Parses the free-text Arabic schedule stored on the `times` table
 *   days  : "السبت والاثنين والأربعاء"
 *   times : "10:00 صباحاً - 12:00 مساءً"
 * into machine-usable weekday numbers and a start/end time window.
 *
 * Designed to FAIL-OPEN: when a value cannot be parsed, the corresponding
 * check returns null (unknown) so teachers are never locked out by bad text.
 */
class ScheduleParser
{
    /** Normalise Arabic letters + strip diacritics/tatweel for reliable matching. */
    private function norm(?string $s): string
    {
        $s = (string) $s;
        $s = str_replace(['أ', 'إ', 'آ', 'ٱ'], 'ا', $s);
        $s = str_replace('ى', 'ي', $s);
        $s = str_replace('ة', 'ه', $s);
        $s = preg_replace('/[\x{064B}-\x{0652}\x{0640}]/u', '', $s); // harakat + tatweel
        return mb_strtolower(trim($s));
    }

    /** Weekday numbers (Carbon: 0=Sunday … 6=Saturday) found in the days text. */
    public function dayNumbers(?string $daysText): array
    {
        $map = [
            'احد'    => 0,
            'اثنين'  => 1,
            'ثلاثاء' => 2,
            'اربعاء' => 3,
            'خميس'   => 4,
            'جمعه'   => 5,
            'سبت'    => 6,
        ];
        $n = $this->norm($daysText);
        $out = [];
        foreach ($map as $token => $num) {
            if (mb_strpos($n, $token) !== false) $out[] = $num;
        }
        return array_values(array_unique($out));
    }

    /** Returns ['start' => 'H:i', 'end' => 'H:i'] or null when not parseable. */
    public function timeRange(?string $timesText): ?array
    {
        $t = $this->norm($timesText);

        // Pass 1: marker-bearing times (صباح/مساء/am/pm)
        preg_match_all('/(\d{1,2})(?::(\d{2}))?\s*(صباح\S*|مساء\S*|am|pm)/u', $t, $m, PREG_SET_ORDER);

        // Pass 2: fall back to plain HH:MM if we did not find two marked times
        if (count($m) < 2) {
            preg_match_all('/(\d{1,2}):(\d{2})/u', $t, $m2, PREG_SET_ORDER);
            if (count($m2) >= 2) {
                $start = $this->to24($m2[0][1], $m2[0][2] ?? '0', '');
                $end   = $this->to24($m2[1][1], $m2[1][2] ?? '0', '');
                return ['start' => $start, 'end' => $end];
            }
            return null;
        }

        $start = $this->to24($m[0][1], $m[0][2] ?? '0', $m[0][3] ?? '');
        $end   = $this->to24($m[1][1], $m[1][2] ?? '0', $m[1][3] ?? '');
        return ['start' => $start, 'end' => $end];
    }

    private function to24($hour, $min, string $marker): string
    {
        $h = (int) $hour;
        $min = (int) $min;
        $marker = $this->norm($marker);
        $isPM = $marker !== '' && (mb_strpos($marker, 'مساء') !== false || $marker === 'pm');
        $isAM = $marker !== '' && (mb_strpos($marker, 'صباح') !== false || $marker === 'am');
        if ($isPM && $h < 12) $h += 12;
        if ($isAM && $h === 12) $h = 0;
        $h = max(0, min(23, $h));
        $min = max(0, min(59, $min));
        return sprintf('%02d:%02d', $h, $min);
    }

    /**
     * Evaluate whether $now falls inside the lecture window described by the schedule.
     * Returns:
     *   scheduled_today : true|false|null  (null = days unparseable → unknown)
     *   within          : true|false|null  (null = time unparseable → unknown)
     *   start, end      : Carbon|null      (the resolved window on $now's date)
     */
    public function evaluate(?string $daysText, ?string $timesText, Carbon $now, int $graceMinutes = 0): array
    {
        $days = $this->dayNumbers($daysText);
        $scheduledToday = empty($days) ? null : in_array((int) $now->dayOfWeek, $days, true);

        $range = $this->timeRange($timesText);
        $start = $end = null;
        $within = null;

        if ($range) {
            $start = $now->copy()->setTimeFromTimeString($range['start']);
            $end   = $now->copy()->setTimeFromTimeString($range['end']);
            if ($end->lessThanOrEqualTo($start)) {
                $end->addDay(); // overnight window safety
            }
            $from = $start->copy()->subMinutes($graceMinutes);
            $to   = $end->copy()->addMinutes($graceMinutes);
            $within = $now->betweenIncluded($from, $to);
        }

        return [
            'scheduled_today' => $scheduledToday,
            'within'          => $within,
            'start'           => $start,
            'end'             => $end,
            'days'            => $days,
        ];
    }
}
