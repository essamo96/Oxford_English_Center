<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class StudentPaymentSubmittedBroadcast implements ShouldBroadcastNow
{
    use Dispatchable;

    public function __construct(
        private readonly int    $branchId,
        private readonly string $studentName,
        private readonly float  $amount,
        private readonly string $link,
    ) {}

    public function broadcastOn(): array
    {
        // Same dual-channel pattern as NewBookingEvent: always include the global channel
        // (super admins with branch_id=null subscribe to it and must see every branch's
        // activity) plus the branch-specific channel when one applies (so that branch's
        // admins also get it on their own channel).
        $channels = [new Channel('admin-notifications')];

        if ($this->branchId) {
            $channels[] = new Channel('admin-notifications-branch-' . $this->branchId);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'student.payment.submitted';
    }

    public function broadcastWith(): array
    {
        return [
            'student_name' => $this->studentName,
            'amount'       => $this->amount,
            'link'         => $this->link,
        ];
    }
}
