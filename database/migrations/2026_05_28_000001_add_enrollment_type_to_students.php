<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('students')) return;
        if (!Schema::hasColumn('students', 'enrollment_type')) {
            Schema::table('students', function (Blueprint $table) {
                // 'test' = Placement Test path, 'course' = Direct Enrollment
                $table->string('enrollment_type', 20)->nullable()->after('program_type');
            });
        }

        // Backfill: any student that already has a placement_tests row is 'test'
        if (Schema::hasTable('placement_tests')) {
            \DB::statement("
                UPDATE students s
                INNER JOIN placement_tests pt ON pt.student_id = s.id
                SET s.enrollment_type = 'test'
                WHERE s.enrollment_type IS NULL
            ");
        }
        // Everyone else who has any fee record is treated as 'course'
        if (Schema::hasTable('group_students_fees')) {
            \DB::statement("
                UPDATE students s
                INNER JOIN group_students_fees gsf ON gsf.student_id = s.id
                SET s.enrollment_type = 'course'
                WHERE s.enrollment_type IS NULL
            ");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('students') && Schema::hasColumn('students', 'enrollment_type')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('enrollment_type');
            });
        }
    }
};
