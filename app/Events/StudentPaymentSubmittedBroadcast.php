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
        $channel = $this->branchId
            ? 'admin-notifications-branch-' . $this->branchId
            : 'admin-notifications';

        return [new Channel($channel)];
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
