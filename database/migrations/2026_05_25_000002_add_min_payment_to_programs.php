<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('programs')) return;
        Schema::table('programs', function (Blueprint $table) {
            if (!Schema::hasColumn('programs', 'min_payment_percent')) {
                $table->decimal('min_payment_percent', 5, 2)->nullable()->after('image');
            }
            if (!Schema::hasColumn('programs', 'min_payment_fixed')) {
                $table->decimal('min_payment_fixed', 10, 2)->nullable()->after('min_payment_percent');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('programs')) return;
        Schema::table('programs', function (Blueprint $table) {
            if (Schema::hasColumn('programs', 'min_payment_percent')) {
                $table->dropColumn('min_payment_percent');
            }
            if (Schema::hasColumn('programs', 'min_payment_fixed')) {
                $table->dropColumn('min_payment_fixed');
            }
        });
    }
};
