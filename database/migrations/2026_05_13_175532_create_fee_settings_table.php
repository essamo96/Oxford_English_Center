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
        Schema::create('fee_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id');
            $table->string('level_name')->nullable(); // Optional level/group name
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('ILS');
            $table->enum('type', ['course', 'placement_test'])->default('course');
            $table->timestamps();

            $table->foreign('program_id')->references('id')->on('pages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_settings');
    }
};
