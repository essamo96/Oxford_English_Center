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
        if (!Schema::hasTable('permissions_group')) {
        Schema::create('permissions_group', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191); // Field: name
            $table->string('name_ar', 255)->nullable(); // Field: name_ar
            $table->string('name_en', 255)->nullable(); // Field: name_en
            $table->text('icon')->nullable(); // Field: icon
            $table->integer('sort')->nullable(); // Field: sort
            $table->boolean('status')->nullable()->default(1); // Field: status
            $table->unsignedBigInteger('parent_id')->default(0); // Field: parent_id
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
        // Schema::dropIfExists('permissions_group'); // Safety: do not drop in synchronization
    }
};
