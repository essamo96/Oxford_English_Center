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
        if (!Schema::hasTable('evaluate_items')) {
        Schema::create('evaluate_items', function (Blueprint $table) {
            $table->id();
            $table->string('name_en', 200); // Field: name_en
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
        // Schema::dropIfExists('evaluate_items'); // Safety: do not drop in synchronization
    }
};
