<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('messages')) {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->integer('from_user'); // Field: from_user
            $table->string('user_type', 191)->nullable(); // Field: user_type
            $table->unsignedBigInteger('group_id'); // Field: group_id
            $table->text('content'); // Field: content
            $table->timestamps();
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('messages'); // Safety: do not drop in synchronization
    }
};
