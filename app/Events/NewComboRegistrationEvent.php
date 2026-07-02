<?php

namespace App\Events;

use App\Models\StudentCompo;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewComboRegistrationEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public StudentCompo $studentCompo,
        public int $pendingComboRegistrations,
        public int $totalNotifyCount
    ) {}

    public function broadcastOn(): array
    {
        return [new Channel('admin-notifications')];
    }

    public function broadcastAs(): string
    {
        return 'new.combo';
    }

    public function broadcastWith(): array
    {
        return [
            'type'              => 'combo',
            'id'                => $this->studentCompo->id,
            'client_name'       => $this->studentCompo->full_name_ar ?? $this->studentCompo->full_name_en,
            'service'           => 'برنامج ' . $this->studentCompo->program_type,
            'mobile'            => $this->studentCompo->phone,
            'message'           => 'طلب تسجيل كومبو جديد: ' . ($this->studentCompo->full_name_ar ?? $this->studentCompo->full_name_en),
            'icon'              => '🧾',
            'sound'             => 'contact',
            'pending_combo'     => $this->pendingComboRegistrations,
            'total_notify'      => $this->totalNotifyCount,
            'link'              => route('standalone_registrations.view'),
            'created_at'        => now()->diffForHumans(),
        ];
    }
}
