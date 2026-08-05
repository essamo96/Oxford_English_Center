<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * exam_attempt_answers: one row per question the student answered within an attempt.
     * Auto-graded types (mcq/true_false) fill is_correct/marks_awarded immediately; text/voice
     * types are left null until a teacher grades them via graded_by_id/graded_at.
     */
    public function up(): void
    {
        if (!Schema::hasTable('exam_attempt_answers')) {
        Schema::create('exam_attempt_answers', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('attempt_id'); // FK to exam_attempts.id
            $table->unsignedBigInteger('question_id'); // FK to exam_questions.id
            $table->unsignedBigInteger('selected_option_id')->nullable(); // FK to exam_question_options.id, for mcq/true_false answers
            $table->text('answer_text')->nullable(); // Free-text answer for type=text questions
            $table->string('answer_audio_path')->nullable(); // Stored recording path for type=voice questions
            $table->boolean('is_correct')->nullable(); // Null until graded; true/false for auto or manually judged answers
            $table->decimal('marks_awarded', 8, 2)->nullable(); // Marks given for this specific answer
            $table->text('teacher_comment')->nullable(); // Manual review feedback shown to the student when review is enabled
            $table->unsignedBigInteger('graded_by_id')->nullable(); // FK to users.id or teachers.id (see exams.created_by_type) who manually graded this answer
            $table->dateTime('graded_at')->nullable(); // When manual grading occurred
            $table->timestamps();

            $table->foreign('attempt_id')->references('id')->on('exam_attempts')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('exam_questions')->onDelete('cascade');
            $table->foreign('selected_option_id')->references('id')->on('exam_question_options')->onDelete('set null');
            $table->unique(['attempt_id', 'question_id']); // One answer per question per attempt
        });
        }
    }

    public function down(): void
    {
        // Schema::dropIfExists('exam_attempt_answers'); // Safety: do not drop in synchronization
    }
};
