<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('times') && !Schema::hasColumn('times', 'is_placement_test')) {
            Schema::table('times', function (Blueprint $table) {
                $table->boolean('is_placement_test')->default(false)->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('times') && Schema::hasColumn('times', 'is_placement_test')) {
            Schema::table('times', function (Blueprint $table) {
                $table->dropColumn('is_placement_test');
            });
        }
    }
};
