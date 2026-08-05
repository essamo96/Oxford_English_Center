<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * exam_questions: the reusable Question Bank. Owned globally by Admin, or scoped to a Teacher
     * when created_by_type = 'teacher' (teachers may only use/see their own questions + the global bank).
     */
    public function up(): void
    {
        if (!Schema::hasTable('exam_questions')) {
        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('branch_id')->nullable(); // Owning branch, mirrors BranchScope used across the system
            $table->unsignedBigInteger('program_id')->nullable(); // Optional link to programs.id, question may target a specific program
            $table->unsignedBigInteger('skill_id')->nullable(); // Link to exam_skills.id (Grammar/Vocabulary/Reading/...)
            $table->enum('type', ['mcq', 'true_false', 'text', 'voice']); // Question type: Multiple Choice, True/False, Text Answer, Voice Answer
            $table->enum('difficulty', ['easy', 'medium', 'hard', 'custom'])->default('medium'); // Difficulty level used by the auto-generation engine
            $table->text('question_text'); // The question body/prompt shown to the student
            $table->string('image_path')->nullable(); // Optional supporting image, stored path
            $table->string('audio_path')->nullable(); // Optional supporting audio (e.g. for Listening skill), stored path
            $table->text('explanation')->nullable(); // Shown to the student during review, explains the correct answer
            $table->unsignedInteger('estimated_time_seconds')->default(60); // Estimated time to answer, used for exam duration suggestions
            $table->decimal('marks', 8, 2)->default(1); // Default marks awarded for a correct answer to this question
            $table->json('tags')->nullable(); // Free-form tags for searching/filtering the question bank
            $table->enum('status', ['draft', 'active', 'archived'])->default('active'); // Lifecycle status of the question
            $table->enum('created_by_type', ['admin', 'teacher']); // Who owns this question: 'admin' = global bank, 'teacher' = owned by a single teacher
            $table->unsignedBigInteger('created_by_id'); // FK to users.id when created_by_type=admin, or teachers.id when created_by_type=teacher
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('skill_id')->references('id')->on('exam_skills')->onDelete('set null');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');

            $table->index(['type', 'difficulty']); // Speeds up auto-generation queries filtering by type/difficulty
            $table->index(['created_by_type', 'created_by_id']); // Speeds up "my questions" lookups for teachers
        });
        }
    }

    public function down(): void
    {
        // Schema::dropIfExists('exam_questions'); // Safety: do not drop in synchronization
    }
};
