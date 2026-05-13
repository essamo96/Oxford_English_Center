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
        if (!Schema::hasTable('programs')) {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('title', 191); // Field: title
            $table->string('image', 191); // Field: image
            $table->text('short'); // Field: short
            $table->integer('exam')->nullable(); // Field: exam
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
        // Schema::dropIfExists('programs'); // Safety: do not drop in synchronization
    }
};
