<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * exams: header/config record for both Placement Tests (category=placement) and Group Exams (category=group).
     * Placement Tests are always created_by_type=admin and have program_id/group_id/teacher_id = null.
     * Group Exams must have program_id + group_id set, and are owned by the group's teacher (or admin).
     */
    public function up(): void
    {
        if (!Schema::hasTable('exams')) {
        Schema::create('exams', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('branch_id')->nullable(); // Owning branch, mirrors BranchScope used across the system
            $table->enum('category', ['placement', 'group'])->index(); // Placement Test (admin-only) vs Group Exam (program/group scoped)
            $table->string('title'); // Exam title shown to students and staff
            $table->text('description')->nullable(); // Optional instructions/description shown before starting the exam
            $table->unsignedBigInteger('program_id')->nullable(); // FK to programs.id, required for category=group, null for placement
            $table->unsignedBigInteger('group_id')->nullable(); // FK to groups.id, required for category=group, null for placement
            $table->unsignedBigInteger('teacher_id')->nullable(); // FK to teachers.id, owning teacher for category=group; null for placement (admin-owned)
            $table->unsignedInteger('duration_minutes')->default(30); // Exam time limit in minutes
            $table->unsignedTinyInteger('max_attempts')->default(1); // Maximum attempts allowed per student
            $table->decimal('passing_score', 5, 2)->default(50); // Minimum percentage score required to pass
            $table->dateTime('start_date')->nullable(); // When the exam becomes available to students
            $table->dateTime('end_date')->nullable(); // When the exam stops accepting new attempts
            $table->enum('status', ['draft', 'scheduled', 'published', 'closed'])->default('draft')->index(); // Exam lifecycle state
            $table->boolean('shuffle_questions')->default(true); // Randomize question order per attempt
            $table->boolean('shuffle_answers')->default(true); // Randomize MCQ option order per attempt
            $table->json('generation_rules')->nullable(); // Auto-generation config, e.g. {"easy":10,"medium":7,"hard":3,"skill_ids":[1,2]}
            $table->boolean('review_available')->default(true); // Whether students may review their answers after finishing
            $table->enum('result_visibility', ['immediate', 'after_review', 'manual'])->default('immediate'); // When the score is revealed to the student
            $table->boolean('anti_cheat_enabled')->default(true); // Toggles lightweight anti-cheat tracking for this exam
            $table->unsignedSmallInteger('anti_cheat_violation_limit')->default(3); // Number of violations tolerated before anti_cheat_action fires
            $table->enum('anti_cheat_action', ['warning', 'notify_teacher', 'auto_submit', 'log'])->default('warning'); // Action taken once the violation limit is exceeded
            $table->enum('created_by_type', ['admin', 'teacher']); // Who owns/created this exam
            $table->unsignedBigInteger('created_by_id'); // FK to users.id when created_by_type=admin, or teachers.id when created_by_type=teacher
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('program_id')->references('id')->on('programs')->onDelete('set null');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
        });
        }
    }

    public function down(): void
    {
        // Schema::dropIfExists('exams'); // Safety: do not drop in synchronization
    }
};
