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
        if (!Schema::hasTable('permissions')) {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191); // Field: name
            $table->string('guard_name', 191); // Field: guard_name
            $table->string('group_id', 191); // Field: group_id
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
        // Schema::dropIfExists('permissions'); // Safety: do not drop in synchronization
    }
};
