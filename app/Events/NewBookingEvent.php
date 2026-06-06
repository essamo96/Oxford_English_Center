<?php

namespace App\Events;

use App\Models\Students;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Broadcast when a new "booking" arrives.
 *
 * In this project a booking is a front-end registration handled by
 * ContactController@postBook, which persists a pending Students row
 * (status = 0, seen = 0). We reuse the same definition the admin header
 * bell already uses for "Unseen_Students_Count" so the live counter stays
 * consistent with the server-rendered value.
 *
 * Implements ShouldBroadcastNow so the event is pushed synchronously and
 * does NOT depend on a running queue worker.
 */
class NewBookingEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Students $student,
        public int $pendingBookings,
        public int $todayBookings,
        public int $totalNotifyCount
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('admin-notifications'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'new.booking';
    }

    public function broadcastWith(): array
    {
        $programType = $this->student->program_type === 'kids' ? 'برنامج الأطفال' : 'برنامج الكبار';

        return [
            'type'              => 'booking',
            'id'                => $this->student->id,
            'client_name'       => $this->student->name,
            'service'           => $programType,
            'mobile'            => $this->student->mobile,
            'message'           => 'حجز/تسجيل جديد من: ' . $this->student->name,
            'icon'              => '📅',
            'sound'             => 'booking',
            'pending_bookings'  => $this->pendingBookings,
            'today_bookings'    => $this->todayBookings,
            'total_notify'      => $this->totalNotifyCount,
            'link'              => route('dashboard.view.membership'),
            'created_at'        => now()->diffForHumans(),
        ];
    }
}
