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
        if (!Schema::hasTable('teacher_library')) {
        Schema::create('teacher_library', function (Blueprint $table) {
            $table->id();
            $table->string('title', 191); // Field: title
            $table->unsignedBigInteger('group_id')->nullable(); // Field: group_id
            $table->unsignedBigInteger('teacher_id')->nullable(); // Field: teacher_id
            $table->text('url')->nullable(); // Field: url
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
        // Schema::dropIfExists('teacher_library'); // Safety: do not drop in synchronization
    }
};
