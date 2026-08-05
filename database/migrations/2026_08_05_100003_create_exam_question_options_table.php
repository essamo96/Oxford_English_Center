<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * exam_question_options: the answer choices for 'mcq' and 'true_false' questions.
     */
    public function up(): void
    {
        if (!Schema::hasTable('exam_question_options')) {
        Schema::create('exam_question_options', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('question_id'); // FK to exam_questions.id, the parent question
            $table->string('option_text'); // The choice text shown to the student (e.g. "True", "Paris")
            $table->boolean('is_correct')->default(false); // Whether this option is a correct answer (supports multi-correct MCQ)
            $table->unsignedSmallInteger('sort_order')->default(0); // Display order of the option before shuffling is applied
            $table->timestamps();

            $table->foreign('question_id')->references('id')->on('exam_questions')->onDelete('cascade');
            $table->index('question_id');
        });
        }
    }

    public function down(): void
    {
        // Schema::dropIfExists('exam_question_options'); // Safety: do not drop in synchronization
    }
};
