<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absent_student', function (Blueprint $table) {
            if (!Schema::hasColumn('absent_student', 'round')) {
                $table->unsignedTinyInteger('round')->default(1)->after('status'); // 1 = first roll-call, 2+ = late round
            }
            if (!Schema::hasColumn('absent_student', 'is_late')) {
                $table->boolean('is_late')->default(false)->after('round');
            }
            if (!Schema::hasColumn('absent_student', 'recorded_at')) {
                $table->dateTime('recorded_at')->nullable()->after('is_late'); // exact moment the row was taken
            }
        });

        Schema::table('absent_teacher', function (Blueprint $table) {
            if (!Schema::hasColumn('absent_teacher', 'lecture_no')) {
                $table->unsignedInteger('lecture_no')->nullable()->after('status'); // running lecture index for the group
            }
            if (!Schema::hasColumn('absent_teacher', 'recorded_at')) {
                $table->dateTime('recorded_at')->nullable()->after('lecture_no');
            }
            if (!Schema::hasColumn('absent_teacher', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('recorded_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('absent_student', function (Blueprint $table) {
            foreach (['round', 'is_late', 'recorded_at'] as $c) {
                if (Schema::hasColumn('absent_student', $c)) $table->dropColumn($c);
            }
        });
        Schema::table('absent_teacher', function (Blueprint $table) {
            foreach (['lecture_no', 'recorded_at', 'ip_address'] as $c) {
                if (Schema::hasColumn('absent_teacher', $c)) $table->dropColumn($c);
            }
        });
    }
};
