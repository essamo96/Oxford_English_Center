<?php

namespace App\Support;

use App\Events\StudentNotificationBroadcast;
use App\Lib\PusherFactory;
use App\Models\GroupChatBan;
use Illuminate\Support\Facades\Log;

/**
 * Applying and lifting chat restrictions.
 *
 * Admins and teachers both moderate their groups, and both must produce exactly
 * the same effect: the same row shape, the same student notification, the same
 * live broadcast. Keeping that in one place is what stops the two paths from
 * drifting apart.
 */
class ChatModeration
{
    /**
     * Restrict a student in a group.
     *
     * @param  string      $type       GroupChatBan::TYPE_MUTE or TYPE_BAN
     * @param  string|null $reason     null = applied silently, no reason stated
     * @param  string      $actorType  'admin' | 'teacher'
     */
    public static function restrict($group, $studentId, string $type, ?string $reason, $actorId, string $actorType = 'admin'): GroupChatBan
    {
        $type = $type === GroupChatBan::TYPE_BAN ? GroupChatBan::TYPE_BAN : GroupChatBan::TYPE_MUTE;

        $ban = GroupChatBan::updateOrCreate(
            ['student_id' => $studentId, 'group_id' => $group->id],
            [
                'type'               => $type,
                'reason'             => $reason,
                'status'             => 1,
                'banned_by'          => $actorId,
                'restricted_by_type' => $actorType,
                'unbanned_by'        => null,
                'unbanned_at'        => null,
            ]
        );

        $isBan = $type === GroupChatBan::TYPE_BAN;

        self::notifyStudent($studentId, [
            'type'     => $isBan ? 'group_chat_banned' : 'group_chat_muted',
            'group_id' => $group->id,
            'title'    => $isBan ? 'تم حظرك من المجموعة' : 'تم إسكاتك في المجموعة',
            'message'  => GroupChatBan::blockMessage($type, $reason)
                . ' (مجموعة: ' . $group->name . ')',
            'reason'   => $reason,
            'can_view' => !$isBan,
        ]);

        return $ban;
    }

    /**
     * Lift whichever restriction is in force. The row stays as history.
     */
    public static function lift($group, $studentId, $actorId): ?GroupChatBan
    {
        $ban = GroupChatBan::where('group_id', $group->id)
            ->where('student_id', $studentId)
            ->first();

        if (!$ban) {
            return null;
        }

        $wasBan = $ban->isFullBan();

        $ban->status      = 0;
        $ban->unbanned_by = $actorId;
        $ban->unbanned_at = now();
        $ban->save();

        self::notifyStudent($studentId, [
            'type'     => 'group_chat_unbanned',
            'group_id' => $group->id,
            'title'    => $wasBan ? 'تم فك الحظر' : 'تم إلغاء الإسكات',
            'message'  => 'يمكنك الآن ' . ($wasBan ? 'عرض المحادثة و' : '')
                . 'إرسال الرسائل في مجموعة "' . $group->name . '".',
            'can_view' => true,
        ]);

        return $ban;
    }

    /**
     * Tell every open client that a message is gone, so it disappears from the
     * students' and teacher's chat boxes without waiting for a reload.
     */
    public static function broadcastMessageDeleted($groupId, $messageId): void
    {
        try {
            PusherFactory::make()->trigger('chat', 'message-deleted', [
                'data' => [
                    'group_id'   => (int) $groupId,
                    'message_id' => (int) $messageId,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('Chat message-deleted broadcast failed: ' . $e->getMessage());
        }
    }

    /**
     * Push onto the student's existing personal channel — the one the finance
     * notifications already use, so the chime and toast are wired up.
     */
    public static function notifyStudent($studentId, array $data): void
    {
        try {
            event(new StudentNotificationBroadcast((int) $studentId, $data));
        } catch (\Throwable $e) {
            Log::warning('Chat student notification failed: ' . $e->getMessage());
        }
    }
}
