<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * exam_settings: global admin-configurable key/value store for Examination Center settings
     * (default anti-cheat rules, default review settings, notification toggles, etc).
     */
    public function up(): void
    {
        if (!Schema::hasTable('exam_settings')) {
        Schema::create('exam_settings', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('key')->unique(); // Setting identifier, e.g. "default_violation_limit"
            $table->text('value')->nullable(); // Setting value, stored as string/JSON depending on key
            $table->timestamps();
        });
        }
    }

    public function down(): void
    {
        // Schema::dropIfExists('exam_settings'); // Safety: do not drop in synchronization
    }
};
