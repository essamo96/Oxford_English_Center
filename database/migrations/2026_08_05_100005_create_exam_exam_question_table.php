<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * exam_exam_question: pivot linking exams to the specific questions selected for them
     * (either hand-picked in the Exam Builder or written by the auto-generation engine).
     */
    public function up(): void
    {
        if (!Schema::hasTable('exam_exam_question')) {
        Schema::create('exam_exam_question', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('exam_id'); // FK to exams.id
            $table->unsignedBigInteger('question_id'); // FK to exam_questions.id
            $table->unsignedSmallInteger('sort_order')->default(0); // Display order before shuffle_questions is applied
            $table->decimal('marks_override', 8, 2)->nullable(); // Optional per-exam override of exam_questions.marks
            $table->timestamps();

            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('exam_questions')->onDelete('cascade');
            $table->unique(['exam_id', 'question_id']); // A question can only appear once per exam
        });
        }
    }

    public function down(): void
    {
        // Schema::dropIfExists('exam_exam_question'); // Safety: do not drop in synchronization
    }
};
