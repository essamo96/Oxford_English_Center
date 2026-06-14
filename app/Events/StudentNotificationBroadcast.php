<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentNotificationBroadcast implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int $studentId,
        public readonly array $data
    ) {}

    public function broadcastOn(): array
    {
        return [new Channel('student-notifications-' . $this->studentId)];
    }

    public function broadcastAs(): string
    {
        return 'student.notification';
    }

    public function broadcastWith(): array
    {
        return $this->data;
    }
}
