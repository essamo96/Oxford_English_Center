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

    public function __construct()
    {
        $this->counters = NotificationCounterService::getAllCounters();
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('admin-notifications'),
        ];
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
