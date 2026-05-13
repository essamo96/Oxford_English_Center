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
        if (!Schema::hasTable('teacher_evaluate_student')) {
        Schema::create('teacher_evaluate_student', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id'); // Field: teacher_id
            $table->unsignedBigInteger('student_id'); // Field: student_id
            $table->unsignedBigInteger('group_id'); // Field: group_id
            $table->integer('total')->nullable(); // Field: total
            $table->text('notes')->nullable(); // Field: notes
            $table->integer('progress')->nullable()->default(0); // Field: progress
            $table->boolean('evaluation_sort')->nullable(); // Field: evaluation_sort
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
        // Schema::dropIfExists('teacher_evaluate_student'); // Safety: do not drop in synchronization
    }
};
