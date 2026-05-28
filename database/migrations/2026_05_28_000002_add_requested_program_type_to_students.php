<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('students')) return;
        if (!Schema::hasColumn('students', 'requested_program_type')) {
            Schema::table('students', function (Blueprint $table) {
                // What the applicant *picked* on the form, before age-routing override.
                // Useful for flagging "tried to register as adult while underage".
                $table->string('requested_program_type', 20)->nullable()->after('program_type');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('students') && Schema::hasColumn('students', 'requested_program_type')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('requested_program_type');
            });
        }
    }
};
