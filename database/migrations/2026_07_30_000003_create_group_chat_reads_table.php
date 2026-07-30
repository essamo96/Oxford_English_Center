<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tracks how far each admin has read into each group conversation, so the
 * monitor can show a per-group unread badge.
 *
 * Per-admin rather than global: two admins monitoring the same academy each
 * need their own "what's new since I last looked".
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('group_chat_reads')) {
            Schema::create('group_chat_reads', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('group_id');
                $table->unsignedBigInteger('last_read_message_id')->default(0);
                $table->timestamps();

                $table->unique(['user_id', 'group_id'], 'group_chat_reads_user_group_unq');
                $table->index('group_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('group_chat_reads');
    }
};
