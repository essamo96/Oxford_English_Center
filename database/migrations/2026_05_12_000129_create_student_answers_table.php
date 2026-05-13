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
        if (!Schema::hasTable('student_answers')) {
        Schema::create('student_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('evaluation_id'); // Field: evaluation_id
            $table->unsignedBigInteger('student_id'); // Field: student_id
            $table->unsignedBigInteger('group_id'); // Field: group_id
            $table->unsignedBigInteger('question_id'); // Field: question_id
            $table->string('answer', 255); // Field: answer
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
        // Schema::dropIfExists('student_answers'); // Safety: do not drop in synchronization
    }
};
