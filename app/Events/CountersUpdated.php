<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Services\NotificationCounterService;

class CountersUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $counters;
    private ?int $branchId;

    public function __construct(?int $branchId = null)
    {
        $this->branchId = $branchId;
        $this->counters = NotificationCounterService::getAllCounters($branchId);
    }

    public function broadcastOn(): array
    {
        if ($this->branchId) {
            return [new Channel('admin-notifications-branch-' . $this->branchId)];
        }

        return [new Channel('admin-notifications')];
    }

    public function broadcastAs(): string
    {
        return 'counters.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'type'     => 'counters_update',
            'counters' => $this->counters
        ];
    }
}
