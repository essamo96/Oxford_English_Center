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
        if (!Schema::hasTable('teacher_evaluate_answer')) {
        Schema::create('teacher_evaluate_answer', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_id'); // Field: question_id
            $table->integer('answer'); // Field: answer
            $table->unsignedBigInteger('evaluate_id'); // Field: evaluate_id
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
        // Schema::dropIfExists('teacher_evaluate_answer'); // Safety: do not drop in synchronization
    }
};
