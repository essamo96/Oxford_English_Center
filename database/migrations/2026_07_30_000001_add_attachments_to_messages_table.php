<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Group chat gains two capabilities that the original student/teacher-only table
 * could not express:
 *
 *  - attachments (files + recorded voice notes), stored as a public path
 *  - an admin sender: `user_type` was 0 = student / 1 = teacher; 2 = admin is new,
 *    and admins live in `users`, a third table the name/avatar lookup must cover.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'attachment')) {
                $table->string('attachment', 255)->nullable()->after('content');
            }
            if (!Schema::hasColumn('messages', 'attachment_name')) {
                $table->string('attachment_name', 255)->nullable()->after('attachment');
            }
            // 'file' | 'image' | 'audio' — drives which player/preview the bubble renders.
            if (!Schema::hasColumn('messages', 'attachment_type')) {
                $table->string('attachment_type', 20)->nullable()->after('attachment_name');
            }
        });

        // `content` is NOT NULL in the original schema, but an attachment-only message
        // (a voice note with no caption) has nothing to put there.
        if (Schema::hasColumn('messages', 'content')) {
            try {
                \DB::statement('ALTER TABLE `messages` MODIFY `content` TEXT NULL');
            } catch (\Throwable $e) {
                // Non-MySQL / insufficient grants — the controller always writes at least ''.
            }
        }

        Schema::table('messages', function (Blueprint $table) {
            try {
                $table->index(['group_id', 'created_at'], 'messages_group_created_idx');
            } catch (\Throwable $e) {
                // index already present
            }
        });
    }

    public function down(): void
    {
        // Safety: do not drop columns in synchronization.
    }
};
