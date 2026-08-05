<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// Mirrors StudentNotificationBroadcast for the teacher guard/portal.
class TeacherNotificationBroadcast implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int $teacherId,
        public readonly array $data
    ) {}

    public function broadcastOn(): array
    {
        return [new Channel('teacher-notifications-' . $this->teacherId)];
    }

    public function broadcastAs(): string
    {
        return 'teacher.notification';
    }

    public function broadcastWith(): array
    {
        return $this->data;
    }
}
