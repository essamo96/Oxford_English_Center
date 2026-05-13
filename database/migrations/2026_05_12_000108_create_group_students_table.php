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
        if (!Schema::hasTable('group_students')) {
        Schema::create('group_students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id'); // Field: student_id
            $table->double('student_fee_total')->default(0); // Field: student_fee_total
            $table->double('student_book_total')->default(0); // Field: student_book_total
            $table->unsignedBigInteger('group_id'); // Field: group_id
            $table->double('exam1_degree')->nullable(); // Field: exam1_degree
            $table->double('exam2_degree')->nullable(); // Field: exam2_degree
            $table->double('exam3_degree')->nullable(); // Field: exam3_degree
            $table->double('exam4_degree')->nullable(); // Field: exam4_degree
            $table->double('activity_degree')->nullable(); // Field: activity_degree
            $table->double('workbook_degree')->nullable(); // Field: workbook_degree
            $table->double('total_degree')->nullable(); // Field: total_degree
            $table->integer('has_evaluation')->nullable()->default(0); // Field: has_evaluation
            $table->dateTime('evaluation_at')->nullable(); // Field: evaluation_at
            $table->integer('progress')->nullable()->default(0); // Field: progress
            $table->string('cer_code', 255)->nullable(); // Field: cer_code
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
        // Schema::dropIfExists('group_students'); // Safety: do not drop in synchronization
    }
};
