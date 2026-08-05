<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds 'fullscreen_exit' to exam_violations.type — the student left the browser's
     * fullscreen mode during the exam (requested via the Fullscreen API when the attempt
     * starts). Raw ALTER TABLE MODIFY is required because MySQL enums can't be extended
     * through Doctrine DBAL's changeColumn().
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE exam_violations MODIFY COLUMN type ENUM('copy', 'paste', 'cut', 'right_click', 'tab_switch', 'window_blur', 'window_focus', 'fullscreen_exit') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE exam_violations MODIFY COLUMN type ENUM('copy', 'paste', 'cut', 'right_click', 'tab_switch', 'window_blur', 'window_focus') NOT NULL");
    }
};
