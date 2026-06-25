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
    public array $globalCounters;
    public array $branchBreakdown;
    private ?int $branchId;

    public function __construct(?int $branchId = null)
    {
        $this->branchId = $branchId;
        // Branch-scoped counters (consumed by that branch's admins) AND grand-total counters
        // (consumed by the super admin, branch_id=null) are both computed here and bundled in
        // one payload — broadcastWith() can't vary per channel, and this event now reaches both
        // the branch channel and the global channel in a single broadcast() call.
        $this->counters       = NotificationCounterService::getAllCounters($branchId);
        $this->globalCounters = NotificationCounterService::getAllCounters(null);
        $this->branchBreakdown = NotificationCounterService::branchFinancialBreakdown();
    }

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
        return 'counters.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'type'             => 'counters_update',
            'counters'         => $this->counters,
            'counters_global'  => $this->globalCounters,
            'branch_breakdown' => $this->branchBreakdown,
        ];
    }
}
