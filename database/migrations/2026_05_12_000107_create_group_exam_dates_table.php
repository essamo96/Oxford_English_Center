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
        if (!Schema::hasTable('group_exam_dates')) {
        Schema::create('group_exam_dates', function (Blueprint $table) {
            $table->id();
            $table->date('progress_test1')->nullable(); // Field: progress_test1
            $table->date('progress_test2')->nullable(); // Field: progress_test2
            $table->date('progress_test3')->nullable(); // Field: progress_test3
            $table->date('final_exam')->nullable(); // Field: final_exam
            $table->unsignedBigInteger('group_id'); // Field: group_id
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
        // Schema::dropIfExists('group_exam_dates'); // Safety: do not drop in synchronization
    }
};
