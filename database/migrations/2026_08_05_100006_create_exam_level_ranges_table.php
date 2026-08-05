<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * exam_level_ranges: global admin-configured score ranges used to auto-recommend a CEFR
     * level (A1-C1) once a Placement Test attempt is graded.
     */
    public function up(): void
    {
        if (!Schema::hasTable('exam_level_ranges')) {
        Schema::create('exam_level_ranges', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('level'); // CEFR level code, e.g. A1, A2, B1, B2, C1
            $table->decimal('min_score', 5, 2); // Minimum percentage score (inclusive) mapped to this level
            $table->decimal('max_score', 5, 2); // Maximum percentage score (inclusive) mapped to this level
            $table->unsignedTinyInteger('status')->default(1); // 1 = active, 0 = inactive
            $table->timestamps();
        });
        }
    }

    public function down(): void
    {
        // Schema::dropIfExists('exam_level_ranges'); // Safety: do not drop in synchronization
    }
};
