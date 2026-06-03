<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('programs', 'is_placement_test_default')) {
            Schema::table('programs', function (Blueprint $table) {
                // The single program used by default as the "Placement Test" program in registration.
                $table->boolean('is_placement_test_default')->default(false)->after('exam');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('programs', 'is_placement_test_default')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->dropColumn('is_placement_test_default');
            });
        }
    }
};
