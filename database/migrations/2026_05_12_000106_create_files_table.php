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
        if (!Schema::hasTable('files')) {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200); // Field: title
            $table->text('descs'); // Field: descs
            $table->unsignedBigInteger('program_id'); // Field: program_id
            $table->string('image', 250); // Field: image
            $table->boolean('status')->default(0); // Field: status
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
        // Schema::dropIfExists('files'); // Safety: do not drop in synchronization
    }
};
