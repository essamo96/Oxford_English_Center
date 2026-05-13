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
        if (!Schema::hasTable('group_students_fees')) {
        Schema::create('group_students_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id'); // Field: student_id
            $table->double('student_fee_paid')->default(0); // Field: student_fee_paid
            $table->boolean('student_paid_type'); // Field: student_paid_type
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
        // Schema::dropIfExists('group_students_fees'); // Safety: do not drop in synchronization
    }
};
