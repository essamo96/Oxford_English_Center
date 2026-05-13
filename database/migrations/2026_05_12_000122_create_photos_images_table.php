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
        if (!Schema::hasTable('photos_images')) {
        Schema::create('photos_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('album_id'); // Field: album_id
            $table->string('image', 300); // Field: image
            $table->integer('feature')->default(0); // Field: feature
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
        // Schema::dropIfExists('photos_images'); // Safety: do not drop in synchronization
    }
};
