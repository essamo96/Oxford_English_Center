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
        if (!Schema::hasTable('vedio')) {
        Schema::create('vedio', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200); // Field: title
            $table->string('url', 255); // Field: url
            $table->boolean('status')->default(0); // Field: status
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
        // Schema::dropIfExists('vedio'); // Safety: do not drop in synchronization
    }
};
