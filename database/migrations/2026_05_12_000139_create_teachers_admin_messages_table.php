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
        if (!Schema::hasTable('teachers_admin_messages')) {
        Schema::create('teachers_admin_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id'); // Field: teacher_id
            $table->string('title', 191); // Field: title
            $table->text('content'); // Field: content
            $table->boolean('seen')->nullable()->default(0); // Field: seen
            $table->timestamps();
            $table->softDeletes();
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('teachers_admin_messages'); // Safety: do not drop in synchronization
    }
};
