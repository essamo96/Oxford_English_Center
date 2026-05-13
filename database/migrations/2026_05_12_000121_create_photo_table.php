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
        if (!Schema::hasTable('photo')) {
        Schema::create('photo', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200); // Field: title
            $table->text('descs'); // Field: descs
            $table->string('image', 250)->nullable(); // Field: image
            $table->boolean('status')->default(0); // Field: status
            $table->string('tags', 300)->nullable(); // Field: tags
            $table->unsignedBigInteger('user_id'); // Field: user_id
            $table->integer('updated_by')->default(0); // Field: updated_by
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
        // Schema::dropIfExists('photo'); // Safety: do not drop in synchronization
    }
};
