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
        if (!Schema::hasTable('closed_classes')) {
        Schema::create('closed_classes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id'); // Field: teacher_id
            $table->unsignedBigInteger('group_id'); // Field: group_id
            $table->dateTime('closed_date'); // Field: closed_date
            $table->boolean('seen')->nullable()->default(0); // Field: seen
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
        // Schema::dropIfExists('closed_classes'); // Safety: do not drop in synchronization
    }
};
