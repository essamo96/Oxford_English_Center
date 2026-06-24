<?php

namespace App\Jobs;

use App\Events\CountersUpdated;
use App\Events\StudentPaymentSubmittedBroadcast;
use App\Models\StudentPaymentSubmission;
use App\Models\User;
use App\Notifications\StudentPaymentSubmittedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

// Runs via dispatch(...)->afterResponse() from StudentFinancialController::submitPayment
// so the student's HTTP response returns instantly (no waiting on DB notify() rows or
// the synchronous Pusher HTTP calls), while admins still get the toast a fraction of a
// second later in the same process — no queue worker dependency, still effectively real-time.
class NotifyAdminsOfStudentPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly StudentPaymentSubmission $submission,
        public readonly ?int $branchId,
        public readonly string $studentName,
        public readonly float $amount,
        public readonly string $link,
    ) {}

    public function handle(): void
    {
        $admins = $this->branchId
            ? User::where('branch_id', $this->branchId)->get()
            : User::whereNull('branch_id')->get();

        foreach ($admins as $admin) {
            try {
                $admin->notify(new StudentPaymentSubmittedNotification($this->submission));
            } catch (\Throwable $e) {
                Log::error('[RT] StudentPaymentSubmittedNotification failed: ' . $e->getMessage());
            }
        }

        try {
            broadcast(new CountersUpdated($this->branchId));
        } catch (\Throwable $e) {
            Log::error('[RT] CountersUpdated broadcast failed (submitPayment): ' . $e->getMessage());
        }

        try {
            broadcast(new StudentPaymentSubmittedBroadcast(
                branchId:    $this->branchId ?? 0,
                studentName: $this->studentName,
                amount:      $this->amount,
                link:        $this->link,
            ));
        } catch (\Throwable $e) {
            Log::error('[RT] StudentPaymentSubmittedBroadcast failed: ' . $e->getMessage());
        }
    }
}
