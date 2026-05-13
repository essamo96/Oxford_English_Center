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
        if (!Schema::hasTable('questions')) {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200); // Field: name
            $table->boolean('status')->nullable(); // Field: status
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
        // Schema::dropIfExists('questions'); // Safety: do not drop in synchronization
    }
};
