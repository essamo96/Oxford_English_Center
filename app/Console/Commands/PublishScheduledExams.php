<?php

namespace App\Console\Commands;

use App\Http\Controllers\ExamNotifier;
use App\Models\Exam;
use Illuminate\Console\Command;

/**
 * Flips Group Exams from 'scheduled' to 'published' the moment their start_date arrives, and
 * fires the student notification at that exact moment — not whenever an admin/teacher happens
 * to click "publish" manually. Without this, an exam scheduled for a future date either sends
 * its notification too early (if marked published now) or never sends one at all (if left as
 * 'scheduled' and nobody manually republishes it later).
 *
 * Placement Tests are intentionally excluded: they have no fixed student list to notify, so
 * their publish action stays fully manual (admin decides when the bank of questions goes live).
 */
class PublishScheduledExams extends Command
{
    protected $signature = 'exams:publish-scheduled';

    protected $description = 'Auto-publish Group Exams whose start_date has arrived and notify enrolled students';

    public function handle(): int
    {
        $exams = Exam::where('category', 'group')
            ->where('status', 'scheduled')
            ->whereNotNull('start_date')
            ->where('start_date', '<=', now())
            ->get();

        foreach ($exams as $exam) {
            $exam->update(['status' => 'published']);
            ExamNotifier::notifyGroupExamPublished($exam);
            $this->info("Published exam #{$exam->id} ({$exam->title}) and notified its group.");
        }

        if ($exams->isEmpty()) {
            $this->line('No scheduled exams due for publishing.');
        }

        return self::SUCCESS;
    }
}
