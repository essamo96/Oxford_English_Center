<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * exam_attempts: one row per student attempt at an exam (placement or group). Holds timing,
     * scoring (auto + manual combined into final_score), and the placement level recommendation.
     */
    public function up(): void
    {
        if (!Schema::hasTable('exam_attempts')) {
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('exam_id'); // FK to exams.id
            $table->unsignedBigInteger('student_id'); // FK to students.id, matches students.id type used across the system
            $table->unsignedTinyInteger('attempt_number')->default(1); // 1-based attempt counter, bounded by exams.max_attempts
            $table->enum('status', ['in_progress', 'submitted', 'graded', 'expired'])->default('in_progress')->index(); // Attempt lifecycle state
            $table->dateTime('started_at')->nullable(); // When the student began the attempt
            $table->dateTime('submitted_at')->nullable(); // When the student (or auto-submit) finished the attempt
            $table->dateTime('expires_at')->nullable(); // started_at + exams.duration_minutes, used to enforce the time limit
            $table->decimal('total_marks', 8, 2)->default(0); // Sum of marks available across the attempt's questions
            $table->decimal('auto_score', 8, 2)->default(0); // Marks awarded automatically (MCQ/True-False)
            $table->decimal('manual_score', 8, 2)->default(0); // Marks awarded manually by a teacher (Text/Voice)
            $table->decimal('final_score', 8, 2)->nullable(); // auto_score + manual_score once grading is complete
            $table->decimal('percentage', 5, 2)->nullable(); // final_score / total_marks * 100, cached for fast reporting
            $table->string('recommended_level')->nullable(); // CEFR level recommended by exam_level_ranges, placement tests only
            $table->unsignedSmallInteger('violations_count')->default(0); // Running count of anti-cheat violations logged for this attempt
            $table->boolean('is_auto_submitted')->default(false); // True if the attempt was force-submitted (time limit or anti-cheat rule)
            $table->string('ip_address', 45)->nullable(); // Student IP at attempt start, kept for anti-cheat auditing
            $table->string('user_agent')->nullable(); // Student browser user-agent at attempt start
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->index(['exam_id', 'student_id']);
        });
        }
    }

    public function down(): void
    {
        // Schema::dropIfExists('exam_attempts'); // Safety: do not drop in synchronization
    }
};
