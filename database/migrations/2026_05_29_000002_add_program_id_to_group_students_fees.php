<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('group_students_fees')) return;
        if (!Schema::hasColumn('group_students_fees', 'program_id')) {
            Schema::table('group_students_fees', function (Blueprint $table) {
                // Stores the program the student picked at registration so the
                // pending-orders modal can pre-select it for the admin.
                $table->unsignedBigInteger('program_id')->nullable()->after('group_id');
                $table->index('program_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('group_students_fees', 'program_id')) {
            Schema::table('group_students_fees', function (Blueprint $table) {
                $table->dropColumn('program_id');
            });
        }
    }
};
