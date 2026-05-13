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
        if (!Schema::hasTable('categories')) {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // Field: name
            $table->unsignedBigInteger('category_id'); // Field: category_id
            $table->integer('sort')->default(1); // Field: sort
            $table->string('tags', 255); // Field: tags
            $table->boolean('status')->default(0); // Field: status
            $table->string('color', 300); // Field: color
            $table->integer('in_menu'); // Field: in_menu
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
        // Schema::dropIfExists('categories'); // Safety: do not drop in synchronization
    }
};
