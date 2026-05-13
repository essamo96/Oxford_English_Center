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
        if (!Schema::hasTable('teacher_evaluate_academy')) {
        Schema::create('teacher_evaluate_academy', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('evaluate_item_id'); // Field: evaluate_item_id
            $table->unsignedBigInteger('class_id'); // Field: class_id
            $table->unsignedBigInteger('teacher_id'); // Field: teacher_id
            $table->integer('value'); // Field: value
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
        // Schema::dropIfExists('teacher_evaluate_academy'); // Safety: do not drop in synchronization
    }
};
