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
        if (!Schema::hasTable('roles')) {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191); // Field: name
            $table->string('guard_name', 191)->default('admin'); // Field: guard_name
            $table->boolean('status')->default(1); // Field: status
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
        // Schema::dropIfExists('roles'); // Safety: do not drop in synchronization
    }
};
