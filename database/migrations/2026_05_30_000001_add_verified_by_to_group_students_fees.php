<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('group_students_fees')) return;
        if (!Schema::hasColumn('group_students_fees', 'verified_by')) {
            Schema::table('group_students_fees', function (Blueprint $table) {
                // Admin (users.id) who confirmed/recorded this financial transaction.
                $table->unsignedBigInteger('verified_by')->nullable()->after('audit_status');
                $table->index('verified_by');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('group_students_fees', 'verified_by')) {
            Schema::table('group_students_fees', function (Blueprint $table) {
                $table->dropColumn('verified_by');
            });
        }
    }
};
