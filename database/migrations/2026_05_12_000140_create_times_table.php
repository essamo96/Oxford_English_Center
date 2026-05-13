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
        if (!Schema::hasTable('times')) {
        Schema::create('times', function (Blueprint $table) {
            $table->id();
            $table->string('days', 191); // Field: days
            $table->string('times', 191); // Field: times
            $table->integer('status'); // Field: status
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
        // Schema::dropIfExists('times'); // Safety: do not drop in synchronization
    }
};
