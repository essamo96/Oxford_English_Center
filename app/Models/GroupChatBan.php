<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A student's chat restriction inside one group.
 *
 * Two kinds, deliberately different in severity:
 *
 *   TYPE_MUTE — cannot post, still reads the conversation normally
 *   TYPE_BAN  — cannot post and cannot open the conversation at all
 *
 * Rows survive being lifted (status flips to 0) so the list doubles as the
 * moderation history: who was restricted, by whom, why, and when it ended.
 */
class GroupChatBan extends Model
{
    protected $table = 'group_chat_bans';

    const TYPE_MUTE = 'mute';
    const TYPE_BAN  = 'ban';

    protected $fillable = [
        'student_id', 'group_id', 'type', 'reason', 'status',
        'banned_by', 'restricted_by_type', 'unbanned_by', 'unbanned_at',
    ];

    protected $casts = [
        'status'      => 'boolean',
        'unbanned_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Students::class, 'student_id');
    }

    public function group()
    {
        return $this->belongsTo(Groups::class, 'group_id');
    }

    public function bannedBy()
    {
        return $this->belongsTo(User::class, 'banned_by');
    }

    /** Restrictions currently in force. */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function isFullBan(): bool
    {
        return $this->type === self::TYPE_BAN;
    }

    public function typeLabel(): string
    {
        return $this->isFullBan() ? 'محظور' : 'مُسكت';
    }

    /**
     * The active restriction on this student in this group, if any.
     * Carries the type and reason, so callers can tell mute from ban.
     */
    public static function activeBan($studentId, $groupId)
    {
        return static::where('student_id', $studentId)
            ->where('group_id', $groupId)
            ->where('status', 1)
            ->first();
    }

    /** Is the student blocked from posting (either restriction does that)? */
    public static function isBanned($studentId, $groupId): bool
    {
        return static::where('student_id', $studentId)
            ->where('group_id', $groupId)
            ->where('status', 1)
            ->exists();
    }

    /**
     * Is the student barred from even reading the conversation?
     * Only a full ban does that — a mute leaves reading intact.
     */
    public static function isBlockedFromViewing($studentId, $groupId): bool
    {
        return static::where('student_id', $studentId)
            ->where('group_id', $groupId)
            ->where('status', 1)
            ->where('type', self::TYPE_BAN)
            ->exists();
    }

    /**
     * Wording shown to the student, and to the moderator in confirmations.
     * A null reason means the restriction was applied silently.
     */
    public static function blockMessage($type, $reason = null): string
    {
        $base = $type === self::TYPE_BAN
            ? 'تم حظرك من هذه المجموعة — لا يمكنك عرض المحادثة أو إرسال الرسائل.'
            : 'تم إسكاتك في هذه المجموعة — يمكنك قراءة المحادثة دون إرسال رسائل.';

        return $reason ? $base . ' السبب: ' . $reason : $base;
    }
}
