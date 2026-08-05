<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// Reuses the existing 'admin-notifications' channel (see NewBookingEvent) for Examination
// Center events that concern the admin bell (e.g. a placement test attempt submitted).
class AdminExamNotificationBroadcast implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly array $data,
        public readonly ?int $branchId = null
    ) {}

    public function broadcastOn(): array
    {
        $channels = [new Channel('admin-notifications')];

        if ($this->branchId) {
            $channels[] = new Channel('admin-notifications-branch-' . $this->branchId);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'exam.notification';
    }

    public function broadcastWith(): array
    {
        return $this->data;
    }
}
