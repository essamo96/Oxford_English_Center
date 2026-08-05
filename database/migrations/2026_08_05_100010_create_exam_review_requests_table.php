<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * exam_review_requests: a student's request to re-examine a graded attempt (e.g. dispute a
     * manually-graded answer). Reviewed by the owning teacher or an admin.
     */
    public function up(): void
    {
        if (!Schema::hasTable('exam_review_requests')) {
        Schema::create('exam_review_requests', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('attempt_id'); // FK to exam_attempts.id being disputed
            $table->unsignedBigInteger('student_id'); // FK to students.id, the requester
            $table->text('message'); // Student's explanation of what they want reviewed
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index(); // Review request state
            $table->text('teacher_comment')->nullable(); // Reviewer's response shown back to the student
            $table->unsignedBigInteger('reviewed_by_id')->nullable(); // FK to users.id or teachers.id who resolved the request
            $table->dateTime('reviewed_at')->nullable(); // When the request was resolved
            $table->timestamps();

            $table->foreign('attempt_id')->references('id')->on('exam_attempts')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
        }
    }

    public function down(): void
    {
        // Schema::dropIfExists('exam_review_requests'); // Safety: do not drop in synchronization
    }
};
