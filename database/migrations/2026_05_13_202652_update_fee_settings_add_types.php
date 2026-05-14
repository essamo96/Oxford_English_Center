<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Changes the 'type' column from a 2-value enum to a string
     * to support more fee types: course, placement_test, registration, materials
     */
    public function up(): void
    {
        // MySQL doesn't easily ALTER ENUM columns, so we convert to string
        DB::statement("ALTER TABLE fee_settings MODIFY COLUMN type VARCHAR(50) NOT NULL DEFAULT 'course'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original 2-value enum (loses any extra values)
        DB::statement("ALTER TABLE fee_settings MODIFY COLUMN type ENUM('course','placement_test') NOT NULL DEFAULT 'course'");
    }
};
