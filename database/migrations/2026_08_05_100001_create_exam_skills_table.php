<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * exam_skills: lookup table for question/exam skills (Grammar, Vocabulary, Reading, Listening, Writing, Speaking).
     */
    public function up(): void
    {
        if (!Schema::hasTable('exam_skills')) {
        Schema::create('exam_skills', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name_en'); // English name of the skill, e.g. "Grammar"
            $table->string('name_ar'); // Arabic name of the skill, e.g. "قواعد"
            $table->string('slug')->unique(); // Machine-readable key used in code, e.g. "grammar"
            $table->unsignedTinyInteger('status')->default(1); // 1 = active, 0 = inactive
            $table->timestamps();
            $table->softDeletes();
        });
        }
    }

    public function down(): void
    {
        // Schema::dropIfExists('exam_skills'); // Safety: do not drop in synchronization
    }
};
