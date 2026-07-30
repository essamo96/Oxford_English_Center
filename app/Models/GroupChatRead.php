<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * A single admin's read position inside one group conversation.
 */
class GroupChatRead extends Model
{
    protected $table = 'group_chat_reads';

    protected $fillable = ['user_id', 'group_id', 'last_read_message_id'];

    /**
     * Unread count per group for one admin.
     *
     * Only messages the admin did not write count as unread — their own comments
     * are, by definition, already read. Returns [group_id => count].
     */
    public static function unreadCountsFor($userId)
    {
        $marks = static::where('user_id', $userId)->pluck('last_read_message_id', 'group_id');

        $rows = DB::table('messages')
            ->select('group_id', DB::raw('COUNT(*) as unread'), DB::raw('MAX(id) as max_id'))
            ->where(function ($q) use ($userId) {
                // Anything not written by this admin.
                $q->where('user_type', '!=', Message::TYPE_ADMIN)
                  ->orWhere('from_user', '!=', $userId);
            })
            ->groupBy('group_id')
            ->get();

        $counts = [];
        foreach ($rows as $row) {
            $mark = (int) ($marks[$row->group_id] ?? 0);
            if ($mark === 0) {
                $counts[$row->group_id] = (int) $row->unread;
                continue;
            }
            // Re-count only past the read mark; a second cheap aggregate beats
            // loading message ids into PHP.
            $counts[$row->group_id] = (int) DB::table('messages')
                ->where('group_id', $row->group_id)
                ->where('id', '>', $mark)
                ->where(function ($q) use ($userId) {
                    $q->where('user_type', '!=', Message::TYPE_ADMIN)
                      ->orWhere('from_user', '!=', $userId);
                })
                ->count();
        }

        return $counts;
    }

    /** Move this admin's read mark to the newest message in the group. */
    public static function markRead($userId, $groupId, $lastMessageId)
    {
        static::updateOrCreate(
            ['user_id' => $userId, 'group_id' => $groupId],
            ['last_read_message_id' => $lastMessageId]
        );
    }
}
