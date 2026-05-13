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
        if (!Schema::hasTable('absent_student')) {
        Schema::create('absent_student', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id'); // Field: teacher_id
            $table->unsignedBigInteger('student_id'); // Field: student_id
            $table->unsignedBigInteger('group_id'); // Field: group_id
            $table->date('days'); // Field: days
            $table->boolean('status'); // Field: status
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
        // Schema::dropIfExists('absent_student'); // Safety: do not drop in synchronization
    }
};
