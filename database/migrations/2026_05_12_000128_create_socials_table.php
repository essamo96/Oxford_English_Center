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
        if (!Schema::hasTable('socials')) {
        Schema::create('socials', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191)->nullable(); // Field: name
            $table->string('link', 191)->nullable(); // Field: link
            $table->string('icon', 191)->nullable(); // Field: icon
            $table->integer('status')->nullable(); // Field: status
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
        // Schema::dropIfExists('socials'); // Safety: do not drop in synchronization
    }
};
