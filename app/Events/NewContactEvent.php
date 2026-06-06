<?php

namespace App\Events;

use App\Models\Contacts;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

/**
 * Broadcast when a new "Contact Us" message is submitted
 * (ContactController@postContact persists a Contacts row with status = 0).
 *
 * Implements ShouldBroadcastNow so it is delivered synchronously without a
 * running queue worker.
 */
class NewContactEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Contacts $contact,
        public int $unreadContacts,
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
        return 'new.contact';
    }

    public function broadcastWith(): array
    {
        return [
            'type'            => 'contact',
            'id'              => $this->contact->id,
            'client_name'     => $this->contact->name,
            'email'           => $this->contact->email,
            'subject'         => $this->contact->subject ?: 'رسالة جديدة',
            'preview'         => Str::limit((string) $this->contact->message, 80),
            'message'         => 'رسالة جديدة من: ' . $this->contact->name,
            'icon'            => '✉️',
            'sound'           => 'contact',
            'unread_contacts' => $this->unreadContacts,
            'total_notify'    => $this->totalNotifyCount,
            'link'            => route('contacts.view'),
            'created_at'      => now()->diffForHumans(),
        ];
    }
}
