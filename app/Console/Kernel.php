<?php

namespace App\Console;

use App\Models\GroupStudents;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Carbon\Carbon;
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Queue worker: يعمل كل دقيقة، يُغلق تلقائياً عند فراغ الطابور،
        // withoutOverlapping يمنع تشغيل نسخة ثانية إذا الأولى لم تنته بعد.
        $schedule->command('queue:work --stop-when-empty --tries=3 --timeout=60')
                 ->everyMinute()
                 ->withoutOverlapping(5);

        // Examination Center: auto-publish Group Exams once their start_date arrives, and
        // notify enrolled students at that exact moment (see PublishScheduledExams for why).
        $schedule->command('exams:publish-scheduled')
                 ->everyMinute()
                 ->withoutOverlapping(5);

        // $schedule->call(function () {
        //     GroupStudents::where('has_evaluation', 1)
        //     ->where('evaluation_at', '<=', now()->subWeek(3))
        //         ->update([
        //             'has_evaluation' => 0,
        //             'evaluation_at' => Carbon::now(),
        //     ]);
        // })->daily();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
