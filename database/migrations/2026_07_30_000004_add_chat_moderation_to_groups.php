<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lets an admin freeze a group's conversation.
 *
 * Locked means read-only for students and teachers: they can still open the chat
 * and read its history, they just cannot post. Admins are never blocked — the
 * whole point is to be able to post the reason the chat was frozen.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            if (!Schema::hasColumn('groups', 'chat_locked')) {
                $table->boolean('chat_locked')->default(0)->after('status');
            }
            if (!Schema::hasColumn('groups', 'chat_locked_at')) {
                $table->timestamp('chat_locked_at')->nullable()->after('chat_locked');
            }
        });
    }

    public function down(): void
    {
        // Safety: do not drop columns in synchronization.
    }
};
