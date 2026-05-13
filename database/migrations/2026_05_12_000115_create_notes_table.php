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
        if (!Schema::hasTable('notes')) {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->text('notes')->nullable(); // Field: notes
            $table->integer('total'); // Field: total
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
        // Schema::dropIfExists('notes'); // Safety: do not drop in synchronization
    }
};
