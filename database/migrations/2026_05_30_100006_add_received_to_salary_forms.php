<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('teacher_salary_forms')) return;
        Schema::table('teacher_salary_forms', function (Blueprint $table) {
            if (!Schema::hasColumn('teacher_salary_forms', 'is_received')) {
                $table->boolean('is_received')->default(false)->after('status'); // teacher actually received the salary?
            }
            if (!Schema::hasColumn('teacher_salary_forms', 'received_at')) {
                $table->dateTime('received_at')->nullable()->after('is_received');
            }
            if (!Schema::hasColumn('teacher_salary_forms', 'received_by')) {
                $table->unsignedBigInteger('received_by')->nullable()->after('received_at'); // admin who recorded the disbursement
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('teacher_salary_forms')) return;
        Schema::table('teacher_salary_forms', function (Blueprint $table) {
            foreach (['is_received', 'received_at', 'received_by'] as $c) {
                if (Schema::hasColumn('teacher_salary_forms', $c)) $table->dropColumn($c);
            }
        });
    }
};
