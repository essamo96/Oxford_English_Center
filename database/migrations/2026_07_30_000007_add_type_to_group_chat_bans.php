<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Splits the single "ban" into two distinct restrictions:
 *
 *   mute — cannot post, can still read the conversation
 *   ban  — cannot post AND cannot see the conversation at all
 *
 * Everything created before this migration behaved as a mute (read access was
 * never removed), so existing rows are backfilled as 'mute' rather than 'ban' —
 * silently upgrading them to a full ban would cut students off from history
 * they can currently see.
 *
 * `restricted_by_type` records whether an admin or the group's teacher applied
 * it, since teachers can now moderate their own groups.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('group_chat_bans', function (Blueprint $table) {
            if (!Schema::hasColumn('group_chat_bans', 'type')) {
                $table->string('type', 10)->default('mute')->after('group_id');
            }
            // 'admin' | 'teacher'
            if (!Schema::hasColumn('group_chat_bans', 'restricted_by_type')) {
                $table->string('restricted_by_type', 10)->default('admin')->after('banned_by');
            }
        });

        DB::table('group_chat_bans')->whereNull('type')->update(['type' => 'mute']);
    }

    public function down(): void
    {
        // Safety: do not drop columns in synchronization.
    }
};
