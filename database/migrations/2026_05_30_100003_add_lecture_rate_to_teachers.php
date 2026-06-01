<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('teachers', 'lecture_rate')) {
            Schema::table('teachers', function (Blueprint $table) {
                // Flat amount paid to the teacher per delivered lecture
                $table->decimal('lecture_rate', 10, 2)->default(0)->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('teachers', 'lecture_rate')) {
            Schema::table('teachers', function (Blueprint $table) {
                $table->dropColumn('lecture_rate');
            });
        }
    }
};
