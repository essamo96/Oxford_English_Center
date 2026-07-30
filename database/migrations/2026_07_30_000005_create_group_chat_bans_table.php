<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Per-group posting bans for individual students.
 *
 * A banned student keeps full read access — only sending is refused. The row is
 * kept after a ban is lifted (status 0) rather than deleted, so the ban list
 * doubles as a moderation history: who was banned, why, and when it ended.
 *
 * `reason` is nullable: the admin may ban silently, in which case the student is
 * told they were muted without a stated reason.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('group_chat_bans')) {
            Schema::create('group_chat_bans', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('group_id');
                $table->text('reason')->nullable();
                // 1 = ban in force, 0 = lifted (kept as history)
                $table->boolean('status')->default(1);
                $table->unsignedBigInteger('banned_by')->nullable();
                $table->unsignedBigInteger('unbanned_by')->nullable();
                $table->timestamp('unbanned_at')->nullable();
                $table->timestamps();

                $table->unique(['student_id', 'group_id'], 'group_chat_bans_student_group_unq');
                $table->index(['group_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('group_chat_bans');
    }
};
